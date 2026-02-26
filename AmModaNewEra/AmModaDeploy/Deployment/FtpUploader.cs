using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.Threading;
using System.Threading.Tasks;
using AmModaDeploy;
using FluentFTP;
using FluentFTP.Exceptions;

namespace AmModaDeploy.Deployment;

public class FtpUploader
{
    public async Task TestConnectionAsync(DeploymentConfiguration configuration, CancellationToken cancellationToken)
    {
        await using var client = CreateClient(configuration);

        try
        {
            await client.Connect(cancellationToken).ConfigureAwait(false);

            await client.GetListing("/", cancellationToken).ConfigureAwait(false);

            var remoteBasePath = NormalizeRemotePath(configuration.RemoteBasePath);
            if (!string.IsNullOrEmpty(remoteBasePath))
            {
                var exists = await client.DirectoryExists(ToAbsoluteRemotePath(remoteBasePath), cancellationToken).ConfigureAwait(false);
                if (!exists)
                {
                    throw new InvalidOperationException($"Remote directory '{remoteBasePath}' does not exist or is not accessible.");
                }
            }
        }
        catch (Exception ex) when (ex is FtpException or IOException or SocketException)
        {
            throw new InvalidOperationException($"FTP connection failed: {ex.Message}", ex);
        }
        finally
        {
            if (client.IsConnected)
            {
                try
                {
                    await client.Disconnect(cancellationToken).ConfigureAwait(false);
                }
                catch
                {
                    // ignore disconnect errors
                }
            }
        }
    }

    public async Task UploadDirectoryAsync(string localDirectory, DeploymentConfiguration configuration, CancellationToken cancellationToken)
    {
        if (!Directory.Exists(localDirectory))
        {
            throw new DirectoryNotFoundException($"Local directory '{localDirectory}' does not exist.");
        }

        await using var client = CreateClient(configuration);

        try
        {
            await client.Connect(cancellationToken).ConfigureAwait(false);

            var remoteBasePath = NormalizeRemotePath(configuration.RemoteBasePath);
            if (!string.IsNullOrEmpty(remoteBasePath))
            {
                await EnsureRemoteDirectoryExistsAsync(client, remoteBasePath, cancellationToken).ConfigureAwait(false);
            }

            await UploadDirectoryInternalAsync(client, localDirectory, remoteBasePath, cancellationToken).ConfigureAwait(false);

            await RemoveMissingRemoteEntriesAsync(client, localDirectory, remoteBasePath, cancellationToken).ConfigureAwait(false);
        }
        finally
        {
            if (client.IsConnected)
            {
                try
                {
                    await client.Disconnect(cancellationToken).ConfigureAwait(false);
                }
                catch
                {
                    // ignore disconnect errors
                }
            }
        }
    }

    private async Task UploadDirectoryInternalAsync(AsyncFtpClient client, string localDirectory, string remoteDirectory, CancellationToken cancellationToken)
    {
        foreach (var directory in Directory.GetDirectories(localDirectory))
        {
            var directoryName = Path.GetFileName(directory);
            if (string.IsNullOrWhiteSpace(directoryName))
            {
                continue;
            }

            var remoteChildPath = CombineRemotePath(remoteDirectory, directoryName);
            await EnsureRemoteDirectoryExistsAsync(client, remoteChildPath, cancellationToken).ConfigureAwait(false);
            await UploadDirectoryInternalAsync(client, directory, remoteChildPath, cancellationToken).ConfigureAwait(false);
        }

        foreach (var file in Directory.GetFiles(localDirectory))
        {
            var fileName = Path.GetFileName(file);
            if (string.IsNullOrWhiteSpace(fileName))
            {
                continue;
            }

            var remoteFilePath = CombineRemotePath(remoteDirectory, fileName);
            await UploadFileAsync(client, file, remoteFilePath, cancellationToken).ConfigureAwait(false);
        }
    }

    private async Task RemoveMissingRemoteEntriesAsync(AsyncFtpClient client, string localDirectory, string remoteDirectory, CancellationToken cancellationToken)
    {
        var remoteAbsolutePath = ToAbsoluteRemotePath(remoteDirectory);
        var remoteListing = await client.GetListing(remoteAbsolutePath, FtpListOption.Recursive, cancellationToken).ConfigureAwait(false);

        var localFiles = Directory
            .GetFiles(localDirectory, "*", SearchOption.AllDirectories)
            .Select(path => NormalizeRelativePath(Path.GetRelativePath(localDirectory, path)))
            .ToHashSet(StringComparer.OrdinalIgnoreCase);

        var localDirectories = Directory
            .GetDirectories(localDirectory, "*", SearchOption.AllDirectories)
            .Select(path => NormalizeRelativePath(Path.GetRelativePath(localDirectory, path)))
            .ToHashSet(StringComparer.OrdinalIgnoreCase);

        foreach (var item in remoteListing.Where(listItem => listItem.Type == FtpObjectType.File))
        {
            var relativePath = GetRelativeRemotePath(item, remoteDirectory);
            if (string.IsNullOrEmpty(relativePath))
            {
                continue;
            }

            if (localFiles.Contains(relativePath))
            {
                continue;
            }

            if (ShouldSkipDeletion(relativePath))
            {
                ConsoleColors.WriteLine(ConsoleColors.Dim, $"Skipping remote file {item.FullName} (retention)");
                continue;
            }

            ConsoleColors.WriteLine(ConsoleColors.Yellow, $"Deleting remote file {item.FullName}");
            await client.DeleteFile(item.FullName, cancellationToken).ConfigureAwait(false);
        }

        var directoriesToDelete = remoteListing
            .Where(listItem => listItem.Type == FtpObjectType.Directory)
            .Select(item => new
            {
                Item = item,
                RelativePath = GetRelativeRemotePath(item, remoteDirectory)
            })
            .Where(x => !string.IsNullOrEmpty(x.RelativePath) && !localDirectories.Contains(x.RelativePath))
            .OrderByDescending(x => x.RelativePath.Length)
            .Select(x => x.Item)
            .ToList();

        foreach (var directory in directoriesToDelete)
        {
            if (ShouldSkipDeletion(GetRelativeRemotePath(directory, remoteDirectory)))
            {
                ConsoleColors.WriteLine(ConsoleColors.Dim, $"Skipping remote directory {directory.FullName} (retention)");
                continue;
            }

            ConsoleColors.WriteLine(ConsoleColors.Yellow, $"Deleting remote directory {directory.FullName}");
            await client.DeleteDirectory(directory.FullName, cancellationToken).ConfigureAwait(false);
        }
    }

