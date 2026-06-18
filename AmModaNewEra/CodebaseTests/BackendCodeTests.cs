using System;
using System.IO;
using System.Linq;
using NUnit.Framework;

namespace CodebaseTests;

public class BackendCodeTests
{
    private static string ResolveServerPath()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        return Path.GetFullPath(Path.Combine(currentDirectory, "..", "..", "..", "..", "Server"));
    }

    [Test]
    public void AdminAuthCredentialsArePresentInPhp()
    {
        var authPath = Path.Combine(ResolveServerPath(), "lib", "Auth.php");
        Assert.That(File.Exists(authPath), Is.True, $"Missing file: {authPath}");

        var content = File.ReadAllText(authPath);
        Assert.That(content, Does.Contain("ADMIN_USERNAME"));
        Assert.That(content, Does.Contain("ADMIN_PASSWORD"));
        Assert.That(content, Does.Contain("'admin'"));
    }

    [Test]
    public void AdminApiEndpointsExist()
    {
        var serverPath = ResolveServerPath();
        var requiredFiles = new[]
        {
            "api/auth.php",
            "api/settings.php",
            "api/admin/settings.php",
            "lib/SiteSettingsRepository.php",
            "lib/OpeningHoursRepository.php",
            "lib/Cors.php",
        };

        foreach (var relativePath in requiredFiles)
        {
            var fullPath = Path.Combine(serverPath, relativePath);
            Assert.That(File.Exists(fullPath), Is.True, $"Missing file: {fullPath}");
        }
    }

    [Test]
    public void DatabaseBootstrapCreatesSiteTables()
    {
        var databasePath = Path.Combine(ResolveServerPath(), "lib", "Database.php");
        var content = File.ReadAllText(databasePath);

        Assert.That(content, Does.Contain("site_settings"));
        Assert.That(content, Does.Contain("opening_hours_weekly"));
        Assert.That(content, Does.Contain("opening_hours_overrides"));
    }

    [Test]
    public void SpaFallbackHtaccessExists()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var htaccessPath = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            "public",
            ".htaccess"));

        Assert.That(File.Exists(htaccessPath), Is.True, $"Missing file: {htaccessPath}");

        var content = File.ReadAllText(htaccessPath);
        Assert.That(content, Does.Contain("RewriteEngine On"));
        Assert.That(content, Does.Contain("index.html"));
    }

    [Test]
    public void StaticProductGalleryWasRemovedFromFrontend()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var galleryPanelPath = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            "src",
            "components",
            "IndexPageGalleryPanel.vue"));

        var content = File.ReadAllText(galleryPanelPath);
        Assert.That(content, Does.Not.Contain("productPhotos"));
        Assert.That(content, Does.Not.Contain("assets/gallery"));
        Assert.That(content, Does.Not.Contain("__gallery--products"));
    }

    [Test]
    public void CommittedEnvMustNotContainLocalhostApiBase()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var envPath = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            ".env"));

        Assert.That(File.Exists(envPath), Is.True, $"Missing file: {envPath}");

        var content = File.ReadAllText(envPath);
        Assert.That(content, Does.Not.Contain("localhost"),
            "Committed .env must not set VITE_API_BASE to localhost — use .env.development for local dev.");
    }
}
