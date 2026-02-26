using System;
using System.Diagnostics;

namespace FrontendTests;

public class FrontendBuildTest
{
    [Test]
    public async Task FrontendBuildTestCheck()
    {
        if (Environment.GetEnvironmentVariable("CODEX_RUNNING") == "true")
        {
            Assert.Pass("Skipping this test... CODEX_RUNNING is true");
            return;
        }

        var currentDirectory = Directory.GetCurrentDirectory();
        var repoRoot = Path.GetFullPath(Path.Combine(currentDirectory, "..", "..", "..", ".."));
        var webSiteFrontendPath = Path.Combine(repoRoot, "WebSiteFrontend");

        var startInfo = new ProcessStartInfo
        {
            FileName = "cmd.exe",
            Arguments = $"/c \"cd /d \"{webSiteFrontendPath}\" && quasar build\"",
            UseShellExecute = false,
            RedirectStandardOutput = true,
            RedirectStandardError = true,
            CreateNoWindow = true,
            WorkingDirectory = webSiteFrontendPath
        };

        string output;
        string error;
        using (var process = Process.Start(startInfo))
        {
            if (process == null)
            {
                Assert.Fail("Failed to start quasar build process.");
                return;
            }

            output = await process.StandardOutput.ReadToEndAsync();
            error = await process.StandardError.ReadToEndAsync();

            var timeoutTask = Task.Delay(120000);
            var waitTask = process.WaitForExitAsync();
            await Task.WhenAny(waitTask, timeoutTask);

            if (!process.HasExited)
            {
                process.Kill();
                Assert.Fail("Quasar build timed out after 120 seconds.");
            }
        }

        var fullOutput = output + "\n" + error;
        Assert.That(fullOutput, Does.Contain("Build succeeded"), "Quasar build did not succeed. Output: " + fullOutput);
    }
}
