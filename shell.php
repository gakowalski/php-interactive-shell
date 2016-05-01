<?php
// Copyright (C) 2016 Grzegorz Kowalski, see LICENSE file

while (true) {
	$command_line = fgets(STDIN);
	eval("$command_line;");
};
