using System;
using System.IO;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;
using AmModaDeploy;

namespace AmModaDeploy.Deployment;

public class DeploymentService
{
    private readonly DeploymentConfiguration _configuration;
    private readonly ProcessRunner _processRunner;
    private readonly FtpUploader _ftpUploader;
    private readonly GitTagger _gitTagger;

    public DeploymentService(DeploymentConfiguration configuration, ProcessRunner processRunner, FtpUploader ftpUploader)
    {
        _configuration = configuration;
        _processRunner = processRunner;
        _ftpUploader = ftpUploader;
        _gitTagger = new GitTagger(processRunner);
    }

    public async Task RunAsync()
    {
        var validationErrors = _configuration.Validate().ToList();
        if (validationErrors.Any())
        {
            ConsoleColors.WriteLine(ConsoleColors.Red, "Configuration errors:");
            foreach (var error in validationErrors)
            {
                ConsoleColors.WriteLine(ConsoleColors.Red, $"- {error}");
            }

            throw new InvalidOperationException("Deployment configuration is invalid.");
        }

        using var cancellationTokenSource = new CancellationTokenSource();
        ConsoleCancelEventHandler? handler = (_, eventArgs) =>
        {
            eventArgs.Cancel = true;
            cancellationTokenSource.Cancel();
        };

        Console.CancelKeyPress += handler;

        try
        {
            var cancellationToken = cancellationTokenSource.Token;

            // Frontend deployment
            var frontendPath = ResolveFrontendPath();
            var buildOutputPath = ResolveBuildOutputPath(frontendPath);

            ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Frontend path: {frontendPath}");
            ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Build output path: {buildOutputPath}");

            await BuildFrontendAsync(frontendPath, cancellationToken).ConfigureAwait(false);

            if (!Directory.Exists(buildOutputPath))
            {
                throw new DirectoryNotFoundException($"Build output path '{buildOutputPath}' does not exist after build.");
            }

            var backendPath = ResolveBackendPath();
            if (backendPath != null)
            {
                ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Backend path: {backendPath}");
                CopyBackendIntoBuildOutput(backendPath, buildOutputPath);
            }

            ReplaceFavicon(buildOutputPath, frontendPath);

            ConsoleColors.WriteLine(ConsoleColors.Yellow, "Uploading frontend files...");
            await _ftpUploader.UploadDirectoryAsync(buildOutputPath, _configuration, cancellationToken).ConfigureAwait(false);

            if (_configuration.CreateGitTagOnDeploy)
            {
                await CreateGitTagAsync(cancellationToken).ConfigureAwait(false);
            }

            ConsoleColors.WriteLine(ConsoleColors.Green, "Deployment completed successfully.");
        }
        finally
        {
            Console.CancelKeyPress -= handler;
        }
    }

    private void CopyBackendIntoBuildOutput(string backendPath, string buildOutputPath)
    {
        var backendDestination = CombineWithRelativePath(buildOutputPath, _configuration.RemoteBackendPath ?? "server");

        ConsoleColors.WriteLine(ConsoleColors.Yellow, $"Copying backend files to '{backendDestination}'...");

        if (Directory.Exists(backendDestination))
        {
            Directory.Delete(backendDestination, recursive: true);
        }

        CopyDirectory(backendPath, backendDestination);

        var smtpConfigPath = Path.Combine(backendDestination, "config", "smtp.php");
        if (File.Exists(smtpConfigPath) && !string.IsNullOrWhiteSpace(_configuration.SmtpPassword))
        {
            ConsoleColors.WriteLine(ConsoleColors.Cyan, "Updating SMTP configuration in build output...");
            var smtpConfig = File.ReadAllText(smtpConfigPath);

            var newSmtpConfig = smtpConfig.Replace(
                "'password' => env_or_default('SMTP_PASSWORD', null),",
                $"'password' => env_or_default('SMTP_PASSWORD', '{_configuration.SmtpPassword}'),"
            );

            File.WriteAllText(smtpConfigPath, newSmtpConfig);
        }
        else if (File.Exists(smtpConfigPath))
        {
            ConsoleColors.WriteLine(ConsoleColors.Dim, $"SMTP config present; Deployment:SmtpPassword not set – leaving smtp.php unchanged.");
        }
    }

    private void ReplaceFavicon(string buildOutputPath, string frontendPath)
    {
        var sourceFavicon = Path.Combine(frontendPath, "public", "ammoda-logo.ico");
        if (!File.Exists(sourceFavicon))
        {
            ConsoleColors.WriteLine(ConsoleColors.Dim, $"Custom favicon not found at '{sourceFavicon}'. Skipping replacement.");
            return;
        }

        var destinationFavicon = Path.Combine(buildOutputPath, "favicon.ico");
        var destinationDirectory = Path.GetDirectoryName(destinationFavicon);
        if (!string.IsNullOrEmpty(destinationDirectory))
        {
            Directory.CreateDirectory(destinationDirectory);
        }

        ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Replacing favicon with '{sourceFavicon}'.");
        File.Copy(sourceFavicon, destinationFavicon, overwrite: true);
    }

