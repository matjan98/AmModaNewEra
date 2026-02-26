using System;
using System.Diagnostics;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using AmModaDeploy;

namespace AmModaDeploy.Deployment;

public class ProcessRunner
{
    public async Task RunAsync(string fileName, string arguments, string workingDirectory, CancellationToken cancellationToken)
    {
        var commandArguments = string.IsNullOrWhiteSpace(arguments)
            ? string.Empty
            : $" {arguments}";

        var processStartInfo = new ProcessStartInfo
        {
            FileName = "cmd.exe",
            Arguments = $"/c chcp 65001 >nul & {fileName}{commandArguments}",
            WorkingDirectory = workingDirectory,
            RedirectStandardError = true,
            RedirectStandardOutput = true,
            StandardErrorEncoding = Encoding.UTF8,
            StandardOutputEncoding = Encoding.UTF8,
            UseShellExecute = false,
            CreateNoWindow = true
        };

        using var process = new Process
        {
            StartInfo = processStartInfo,
            EnableRaisingEvents = true
        };

        var completionSource = new TaskCompletionSource<int>(TaskCreationOptions.RunContinuationsAsynchronously);

        process.OutputDataReceived += (_, args) =>
        {
            if (args.Data is not null)
            {
                Console.WriteLine(args.Data);
            }
        };

        process.ErrorDataReceived += (_, args) =>
        {
            if (args.Data is not null)
            {
                ConsoleColors.WriteLine(ConsoleColors.Red, args.Data);
            }
        };

        process.Exited += (_, _) => completionSource.TrySetResult(process.ExitCode);

        if (!process.Start())
        {
            throw new InvalidOperationException($"Failed to start process {fileName} {arguments}.");
        }

        process.BeginOutputReadLine();
        process.BeginErrorReadLine();

        using var cancellationRegistration = cancellationToken.Register(() =>
        {
            if (!process.HasExited)
            {
                try
                {
                    process.Kill(entireProcessTree: true);
                }
                catch
                {
                    // ignore kill exceptions
                }
            }
        });

        var exitCode = await completionSource.Task.ConfigureAwait(false);

        cancellationToken.ThrowIfCancellationRequested();

        if (exitCode != 0)
        {
            throw new InvalidOperationException($"Process '{fileName} {arguments}' exited with code {exitCode}.");
        }
    }
}
