namespace AmModaDeploy;

/// <summary>
/// ANSI escape codes for colored console output (same approach as TopBooks Tools).
/// Works in Windows 10+ console and most terminals.
/// </summary>
public static class ConsoleColors
{
    /// <summary>ANSI escape code to reset attributes.</summary>
    public const string Reset = "\x1b[0m";

    /// <summary>ANSI escape code for red foreground.</summary>
    public const string Red = "\x1b[31m";

    /// <summary>ANSI escape code for green foreground.</summary>
    public const string Green = "\x1b[32m";

    /// <summary>ANSI escape code for yellow foreground.</summary>
    public const string Yellow = "\x1b[33m";

    /// <summary>ANSI escape code for blue foreground.</summary>
    public const string Blue = "\x1b[34m";

    /// <summary>ANSI escape code for magenta foreground.</summary>
    public const string Magenta = "\x1b[35m";

    /// <summary>ANSI escape code for cyan foreground.</summary>
    public const string Cyan = "\x1b[36m";

    /// <summary>ANSI escape code for dim/brightness reduced.</summary>
    public const string Dim = "\x1b[2m";

    /// <summary>Writes a line to the console with the given ANSI color prefix, then resets.</summary>
    public static void WriteLine(string colorAnsi, string message)
    {
        Console.WriteLine(string.IsNullOrEmpty(colorAnsi) ? message : colorAnsi + message + Reset);
    }

    /// <summary>Writes to the console with the given ANSI color prefix, then resets (no newline).</summary>
    public static void Write(string colorAnsi, string message)
    {
        Console.Write(string.IsNullOrEmpty(colorAnsi) ? message : colorAnsi + message + Reset);
    }
}
