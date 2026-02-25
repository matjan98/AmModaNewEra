using System.Collections.Generic;

namespace AmModaDeploy.Deployment;

public class DeploymentConfiguration
{
    public string? FrontendPath { get; set; }
    public string? BackendPath { get; set; }

    public string? BuildOutputSubPath { get; set; } = "dist/spa";

    public string? RemoteBackendPath { get; set; } = "server";

    public string? SmtpPassword { get; set; }

    public string? FtpHost { get; set; }

    public int FtpPort { get; set; } = 21;

    public string? FtpUsername { get; set; }

    public string? FtpPassword { get; set; }

    public string? RemoteBasePath { get; set; }

    public bool UsePassiveMode { get; set; } = true;

    public string? GitRepositoryPath { get; set; }

    public string GitTagPrefix { get; set; } = "deploy";

    public bool CreateGitTagOnDeploy { get; set; } = true;

    public IEnumerable<string> Validate()
    {
        if (string.IsNullOrWhiteSpace(FtpHost))
        {
            yield return "Deployment:FtpHost is missing.";
        }

        if (FtpPort <= 0)
        {
            yield return "Deployment:FtpPort must be greater than zero.";
        }

        if (string.IsNullOrWhiteSpace(FtpUsername))
        {
            yield return "Deployment:FtpUsername is missing.";
        }

        if (string.IsNullOrWhiteSpace(FtpPassword))
        {
            yield return "Deployment:FtpPassword is missing.";
        }

        if (string.IsNullOrWhiteSpace(SmtpPassword))
        {
            yield return "Deployment:SmtpPassword is missing. Set it using: dotnet user-secrets set \"Deployment:SmtpPassword\" \"your-smtp-password\"";
        }
    }
}
