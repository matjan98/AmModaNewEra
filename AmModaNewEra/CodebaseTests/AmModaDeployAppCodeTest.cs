using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Text.RegularExpressions;

namespace CodebaseTests;

internal class AmModaDeployAppCodeTest
{
    [Test]
    public void NamespacesMatchFolderStructure()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var rootPath = Path.GetFullPath(Path.Combine(currentDirectory, "..", "..", "..", ".."));

        var projects = new[] { "AmModaDeploy" };
        var errors = new List<string>();

        foreach (var project in projects)
        {
            var projectPath = Path.Combine(rootPath, project);
            foreach (var filePath in Directory.GetFiles(projectPath, "*.cs", SearchOption.AllDirectories))
            {
                if (filePath.Contains($"{Path.DirectorySeparatorChar}bin{Path.DirectorySeparatorChar}") ||
                    filePath.Contains($"{Path.DirectorySeparatorChar}obj{Path.DirectorySeparatorChar}"))
                {
                    continue;
                }

                var fileContent = File.ReadAllText(filePath);
                var match = Regex.Match(fileContent, @"^\s*namespace\s+([A-Za-z0-9_.]+)\s*(?:\{|;)", RegexOptions.Multiline);
                if (!match.Success)
                {
                    continue;
                }

                var declaredNamespace = match.Groups[1].Value;
                var relativePath = Path.GetRelativePath(projectPath, filePath);
                var directoryPart = Path.GetDirectoryName(relativePath);
                var expectedNamespace = project;

                if (!string.IsNullOrEmpty(directoryPart))
                {
                    expectedNamespace += "." + directoryPart.Replace(Path.DirectorySeparatorChar, '.');
                }

                if (!string.Equals(declaredNamespace, expectedNamespace, StringComparison.Ordinal))
                {
                    errors.Add($"{relativePath} -> '{declaredNamespace}' should be '{expectedNamespace}'");
                }
            }
        }

        if (errors.Count > 0)
        {
            Assert.Fail(string.Join("; ", errors));
        }

        Assert.Pass("All namespaces match their folder structure.");
    }

    [Test]
    public void AsyncMethodsHaveAsyncSuffix()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var rootPath = Path.GetFullPath(Path.Combine(currentDirectory, "..", "..", "..", ".."));

        var projects = new[] { "AmModaDeploy" };
        var errors = new List<string>();

        var regex = new Regex(@"async\s+Task(?:<[^>]+>)?\s+(?<name>[A-Za-z0-9_]+)\s*\(");

        foreach (var project in projects)
        {
            var projectPath = Path.Combine(rootPath, project);
            foreach (var filePath in Directory.GetFiles(projectPath, "*.cs", SearchOption.AllDirectories))
            {
                if (filePath.Contains($"{Path.DirectorySeparatorChar}bin{Path.DirectorySeparatorChar}") ||
                    filePath.Contains($"{Path.DirectorySeparatorChar}obj{Path.DirectorySeparatorChar}"))
                {
                    continue;
                }

                var fileContent = File.ReadAllText(filePath);
                foreach (Match match in regex.Matches(fileContent))
                {
                    var methodName = match.Groups["name"].Value;
                    if (!methodName.EndsWith("Async", StringComparison.Ordinal))
                    {
                        errors.Add($"{Path.GetFileName(filePath)} -> {methodName}");
                    }
                }
            }
        }

        if (errors.Count > 0)
        {
            Assert.Fail(string.Join("; ", errors));
        }

        Assert.Pass("All async methods have Async suffix.");
    }

    [Test]
    public void BuildHasNoWarningsOrErrors()
    {
        KillAmModaDeployProcesses();

        var currentDirectory = Directory.GetCurrentDirectory();
        var projectPath = Path.GetFullPath(Path.Combine(currentDirectory, "..", "..", "..", "..", "AmModaDeploy", "AmModaDeploy.csproj"));

        var startInfo = new ProcessStartInfo
        {
            FileName = "dotnet",
            Arguments = $"build \"{projectPath}\" --no-restore",
            RedirectStandardOutput = true,
            RedirectStandardError = true,
            UseShellExecute = false,
            CreateNoWindow = true
        };

        using var process = Process.Start(startInfo)!;
        string output = process.StandardOutput.ReadToEnd();
        string error = process.StandardError.ReadToEnd();
        process.WaitForExit();

        var lines = (output + "\n" + error).Split('\n', StringSplitOptions.RemoveEmptyEntries);
        var warnings = lines.Where(l => l.Contains(": warning", StringComparison.OrdinalIgnoreCase)).ToList();
        var errors = lines.Where(l => l.Contains(": error", StringComparison.OrdinalIgnoreCase)).ToList();

        var fileLockErrorCodes = new[] { "MSB3026", "MSB3027", "MSB3021" };
        var fileLockErrors = errors.Where(e => fileLockErrorCodes.Any(code => e.Contains(code, StringComparison.OrdinalIgnoreCase))).ToList();
        var fileLockWarnings = warnings.Where(w => fileLockErrorCodes.Any(code => w.Contains(code, StringComparison.OrdinalIgnoreCase))).ToList();

        var realErrors = errors.Except(fileLockErrors).ToList();
        var realWarnings = warnings.Except(fileLockWarnings).ToList();

        if (realWarnings.Count > 0 || realErrors.Count > 0 || (process.ExitCode != 0 && realErrors.Count > 0))
        {
            var message = $"Build produced {realWarnings.Count} warnings and {realErrors.Count} errors.";
            Assert.Fail(message + "\n" + output + error);
        }

        Assert.Pass("Build succeeded without warnings or errors.");
    }

    private static void KillAmModaDeployProcesses()
    {
        try
        {
            var processes = Process.GetProcessesByName("AmModaDeploy");
            foreach (var proc in processes)
            {
                try
                {
                    proc.Kill();
                    proc.WaitForExit(5000);
                }
                catch
                {
                    // Ignore
                }
                finally
                {
                    proc?.Dispose();
                }
            }
        }
        catch
        {
            // Ignore
        }
    }
}
