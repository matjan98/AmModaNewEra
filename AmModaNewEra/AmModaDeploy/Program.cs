using Microsoft.Extensions.Configuration;
using AmModaDeploy;
using AmModaDeploy.Deployment;

var configuration = new ConfigurationBuilder()
    .SetBasePath(AppContext.BaseDirectory)
    .AddJsonFile("appsettings.json", optional: true)
    .AddEnvironmentVariables()
    .AddUserSecrets<Program>(optional: true)
    .Build();

var deploymentSettings = configuration.GetSection("Deployment").Get<DeploymentConfiguration>();

if (deploymentSettings is null)
{
    Console.WriteLine("Deployment section is missing in configuration. Ensure user secrets are configured.");
    return;
}

var deploymentService = new DeploymentService(deploymentSettings, new ProcessRunner(), new FtpUploader());

while (true)
{
    Console.WriteLine("AmModa Deploy Tool");
    Console.WriteLine("1 - Deploy");
    Console.WriteLine("2 - Test connection");
    Console.WriteLine("3 - Show git statistics");
    Console.WriteLine("4 - Exit");
    Console.Write("Your choice > ");

    var choice = Console.ReadLine();

    switch (choice)
    {
        case "1":
            try
            {
                await deploymentService.RunAsync();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Deployment failed: {ex.Message}");
            }

            Console.WriteLine();
            break;
        case "2":
            await deploymentService.TestConnectionAsync();
            Console.WriteLine();
            break;
        case "3":
            await GitStatistics.ShowGitStatisticsAsync();
            break;
        case "4":
            return;
        default:
            Console.WriteLine("Invalid choice. Try again.\n");
            break;
    }
}
