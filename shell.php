<?php
// Copyright (C) 2016 Grzegorz Kowalski, see LICENSE file

function llecho($message) {
	echo "// $message\n";
}

function php_syntax_check_func($data, $stdin, $stdout, $stderr)
{
	fwrite($stdin, $data);
	fclose($stdin);
	stream_get_contents($stdout);	//< unblocking stdout
	return stream_get_contents($stderr);
}

function execute_console_app($path, $process_function, $data = null)
{
	$pipes_spec = array(
		0 => array('pipe', 'r'),
		1 => array('pipe', 'w'),
		2 => array('pipe', 'w'),
	);

	$process = proc_open($path, $pipes_spec, $pipes);

	if (is_resource($process)) {
		$return_value = $process_function($data, $pipes[0], $pipes[1], $pipes[2]);

		if (isset($pipes[2]) and is_resource($pipes[2])) {
			fclose($pipes[2]);
			unset($pipes[2]);
		}

		if (isset($pipes[1]) and is_resource($pipes[1])) {
			fclose($pipes[1]);
			unset($pipes[1]);
		}

		if (isset($pipes[0]) and is_resource($pipes[0])) {
			fclose($pipes[0]);
			unset($pipes[0]);
		}

		$results = Array(
			'value' => $return_value,
			'code' => proc_close($process),
		);
	}	else {
		$results = null;
	}

	return $results;
}

function check_syntax($code, $print_error_message = false)
{
	$result = execute_console_app('php -l', 'php_syntax_check_func', '<?php '.$code);

	if ($result['code'] == 0) {
		return true;
	}

	if ($print_error_message !== false) {
		llecho('Error message from `php -l`: ' . $result['value']);
	}

	return false;
}

while (true)
{
	$command_line = trim(fgets(STDIN));

  if (trim($command_line) == '') {
		continue;	//< do not (re)set $_ for blank lines
	}

	$command_line .= ';';

	if (trim($command_line) == 'break;') {
		break;	//< resume execution in case shell is used in debugging
	}

	if (check_syntax("$command_line", true) === false) {
		continue;
	}

	if (check_syntax("\$_ = $command_line") === true) {
		$command_line = "\$_ = $command_line";
		// echo "// \$_ has value of $_\n";
	} else {
		$command_line .= '$_ = null;';
	}

	try {
		//echo "// executing $command_line\n";
		eval($command_line);

		if (is_array($_)) llecho('$_ is an array of length ' . count($_));
		if (is_object($_)) llecho('$_ is an object of class ' . get_class($_));
		if (is_resource($_)) llecho('$_ is a resource');
		if (is_string($_)) llecho('$_ is a string of length ' . strlen($_));
		if (is_callable($_)) llecho('$_ can be called as a function');
		if (is_null($_)) llecho('$_ is null');
		if (is_bool($_)) llecho('$_ has boolean value of ' . ($_? 'true' : 'false'));
		if (is_numeric($_)) llecho('$_ has numeric value of ' . $_);
	}
	catch (Exception $e) {
		$_e = $e;
		llecho('$_e is an object of class ' . get_class($_e));
		llecho('$_e->getMessage() returns \'' . $e->getMessage() . '\'');
	}
	catch (\Exception $e) {
		$_e = $e;
		llecho('$_e is an object of class ' . get_class($_e));
		llecho('$_e->getMessage() returns \'' . $e->getMessage() . '\'');
	}
};
