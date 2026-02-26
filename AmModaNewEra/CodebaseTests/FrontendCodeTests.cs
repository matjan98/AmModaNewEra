using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text.RegularExpressions;

namespace CodebaseTests;

public class FrontendCodeTests
{
    private static string ResolveFrontendSrcPath()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        return Path.GetFullPath(Path.Combine(currentDirectory, "..", "..", "..", "..", "WebSiteFrontend", "src"));
    }

    [Test]
    public void FrontendDebuggerLeftoversCheck()
    {
        var rootPath = ResolveFrontendSrcPath();
        var filePatterns = new[] { "*.js", "*.vue" };
        var debuggerRegex = new Regex(@"\bdebugger;", RegexOptions.Compiled);

        foreach (var pattern in filePatterns)
        {
            foreach (var filePath in Directory.GetFiles(rootPath, pattern, SearchOption.AllDirectories))
            {
                var fileContent = File.ReadAllText(filePath);
                if (debuggerRegex.IsMatch(fileContent))
                {
                    Assert.Fail($"Debugger statement found in file: {filePath}");
                }
            }
        }

        Assert.Pass("No debugger statements found.");
    }

    [Test]
    public void FrontendCssClassesCheck()
    {
        var rootPath = ResolveFrontendSrcPath();
        var filePatterns = new[] { "*.vue" };
        var allowedPrefixes = new HashSet<string>(StringComparer.OrdinalIgnoreCase)
        {
            "q-",
            "flex",
            "col",
            "row",
            "text-",
            "items-",
            "justify-",
            "index-page-"
        };

        List<string> errors = new();

        foreach (var pattern in filePatterns)
        {
            foreach (var filePath in Directory.GetFiles(rootPath, pattern, SearchOption.AllDirectories))
            {
                var fileContent = File.ReadAllText(filePath);

                var fileNameWithoutExtension = Path.GetFileNameWithoutExtension(filePath);
                var expectedClassPrefix = ConvertToKebabCase(fileNameWithoutExtension);

                var styleMatch = Regex.Match(fileContent, "<style[^>]*>(.*?)</style>", RegexOptions.Singleline);
                if (!styleMatch.Success)
                {
                    continue;
                }

                var styleContent = styleMatch.Groups[1].Value;
                var matches = Regex.Matches(styleContent, "(?<![a-zA-Z0-9])\\.([a-zA-Z0-9\\-]+)");
                var classList = matches.Cast<Match>().Select(m => m.Groups[1].Value).ToList();

                for (int i = 0; i < classList.Count; i++)
                {
                    var className = classList[i];
                    if (allowedPrefixes.Any(prefix => className.StartsWith(prefix, StringComparison.OrdinalIgnoreCase)))
                    {
                        continue;
                    }

                    if (className is "body--dark" or "body--light")
                    {
                        if (i + 1 < classList.Count && classList[i + 1].StartsWith(expectedClassPrefix, StringComparison.OrdinalIgnoreCase))
                        {
                            i++;
                            continue;
                        }
                    }

                    if (!className.StartsWith(expectedClassPrefix, StringComparison.OrdinalIgnoreCase))
                    {
                        errors.Add($"Invalid CSS class naming in file {filePath}. Found class '{className}' that does not start with '{expectedClassPrefix}'.");
                    }
                }
            }
        }

        if (errors.Count > 0)
        {
            Assert.Fail(string.Join(",\n", errors));
        }

        Assert.Pass("All CSS classes are valid in terms of naming policy.");

        static string ConvertToKebabCase(string input)
        {
            return Regex.Replace(input, @"([a-z])([A-Z])", "$1-$2").ToLowerInvariant().Replace("_", "-").Replace(" ", "-");
        }
    }

    [Test]
    public void FrontendCssClassesUsageCheck()
    {
        var rootPath = ResolveFrontendSrcPath();
        var filePatterns = new[] { "*.vue" };

        List<string> errors = new();

        var transitionSuffixes = new[] { "enter-active", "leave-active", "enter-from", "enter-to", "leave-from", "leave-to" };

        foreach (var pattern in filePatterns)
        {
            foreach (var filePath in Directory.GetFiles(rootPath, pattern, SearchOption.AllDirectories))
            {
                var fileContent = File.ReadAllText(filePath);

                var stylePattern = @"<style[^>]*>(.*?)<\/style>";
                var styleMatch = Regex.Match(fileContent, stylePattern, RegexOptions.Singleline);

                if (!styleMatch.Success)
                {
                    continue;
                }

                var transitionNamePattern = @"<transition\s+name=[""']([^""']+)[""']";
                var transitionMatches = Regex.Matches(fileContent, transitionNamePattern, RegexOptions.IgnoreCase);
                var transitionNames = transitionMatches.Cast<Match>()
                    .Select(m => m.Groups[1].Value)
                    .ToHashSet(StringComparer.OrdinalIgnoreCase);

                var styleContent = styleMatch.Groups[1].Value;
                var classPattern = @"(?<![a-zA-Z0-9])\.([a-zA-Z0-9\-]+)";
                var matches = Regex.Matches(styleContent, classPattern);

                foreach (Match match in matches)
                {
                    var className = match.Groups[1].Value;

                    if (className is "body--dark" or "body--light")
                    {
                        continue;
                    }

                    if (className.StartsWith("q-", StringComparison.OrdinalIgnoreCase))
                    {
                        continue;
                    }

                    var isTransitionClass = false;
                    foreach (var transitionName in transitionNames)
                    {
                        foreach (var suffix in transitionSuffixes)
                        {
                            if (className.Equals($"{transitionName}-{suffix}", StringComparison.OrdinalIgnoreCase))
                            {
                                isTransitionClass = true;
                                break;
                            }
                        }
                        if (isTransitionClass) break;
                    }

                    if (isTransitionClass)
                    {
                        continue;
                    }

                    var usagePattern = @$"\b{Regex.Escape(className)}\b";
                    var fileContentWithoutStyle = Regex.Replace(fileContent, stylePattern, string.Empty, RegexOptions.Singleline);
                    var usageMatch = Regex.IsMatch(fileContentWithoutStyle, usagePattern, RegexOptions.Singleline);

                    if (!usageMatch)
                    {
                        errors.Add($"Unused CSS class '{className}' in file {filePath}.");
                    }
                }
            }
        }

        if (errors.Count > 0)
        {
            Assert.Fail(string.Join(",\n", errors));
        }

        Assert.Pass("All CSS classes are used correctly.");
    }

    [Test]
    public void FrontendEmptyElementsWithClosingTagCheck()
    {
        var rootPath = ResolveFrontendSrcPath();
        var filePatterns = new[] { "*.vue" };

        List<string> errors = new();

        foreach (var pattern in filePatterns)
        {
            foreach (var filePath in Directory.GetFiles(rootPath, pattern, SearchOption.AllDirectories))
            {
                var fileContent = File.ReadAllText(filePath);

                var emptyTagPattern = @"<([a-zA-Z0-9\-]+)([^>]*)></\1>";
                var matches = Regex.Matches(fileContent, emptyTagPattern, RegexOptions.Singleline);

                foreach (Match match in matches)
                {
                    var fullTag = match.Value;
                    var tagName = match.Groups[1].Value;

                    if (string.Equals(tagName, "template", StringComparison.OrdinalIgnoreCase))
                    {
                        continue;
                    }

                    var attributes = match.Groups[2].Value.Trim();
                    var correctedTag = attributes.Length > 0
                        ? $"<{tagName} {attributes} />"
                        : $"<{tagName} />";

                    errors.Add($"Incorrectly closed empty tag in file {filePath}: {fullTag}. Suggested fix: {correctedTag}");
                }
            }
        }

        if (errors.Count > 0)
        {
            Assert.Fail(string.Join("\n", errors));
        }

        Assert.Pass("No incorrectly closed empty tags found.");
    }

    [Test]
    public void FrontendImgTagsAltAttributeCheck()
    {
        var rootPath = ResolveFrontendSrcPath();
        var filePatterns = new[] { "*.vue" };

        List<string> errors = new();

        foreach (var pattern in filePatterns)
        {
            foreach (var filePath in Directory.GetFiles(rootPath, pattern, SearchOption.AllDirectories))
            {
                var fileContent = File.ReadAllText(filePath);
                var imgPattern = @"<(img|q-img)([^>]*)>";
                var imgMatches = Regex.Matches(fileContent, imgPattern, RegexOptions.Singleline);

                foreach (Match match in imgMatches)
                {
                    var tag = match.Value;
                    var altPattern = @"\salt\s*=\s*[""'].*?[""']";
                    var altMatch = Regex.IsMatch(tag, altPattern);

                    if (!altMatch)
                    {
                        errors.Add($"Missing 'alt' attribute in file {filePath} for tag: {tag.Trim()}");
                    }
                }
            }
        }

        if (errors.Count > 0)
        {
            Assert.Fail(string.Join(",\n", errors));
        }

        Assert.Pass("All <img> and <q-img> tags contain 'alt' attributes.");
    }

    [Test]
    public void CheckIfAllImportsAreUsed()
    {
        var rootPath = ResolveFrontendSrcPath();
        var filePatterns = new[] { "*.vue", "*.js" };
        var importRegex = new Regex("""import\s+(\{[^}]+\}|\*?\s*[^\s,]+(?:\s+as\s+[^\s,]+)?(?:\s*,\s*\{[^}]+\})?)\s+from\s+['"]([^'""]+)['"]""", RegexOptions.Compiled);

        List<string> errors = new();

        foreach (var pattern in filePatterns)
        {
            foreach (var filePath in Directory.GetFiles(rootPath, pattern, SearchOption.AllDirectories))
            {
                var fileContent = File.ReadAllText(filePath);
                var matches = importRegex.Matches(fileContent);
                var importedItems = new List<string>();

                foreach (Match match in matches)
                {
                    var itemsGroup = match.Groups[1].Value;
                    var parts = itemsGroup
                        .Replace("{", string.Empty)
                        .Replace("}", string.Empty)
                        .Split(',', StringSplitOptions.RemoveEmptyEntries)
                        .Select(x => x.Trim())
                        .ToList();

                    importedItems.AddRange(parts);
                }

                foreach (var item in importedItems)
                {
                    if (string.IsNullOrWhiteSpace(item) || item.StartsWith("*", StringComparison.Ordinal))
                    {
                        continue;
                    }

                    var cleanItem = item.Contains(" as ", StringComparison.OrdinalIgnoreCase)
                        ? item.Split(" as ", StringSplitOptions.RemoveEmptyEntries | StringSplitOptions.TrimEntries).Last()
                        : item;

                    var usagePattern = new Regex($@"\b{Regex.Escape(cleanItem)}\b", RegexOptions.Compiled);
                    var matchesInFile = usagePattern.Matches(fileContent).Count;

                    if (matchesInFile == 1)
                    {
                        errors.Add($"Unused import '{cleanItem}' found in file {filePath}");
                    }
                }
            }
        }

        if (errors.Count > 0)
        {
            Assert.Fail(string.Join("\n", errors));
        }

        Assert.Pass("All imports are used correctly.");
    }
}
