namespace AmModaDeploy;

public static class ConsoleColors
{
    public const string Reset = "\x1b[0m";

    public const string Red = "\x1b[31m";

    public const string Green = "\x1b[32m";

    public const string Yellow = "\x1b[33m";

    public const string BrightYellow = "\x1b[93m";

    public const string Blue = "\x1b[34m";

    public const string DarkBlue = "\x1b[2m\x1b[34m";

    public const string BrightBlue = "\x1b[94m";

    public const string Magenta = "\x1b[35m";

    public const string Cyan = "\x1b[36m";

    public const string Dim = "\x1b[2m";

    public static void WriteLine(string colorAnsi, string message)
    {
        Console.WriteLine(string.IsNullOrEmpty(colorAnsi) ? message : colorAnsi + message + Reset);
    }

    public static void Write(string colorAnsi, string message)
    {
        Console.Write(string.IsNullOrEmpty(colorAnsi) ? message : colorAnsi + message + Reset);
    }
}
