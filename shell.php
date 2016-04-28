<?php

function shell_main_loop() {
	$file_pointer = fopen('php://stdin', 'r');
	
	while (true) {
		$command_line = fgets($file_pointer);
		eval("$command_line;");
	}	
	
	fclose($file_pointer);
}

shell_main_loop();