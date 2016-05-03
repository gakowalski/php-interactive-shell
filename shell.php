<?php
// Copyright (C) 2016 Grzegorz Kowalski, see LICENSE file

function check_syntax($code) {
	// execute php -a -l
	return true;
}

while (true) {
	$command_line = fgets(STDIN);
	eval("$command_line;");
};
