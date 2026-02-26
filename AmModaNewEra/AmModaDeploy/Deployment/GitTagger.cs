using System;
using System.IO;
using System.Threading;
using System.Threading.Tasks;
using AmModaDeploy;

namespace AmModaDeploy.Deployment;

public class GitTagger
{
    private readonly ProcessRunner _processRunner;

    public GitTagger(ProcessRunner processRunner)
    {
        _processRunner = processRunner;
    }

    public async Task CreateAndPushTagAsync(string repositoryPath, string tagPrefix, CancellationToken cancellationToken)
    {
        var tagName = $"{tagPrefix}-{DateTime.Now:yyyy-MM-dd-HH-mm}";
        
        ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Creating git tag '{tagName}'...");
        await _processRunner.RunAsync("git", $"tag -f {tagName}", repositoryPath, cancellationToken).ConfigureAwait(false);
        
        ConsoleColors.WriteLine(ConsoleColors.Cyan, $"Pushing git tag '{tagName}' to origin...");
        await _processRunner.RunAsync("git", $"push origin {tagName} --force", repositoryPath, cancellationToken).ConfigureAwait(false);
        
        ConsoleColors.WriteLine(ConsoleColors.Green, $"Git tag '{tagName}' created and pushed successfully.");
    }

    public static string? ResolveGitRepositoryPath(string? configuredPath)
    {
        if (!string.IsNullOrWhiteSpace(configuredPath))
        {
            var fullPath = Path.GetFullPath(configuredPath);
            if (Directory.Exists(Path.Combine(fullPath, ".git")))
            {
                return fullPath;
            }

            ConsoleColors.WriteLine(ConsoleColors.Dim, $"Configured git repository path '{fullPath}' does not contain a .git directory.");
            return null;
        }

        var searchRoot = new DirectoryInfo(AppContext.BaseDirectory);
        for (var i = 0; i < 8 && searchRoot is not null; i++)
        {
            if (Directory.Exists(Path.Combine(searchRoot.FullName, ".git")))
            {
                return searchRoot.FullName;
            }

            searchRoot = searchRoot.Parent;
        }

        return null;
    }
}