    private static void CopyDirectory(string sourceDirectory, string destinationDirectory)
    {
        if (!Directory.Exists(sourceDirectory))
        {
            throw new DirectoryNotFoundException($"Source directory '{sourceDirectory}' does not exist.");
        }

        Directory.CreateDirectory(destinationDirectory);

        foreach (var directory in Directory.GetDirectories(sourceDirectory, "*", SearchOption.AllDirectories))
        {
            var relativePath = Path.GetRelativePath(sourceDirectory, directory);
            var targetDirectory = Path.Combine(destinationDirectory, relativePath);
            Directory.CreateDirectory(targetDirectory);
        }

        foreach (var file in Directory.GetFiles(sourceDirectory, "*", SearchOption.AllDirectories))
        {
            var relativePath = Path.GetRelativePath(sourceDirectory, file);
            var targetFile = Path.Combine(destinationDirectory, relativePath);
            var targetFileDirectory = Path.GetDirectoryName(targetFile);
            if (!string.IsNullOrEmpty(targetFileDirectory))
            {
                Directory.CreateDirectory(targetFileDirectory);
            }

            File.Copy(file, targetFile, overwrite: true);
        }
    }

    private static string CombineWithRelativePath(string basePath, string relativePath)
    {
        if (string.IsNullOrWhiteSpace(relativePath))
        {
            return basePath;
        }

        var segments = relativePath.Split(new[] { '/', '\\' }, StringSplitOptions.RemoveEmptyEntries);
        return Path.Combine(new[] { basePath }.Concat(segments).ToArray());
    }

    public async Task TestConnectionAsync()
    {
        var validationErrors = _configuration.Validate().ToList();
        if (validationErrors.Any())
        {
            ConsoleColors.WriteLine(ConsoleColors.Red, "Configuration errors:");
            foreach (var error in validationErrors)
            {
                ConsoleColors.WriteLine(ConsoleColors.Red, $"- {error}");
            }

            return;
        }

        using var cancellationTokenSource = new CancellationTokenSource();
        ConsoleCancelEventHandler? handler = (_, eventArgs) =>
        {
            eventArgs.Cancel = true;
            cancellationTokenSource.Cancel();
        };

        Console.CancelKeyPress += handler;

        try
        {
            ConsoleColors.WriteLine(ConsoleColors.Yellow, "Testing FTP connection...");
            await _ftpUploader.TestConnectionAsync(_configuration, cancellationTokenSource.Token).ConfigureAwait(false);
            ConsoleColors.WriteLine(ConsoleColors.Green, "Connection test completed successfully.");
        }
        catch (Exception ex)
        {
            ConsoleColors.WriteLine(ConsoleColors.Red, $"Connection test failed: {ex.Message}");
        }
        finally
        {
            Console.CancelKeyPress -= handler;
        }
    }

    private async Task BuildFrontendAsync(string frontendPath, CancellationToken cancellationToken)
    {
        ConsoleColors.WriteLine(ConsoleColors.Yellow, "Building frontend (npx quasar build)...");
        await _processRunner.RunAsync("npx", "quasar build", frontendPath, cancellationToken).ConfigureAwait(false);
    }

    private async Task CreateGitTagAsync(CancellationToken cancellationToken)
    {
        var repositoryPath = GitTagger.ResolveGitRepositoryPath(_configuration.GitRepositoryPath);
        if (repositoryPath is null)
        {
            ConsoleColors.WriteLine(ConsoleColors.Dim, "Git repository not found. Skipping tag creation.");
            return;
        }

        ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Git repository path: {repositoryPath}");
        await _gitTagger.CreateAndPushTagAsync(repositoryPath, _configuration.GitTagPrefix, cancellationToken).ConfigureAwait(false);
    }

    private string ResolveFrontendPath()
    {
        if (!string.IsNullOrWhiteSpace(_configuration.FrontendPath))
        {
            var configuredPath = Path.GetFullPath(_configuration.FrontendPath);
            if (!Directory.Exists(configuredPath))
            {
                throw new DirectoryNotFoundException($"Configured frontend path '{configuredPath}' does not exist.");
            }

            return configuredPath;
        }

        var searchRoot = new DirectoryInfo(AppContext.BaseDirectory);
        for (var i = 0; i < 6 && searchRoot is not null; i++)
        {
            var candidate = Path.Combine(searchRoot.FullName, "WebSiteFrontend");
            if (Directory.Exists(candidate))
            {
                return candidate;
            }

            searchRoot = searchRoot.Parent;
        }

        throw new DirectoryNotFoundException("Unable to locate WebSiteFrontend directory. Configure Deployment:FrontendPath secret.");
    }

    private string ResolveBuildOutputPath(string frontendPath)
    {
        var subPath = string.IsNullOrWhiteSpace(_configuration.BuildOutputSubPath)
            ? "dist/spa"
            : _configuration.BuildOutputSubPath;

        return Path.GetFullPath(Path.Combine(frontendPath, subPath));
    }

    private string? ResolveBackendPath()
    {
        if (!string.IsNullOrWhiteSpace(_configuration.BackendPath))
        {
            var configuredPath = Path.GetFullPath(_configuration.BackendPath);
            if (!Directory.Exists(configuredPath))
            {
                throw new DirectoryNotFoundException($"Configured backend path '{configuredPath}' does not exist.");
            }

            return configuredPath;
        }

        var searchRoot = new DirectoryInfo(AppContext.BaseDirectory);
        for (var i = 0; i < 6 && searchRoot is not null; i++)
        {
            var candidate = Path.Combine(searchRoot.FullName, "Server");
            if (Directory.Exists(candidate))
            {
                return candidate;
            }

            searchRoot = searchRoot.Parent;
        }

        // Backend is optional - if not found, return null
        return null;
    }
}
