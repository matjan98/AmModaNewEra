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
            "api/admin/reviews.php",
            "lib/SiteSettingsRepository.php",
            "lib/OpeningHoursRepository.php",
            "lib/ReviewsSync.php",
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
    public void LegacyMainPhotoEndpointWasRemovedFromBackend()
    {
        var photoPath = Path.Combine(ResolveServerPath(), "api", "photo.php");
        Assert.That(File.Exists(photoPath), Is.True, $"Missing file: {photoPath}");

        var content = File.ReadAllText(photoPath);
        Assert.That(content, Does.Not.Contain("main.*"));
        Assert.That(content, Does.Not.Contain("hasPhoto"));
    }

    [Test]
    public void PhotoListEndpointDisablesHttpCache()
    {
        var photoPath = Path.Combine(ResolveServerPath(), "api", "photo.php");
        Assert.That(File.Exists(photoPath), Is.True, $"Missing file: {photoPath}");

        var content = File.ReadAllText(photoPath);
        Assert.That(content, Does.Contain("no-store"));
        Assert.That(content, Does.Contain("no-cache"));
    }

    [Test]
    public void ServerApiHtaccessDisablesExpiresDefault()
    {
        var path = Path.Combine(ResolveServerPath(), "api", ".htaccess");
        Assert.That(File.Exists(path), Is.True, $"Missing file: {path}");

        var content = File.ReadAllText(path);
        Assert.That(content, Does.Contain("ExpiresActive Off"));
    }

    [Test]
    public void ApiJsonClientUsesNoStoreFetchCache()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var utilPath = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            "src",
            "utils",
            "apiJson.js"));

        Assert.That(File.Exists(utilPath), Is.True, $"Missing file: {utilPath}");

        var content = File.ReadAllText(utilPath);
        Assert.That(content, Does.Contain("cache: 'no-store'"));
    }

    [Test]
    public void GalleryPhotosComposableSupportsReload()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var path = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            "src",
            "composables",
            "useGalleryPhotos.js"));

        Assert.That(File.Exists(path), Is.True, $"Missing file: {path}");

        var content = File.ReadAllText(path);
        Assert.That(content, Does.Contain("reloadGalleryPhotos"));
        Assert.That(content, Does.Contain("Date.now()"));
    }

    [Test]
    public void PhotoDeleteEndpointSupportsBulkIds()
    {
        var deletePath = Path.Combine(ResolveServerPath(), "api", "delete.php");
        Assert.That(File.Exists(deletePath), Is.True, $"Missing file: {deletePath}");

        var content = File.ReadAllText(deletePath);
        Assert.That(content, Does.Contain("ids"));
        Assert.That(content, Does.Contain("'deleted'"));
        Assert.That(content, Does.Contain("php://input"));
        Assert.That(content, Does.Contain("'reason'"));
        Assert.That(content, Does.Contain("is_writable"));
    }

    [Test]
    public void GalleryPermissionsRepairEndpointExists()
    {
        var path = Path.Combine(ResolveServerPath(), "api", "admin", "gallery-permissions.php");
        Assert.That(File.Exists(path), Is.True, $"Missing file: {path}");

        var content = File.ReadAllText(path);
        Assert.That(content, Does.Contain("requireAuthenticated"));
        Assert.That(content, Does.Contain("still_unwritable"));
        Assert.That(content, Does.Contain("chmod"));
    }

    [Test]
    public void GalleryBatchUploadUtilExists()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var utilPath = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            "src",
            "utils",
            "galleryBatchUpload.js"));

        Assert.That(File.Exists(utilPath), Is.True, $"Missing file: {utilPath}");

        var content = File.ReadAllText(utilPath);
        Assert.That(content, Does.Contain("GALLERY_UPLOAD_BATCH_SIZE = 10"));
        Assert.That(content, Does.Contain("uploadPhotosInBatches"));
        Assert.That(content, Does.Contain("rollbackSessionUploads"));
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

    [Test]
    public void ApiUrlUsesRootRelativePathInProduction()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var apiUrlPath = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            "src",
            "utils",
            "apiUrl.js"));

        var content = File.ReadAllText(apiUrlPath);
        Assert.That(content, Does.Contain("return `/${DEFAULT_API_SUBPATH}/${normalizedPath}`"),
            "Default API URL must start with / so nested routes like /admin/login resolve correctly.");
    }

    [Test]
    public void NewsTextPreservesLineBreaksInQuickInfo()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var quickInfoPath = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            "src",
            "components",
            "indexPage",
            "IndexPageQuickInfo.vue"));

        var content = File.ReadAllText(quickInfoPath);
        Assert.That(content, Does.Contain("index-page-quick-info__news-text"));
        Assert.That(content, Does.Contain("white-space: pre-line"),
            "News text must use pre-line so textarea line breaks render on the public site.");
    }

    [Test]
    public void PhotoReorderEndpointExists()
    {
        var serverPath = ResolveServerPath();

        var reorderPath = Path.Combine(serverPath, "api", "reorder.php");
        Assert.That(File.Exists(reorderPath), Is.True, $"Missing file: {reorderPath}");

        var reorderContent = File.ReadAllText(reorderPath);
        Assert.That(reorderContent, Does.Contain("GalleryOrder"));
        Assert.That(reorderContent, Does.Contain("requireAuthenticated"));

        var libPath = Path.Combine(serverPath, "lib", "GalleryOrder.php");
        Assert.That(File.Exists(libPath), Is.True, $"Missing file: {libPath}");

        var photoContent = File.ReadAllText(Path.Combine(serverPath, "api", "photo.php"));
        Assert.That(photoContent, Does.Contain("GalleryOrder"));
    }

    [Test]
    public void ImageProcessorLibraryExists()
    {
        var processorPath = Path.Combine(ResolveServerPath(), "lib", "ImageProcessor.php");
        Assert.That(File.Exists(processorPath), Is.True, $"Missing file: {processorPath}");

        var content = File.ReadAllText(processorPath);
        Assert.That(content, Does.Contain("class ImageProcessor"));
        Assert.That(content, Does.Contain("webp"));
        Assert.That(content, Does.Contain("MAX_LONG_EDGE"));
        Assert.That(content, Does.Contain("MAX_SHORT_EDGE"));
    }

    [Test]
    public void UploadConvertsImagesToWebp()
    {
        var uploadPath = Path.Combine(ResolveServerPath(), "api", "upload.php");
        var content = File.ReadAllText(uploadPath);

        Assert.That(content, Does.Contain("ImageProcessor"));
        Assert.That(content, Does.Contain(".webp"));
        Assert.That(content, Does.Not.Contain("move_uploaded_file"),
            "Upload must process images via ImageProcessor instead of moving the raw file.");
    }

    [Test]
    public void UploadAcceptsBmpInput()
    {
        var uploadPath = Path.Combine(ResolveServerPath(), "api", "upload.php");
        var content = File.ReadAllText(uploadPath);
        Assert.That(content, Does.Contain("image/bmp"));
    }

    [Test]
    public void AdminGalleryAcceptsBmpUploads()
    {
        var currentDirectory = Directory.GetCurrentDirectory();
        var pagePath = Path.GetFullPath(Path.Combine(
            currentDirectory,
            "..",
            "..",
            "..",
            "..",
            "WebSiteFrontend",
            "src",
            "pages",
            "admin",
            "AdminDashboardPage.vue"));

        var content = File.ReadAllText(pagePath);
        Assert.That(content, Does.Contain("image/bmp"));
    }

    [Test]
    public void GoogleReviewsAutoSyncFlagIsWired()
    {
        var serverPath = ResolveServerPath();

        var databaseContent = File.ReadAllText(Path.Combine(serverPath, "lib", "Database.php"));
        Assert.That(databaseContent, Does.Contain("google_reviews_auto_sync"),
            "site_settings must define the google_reviews_auto_sync column.");

        var settingsRepoContent = File.ReadAllText(Path.Combine(serverPath, "lib", "SiteSettingsRepository.php"));
        Assert.That(settingsRepoContent, Does.Contain("google_reviews_auto_sync"));
        Assert.That(settingsRepoContent, Does.Contain("saveGoogleReviewsAutoSync"));

        var syncPath = Path.Combine(serverPath, "lib", "ReviewsSync.php");
        Assert.That(File.Exists(syncPath), Is.True, $"Missing file: {syncPath}");

        var syncContent = File.ReadAllText(syncPath);
        Assert.That(syncContent, Does.Contain("class ReviewsSync"));
        Assert.That(syncContent, Does.Contain("refreshIfNeeded"));
    }

    [Test]
    public void ReviewsEndpointsRespectAutoSyncFlag()
    {
        var serverPath = ResolveServerPath();

        var publicReviews = File.ReadAllText(Path.Combine(serverPath, "api", "reviews.php"));
        Assert.That(publicReviews, Does.Contain("ReviewsSync"));
        Assert.That(publicReviews, Does.Contain("google_reviews_auto_sync"),
            "Public reviews endpoint must honor the auto-sync flag before calling Google.");

        var adminReviews = File.ReadAllText(Path.Combine(serverPath, "api", "admin", "reviews.php"));
        Assert.That(adminReviews, Does.Contain("requireAuthenticated"));
        Assert.That(adminReviews, Does.Contain("autoSyncEnabled"));
        Assert.That(adminReviews, Does.Contain("saveGoogleReviewsAutoSync"));
        Assert.That(adminReviews, Does.Contain("refreshIfNeeded"));
    }
}
