using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace AmModaDeploy
{
    internal class GitStatistics
    {
        internal static async Task ShowGitStatisticsAsync()
        {
            var authors = await GetAuthorsAsync();
            Console.WriteLine();
            ConsoleColors.WriteLine(ConsoleColors.Cyan, "Git Statistics:\n");
            ConsoleColors.WriteLine(ConsoleColors.Cyan, string.Format("{0,-25} {1,10} {2,15} {3,15} {4,15} {5,15} {6,40}",
                "Author", "Commits", "Lines +", "Lines -", "Chars +", "Chars -", "Favorite file"));

            foreach (var author in authors)
            {
                int commits = await CountCommitsAsync(author);
                (int added, int deleted) = await CountLinesChangedAsync(author);

                (int charsAdded, int charsRemoved) = await CountCharactersChangedAsync(author);
                string favoriteFile = await GetFavoriteFileAsync(author);

                Console.WriteLine("{0,-25} {1,10} {2,15} {3,15} {4,15} {5,15} {6,40}",
                    author, commits, added, deleted, charsAdded, charsRemoved, favoriteFile);
            }
        }

        private static async Task<string> GetFavoriteFileAsync(string author)
        {
            var output = await RunGitCommandAsync($"log --author=\"{author}\" --pretty=format:%H --all");
            var commitHashes = output.Split('\n').Select(h => h.Trim()).Where(h => !string.IsNullOrWhiteSpace(h));

            var fileStats = new Dictionary<string, (int added, int deleted)>(StringComparer.OrdinalIgnoreCase);

            foreach (var commit in commitHashes)
            {
                var diffOutput = await RunGitCommandAsync($"show --pretty=format: --numstat {commit}");

                foreach (var line in diffOutput.Split('\n'))
                {
                    var parts = line.Split('\t');
                    if (parts.Length == 3 &&
                        int.TryParse(parts[0], out int added) &&
                        int.TryParse(parts[1], out int deleted))
                    {
                        string filePath = parts[2].Trim();
                        string fileName = Path.GetFileName(filePath);

                        if (string.IsNullOrWhiteSpace(fileName))
                            continue;

                        if (fileName.ToLower().Contains("generated") ||
                            fileName.ToLower().Contains("examplebookstoadd") ||
                            fileName.ToLower().Contains("topbooksinitial") ||
                            fileName.ToLower().Contains("designer") ||
                            fileName.ToLower().Contains("databasecontextmodelsnapshot"))
                            continue;

                        if (!fileStats.ContainsKey(fileName))
                            fileStats[fileName] = (0, 0);

                        var current = fileStats[fileName];
                        fileStats[fileName] = (current.added + added, current.deleted + deleted);
                    }
                }
            }

            if (fileStats.Count == 0)
                return "-";

            var topFile = fileStats
                .OrderByDescending(kv => kv.Value.added + kv.Value.deleted)
                .First();

            return $"{topFile.Key} ({topFile.Value.added}/{topFile.Value.deleted})";
        }


        private static async Task<(int addedChars, int removedChars)> CountCharactersChangedAsync(string author)
        {
            var output = await RunGitCommandAsync($"log --author=\"{author}\" --pretty=format:%H --all");

            int added = 0;
            int removed = 0;

            foreach (var line in output.Split('\n'))
            {
                var commitHash = line.Trim();
                if (string.IsNullOrWhiteSpace(commitHash)) continue;

                var diff = await RunGitCommandAsync($"show {commitHash} --unified=0 --no-color");

                foreach (var diffLine in diff.Split('\n'))
                {
                    if (diffLine.StartsWith("+") && !diffLine.StartsWith("+++"))
                        added += diffLine.Substring(1).Length;
                    else if (diffLine.StartsWith("-") && !diffLine.StartsWith("---"))
                        removed += diffLine.Substring(1).Length;
                }
            }

            return (added, removed);
        }


        private static async Task<List<string>> GetAuthorsAsync()
        {
            var output = await RunGitCommandAsync("log --pretty='%aN'");
            return output
                .Split('\n')
                .Select(line => line.Trim('\'', '\r', '\n'))
                .Where(line => !string.IsNullOrWhiteSpace(line))
                .Distinct()
                .ToList();
        }

        private static async Task<int> CountCommitsAsync(string author)
        {
            var output = await RunGitCommandAsync($"log --author=\"{author}\" --pretty=oneline --all");
            return output.Split('\n').Count(line => !string.IsNullOrWhiteSpace(line));
        }

        private static async Task<(int added, int deleted)> CountLinesChangedAsync(string author)
        {
            var output = await RunGitCommandAsync($"log --author=\"{author}\" --pretty=format:%H --all");
            var hashes = output.Split('\n').Select(h => h.Trim()).Where(h => !string.IsNullOrWhiteSpace(h));

            int added = 0;
            int deleted = 0;

            foreach (var hash in hashes)
            {
                var diffOutput = await RunGitCommandAsync($"show --pretty=format: --numstat {hash}");
                foreach (var line in diffOutput.Split('\n'))
                {
                    var parts = line.Split('\t');
                    if (parts.Length == 3 &&
                        int.TryParse(parts[0], out int a) &&
                        int.TryParse(parts[1], out int d))
                    {
                        added += a;
                        deleted += d;
                    }
                }
            }

            return (added, deleted);
        }


        private static async Task<string> RunGitCommandAsync(string arguments)
        {
            var psi = new ProcessStartInfo
            {
                FileName = "git",
                Arguments = arguments,
                RedirectStandardOutput = true,
                RedirectStandardError = true,
                UseShellExecute = false,
                CreateNoWindow = true,
                StandardOutputEncoding = Encoding.UTF8
            };

            var process = new Process { StartInfo = psi };

            var outputBuilder = new StringBuilder();

            process.OutputDataReceived += (sender, e) =>
            {
                if (e.Data != null)
                    outputBuilder.AppendLine(e.Data);
            };

            process.Start();
            process.BeginOutputReadLine();

            await process.WaitForExitAsync();

            return outputBuilder.ToString();
        }
    }
}
