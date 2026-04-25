using System.Runtime.InteropServices;
using System.Text;
using Microsoft.Extensions.Configuration;
using AmModaDeploy;
using AmModaDeploy.Deployment;

// Use UTF-8 for console so output from child processes (e.g. Quasar build) displays correctly
Console.OutputEncoding = Encoding.UTF8;
if (RuntimeInformation.IsOSPlatform(OSPlatform.Windows))
{
    try
    {
        Native.SetConsoleOutputCP(65001);
        Native.EnableVirtualTerminalProcessing();
    }
    catch { /* ignore if not running in console */ }
}

var configuration = new ConfigurationBuilder()
    .SetBasePath(AppContext.BaseDirectory)
    .AddJsonFile("appsettings.json", optional: true)
    .AddEnvironmentVariables()
    .AddUserSecrets<Program>(optional: true)
    .Build();

var deploymentSettings = configuration.GetSection("Deployment").Get<DeploymentConfiguration>();

if (deploymentSettings is null)
{
    ConsoleColors.WriteLine(ConsoleColors.Red, "Deployment section is missing in configuration. Ensure user secrets are configured.");
    return;
}

var prodSettings = deploymentSettings.Clone();
configuration.GetSection("DeploymentProd").Bind(prodSettings);

var processRunner = new ProcessRunner();
var ftpUploader = new FtpUploader();

var deploymentService = new DeploymentService(deploymentSettings, processRunner, ftpUploader);
var deploymentServiceProd = new DeploymentService(prodSettings, processRunner, ftpUploader);

while (true)
{
    ConsoleColors.WriteLine(ConsoleColors.Cyan, "AmModa Deploy Tool");
    Console.WriteLine("1 - Deploy");
    Console.WriteLine("2 - Deploy (prod)");
    Console.WriteLine("3 - Test connection");
    Console.WriteLine("4 - Test connection (prod)");
    Console.WriteLine("5 - Show git statistics");
    Console.WriteLine("6 - Exit");
    ConsoleColors.Write(ConsoleColors.Yellow, "Your choice > ");

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
                ConsoleColors.WriteLine(ConsoleColors.Red, $"Deployment failed: {ex.Message}");
            }

            Console.WriteLine();
            break;
        case "2":
            ConsoleColors.WriteLine(ConsoleColors.BrightYellow, $"PROD deployment - target: {prodSettings.RemoteBasePath}");
            try
            {
                await deploymentServiceProd.RunAsync();
            }
            catch (Exception ex)
            {
                ConsoleColors.WriteLine(ConsoleColors.Red, $"Deployment failed: {ex.Message}");
            }

            Console.WriteLine();
            break;
        case "3":
            await deploymentService.TestConnectionAsync();
            Console.WriteLine();
            break;
        case "4":
            ConsoleColors.WriteLine(ConsoleColors.BrightYellow, $"PROD connection test - target: {prodSettings.FtpHost}");
            await deploymentServiceProd.TestConnectionAsync();
            Console.WriteLine();
            break;
        case "5":
            await GitStatistics.ShowGitStatisticsAsync();
            break;
        case "6":
            return;
        default:
            ConsoleColors.WriteLine(ConsoleColors.Red, "Invalid choice. Try again.\n");
            break;
    }
}

internal static partial class Native
{
    [DllImport("kernel32.dll", SetLastError = true)]
    internal static extern bool SetConsoleOutputCP(uint wCodePageID);

    private const int StdOutputHandle = -11;
    private const uint VtProcessingFlag = 0x0004;

    [DllImport("kernel32.dll", SetLastError = true)]
    private static extern IntPtr GetStdHandle(int nStdHandle);

    [DllImport("kernel32.dll", SetLastError = true)]
    private static extern bool GetConsoleMode(IntPtr hConsoleHandle, out uint lpMode);

    [DllImport("kernel32.dll", SetLastError = true)]
    private static extern bool SetConsoleMode(IntPtr hConsoleHandle, uint dwMode);

    internal static void EnableVirtualTerminalProcessing()
    {
        var handle = GetStdHandle(StdOutputHandle);
        if (handle == IntPtr.Zero || handle == new IntPtr(-1))
            return;
        if (!GetConsoleMode(handle, out var mode))
            return;
        mode |= VtProcessingFlag;
        SetConsoleMode(handle, mode);
    }
}
