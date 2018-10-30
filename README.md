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

Then try to execute some commands, e.g. `echo "Hello\n"`. Last `;` is optional to simplify issuing of single commands. All other semicolons are mandatory.

At this time there is no command prompt, you can add it manually in the code.

To stop the program, simply execute:

`exit`

## Special properties of Windows console

Although this script doesn't implement any special features itself, there are some useful functionalities inherited from Windows [DOSKEY][3] utility, which is now merged into Windows command-line processor:

* Line editing
  * You can change cursor position within edited line with arrow keys, `HOME`, `END` and other keys;
  * Insert and replace mode switched with `INSERT` key;
* Command history
  * Use up and down arrow keys to browse recent commands while editing;
  * Use F7 key to open list of all recent commands in text window;
  * Use last command as a template for new one;

For complete listing please take a look at [TechNet article about DOSKEY][3].

## ANSI colors

On **Windows 10** output comments are colored red if `shell.php` can access helper batch file `ansi_echo.bat`. Batch file takes two arguments: ANSI color code and text message with space characters replaced with `$!`.

## Ideas taken from [PsySH][4]

* `$_` contains result of last operation or `NULL`.
* `$_e` contains last uncatched exception (uncatched by your code).

## Other info

* `break` command stops the shell but not he whole script (so you can call shell.php from other scripts - e.g. instead of `die()` while debugging).

[1]: http://www.php.net/manual/en/intro.readline.php
[2]: http://php.net/manual/en/features.commandline.interactive.php#98642
[3]: https://technet.microsoft.com/en-us/library/cc753867.aspx
[4]: http://psysh.org/
