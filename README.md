# php-interactive-shell
Simple PHP interactive shell for Windows.

## Background

As you may know, there exists special parameter `-a` in PHP command line, which is supposed to execute PHP in some kind of "interactive run". In practice, one of two possible modes of execution is possible: "interactive mode" and "interactive shell". Interactive mode simply waits for data from standard input device terminated with CTRL+Z (Windows) or maybe CTRL+D (probably Linux) and then tries to interpret that data as PHP code. Interactive shell works different: every line of data from standard input device is interpreted immediately.

After issuing `php -a` command PHP checks if there is readline extension enabled. If not, interactive mode is chosen. Otherwise, interactive shell is executed. As readline extension [isn't available on Windows][1], only interactive mode is possible in that system.

This little script is trying to simulate some very simple interactive shell. It can be easily extended and I've already used this script with proper modifications in some of my projects.

This code is based on [anynomous comment][2] under PHP online documentation.

## Usage

To run it:

`php shell.php`

Then try to execute some commands, e.g. `echo "Hello\n"` (trailing `;` is optional). There is no command prompt, you can add it manually in the code.

To stop it, simply execute:

`exit`

Thanks to Windows console built-in command history, you can use arrow keys to browse previously used commands.

[1]: http://www.php.net/manual/en/intro.readline.php
[2]: http://php.net/manual/en/features.commandline.interactive.php#98642
