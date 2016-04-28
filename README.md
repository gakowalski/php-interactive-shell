# php-interactive-shell
Simple PHP interactive shell for Windows.

## Background

As you may know, there exists special parameter `-a` in PHP command line which is supposed to execute PHP in some kind of "interactive run". In practice, one of two possible modes of execution is possible: "interactive mode" and "interactive shell". Interactive mode simply waits for data from standard input device terminated with CTRL+Z (Windows) or maybe CTRL+D (probably Linux) and then tries to interpret that data as PHP code. Interactive shell works different: every line of data from stanard input device is interpreted immediately.

After issuing `php -a` command PHP checks if there is readline extension enabled. If not, interactive mode is chosen. Otherwise, interactive shell is executed. As readline extension [isn't available on Windows][1], only interactive mode is possible in that system.

[1]: http://www.php.net/manual/en/intro.readline.php