    private async Task EnsureRemoteDirectoryExistsAsync(AsyncFtpClient client, string remoteDirectory, CancellationToken cancellationToken)
    {
        if (string.IsNullOrEmpty(remoteDirectory))
        {
            return;
        }

        var absolutePath = ToAbsoluteRemotePath(remoteDirectory);
        if (await client.DirectoryExists(absolutePath, cancellationToken).ConfigureAwait(false))
        {
            return;
        }

        ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Creating remote directory {absolutePath}");
        await client.CreateDirectory(absolutePath, true, cancellationToken).ConfigureAwait(false);
    }

    private static async Task UploadFileAsync(AsyncFtpClient client, string localFilePath, string remotePath, CancellationToken cancellationToken)
    {
        var absolutePath = ToAbsoluteRemotePath(remotePath);
        ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Uploading {localFilePath} -> {absolutePath}");

        await client.UploadFile(localFilePath, absolutePath, FtpRemoteExists.Overwrite, true, FtpVerify.None, null, cancellationToken).ConfigureAwait(false);
    }

    private static string GetRelativeRemotePath(FtpListItem item, string remoteBasePath)
    {
        var itemPath = NormalizeRemotePath(item.FullName);
        var normalizedBase = NormalizeRemotePath(remoteBasePath);

        if (string.IsNullOrEmpty(normalizedBase))
        {
            return itemPath;
        }

        if (itemPath.Equals(normalizedBase, StringComparison.OrdinalIgnoreCase))
        {
            return string.Empty;
        }

        var prefix = normalizedBase + "/";
        return itemPath.StartsWith(prefix, StringComparison.OrdinalIgnoreCase)
            ? itemPath[prefix.Length..]
            : itemPath;
    }

    private AsyncFtpClient CreateClient(DeploymentConfiguration configuration)
    {
        var host = configuration.FtpHost ?? throw new InvalidOperationException("FTP host is not configured.");
        var client = new AsyncFtpClient(host)
        {
            Port = configuration.FtpPort,
            Credentials = new NetworkCredential(configuration.FtpUsername, configuration.FtpPassword)
        };

        client.Config.DataConnectionType = configuration.UsePassiveMode ? FtpDataConnectionType.AutoPassive : FtpDataConnectionType.AutoActive;
        client.Config.EncryptionMode = FtpEncryptionMode.None;
        return client;
    }

    private static string CombineRemotePath(string basePath, string appended)
    {
        if (string.IsNullOrEmpty(basePath))
        {
            return NormalizeRemotePath(appended);
        }

        if (string.IsNullOrEmpty(appended))
        {
            return NormalizeRemotePath(basePath);
        }

        return NormalizeRemotePath($"{TrimTrailingSlash(basePath)}/{TrimLeadingSlash(appended)}");
    }

    private static string NormalizeRemotePath(string? path)
    {
        if (string.IsNullOrWhiteSpace(path))
        {
            return string.Empty;
        }

        return TrimLeadingSlash(TrimTrailingSlash(path.Replace("\\", "/")));
    }

    private static string NormalizeRelativePath(string relativePath)
    {
        return relativePath.Replace("\\", "/");
    }

    private static string ToAbsoluteRemotePath(string? path)
    {
        var normalized = NormalizeRemotePath(path);
        return string.IsNullOrEmpty(normalized) ? "/" : $"/{normalized}";
    }

    private static string TrimLeadingSlash(string path)
    {
        return path.TrimStart('/');
    }

    private static string TrimTrailingSlash(string path)
    {
        return path.TrimEnd('/');
    }

    private static bool ShouldSkipDeletion(string relativePath)
    {
        var normalizedPath = NormalizeRelativePath(relativePath);
        if (string.IsNullOrEmpty(normalizedPath))
        {
            return false;
        }

        var segments = normalizedPath
            .Split('/', StringSplitOptions.RemoveEmptyEntries);

        if (segments.Any(segment => segment.Equals("logs", StringComparison.OrdinalIgnoreCase)))
        {
            return true;
        }

        if (segments.Any(segment => segment.Equals("photos", StringComparison.OrdinalIgnoreCase)))
        {
            return true;
        }

        return normalizedPath.EndsWith(".log", StringComparison.OrdinalIgnoreCase);
    }
}
