<?php
// Copyright (C) 2016 Grzegorz Kowalski, see LICENSE file

function ansi_color($R, $G, $B, $bold = false) {
	return 30 + ($R?1:0) + ($G?2:0) + ($B?3:0) + ($bold?40:0);
}

function ansi_str($message, $code) {
	return '[' . $code . 'm' . $message . '[0m';
}

// llecho('my message');
// llech('my bold blue message', ansi_color(0, 0, 1, 1));
function llecho($message, $ansi_color = '31') {
	if (strpos(php_uname(), 'Windows 10') !== false) {
		system('powershell "[Text.Encoding]::UTF8.GetString([convert]::FromBase64String(\"'
			. base64_encode(ansi_str($message, $ansi_color))
			.'\"))');
	} else {
		echo "// $message\n";
	}
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

		foreach ([2,1,0] as $i) {
			if (isset($pipes[$i]) and is_resource($pipes[$i])) {
				fclose($pipes[$i]);
				unset($pipes[$i]);
			}
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

		/* test: array() */
		if (is_array($_)) llecho('$_ is an array of length ' . count($_));

		/* test_1: new Exception()
			 test_2: unserialize('O:4:"Test_of_incomplete_object":1:{s:1:"i";N;}') */
		if (is_object($_)) llecho('$_ is an object of class ' . get_class($_));

		/* test: $fp = fopen("foo", "w") */
		if (is_resource($_)) llecho('$_ is a resource of type ' . get_resource_type($_));

		/* test: "test" */
		if (is_string($_)) llecho('$_ is a string of length ' . strlen($_));

		/* test: new Exception() */
		if (is_object($_) && method_exists($_, '__toString' ))
			llecho('$_ automatically converts to string of length ' . strlen($_));

		/* test_1: $tmp = 'fopen'
		   test_2: $tmp = function() { } */
		if (!is_object($_) && is_callable($_, false, $callable_name)) llecho('$_ can be called by name ' . $callable_name);

		/* test: null */
		if (is_null($_)) llecho('$_ is null');

		/* test: array() */
		if (empty($_)) llecho('$_ is empty');

		/* test: true */
		if (is_bool($_)) llecho('$_ has boolean value of ' . ($_? 'true' : 'false'));

		/* test: 0 */
		if (is_numeric($_)) llecho('$_ has numeric value of ' . $_);

		/* test: array() */
		if (version_compare(phpversion(), '7.1.0', '>=')) {
			if (is_iterable($_)) llecho('$_ is iterable');
		}

		/* test: $fp = fopen("foo", "w"); fclose($fp); $fp */
		if (version_compare(phpversion(), '7.2.0', '>=')) {
			if (gettype($_) == 'resource (closed)') llecho('$_ is a closed resource');
		} else {
			if (gettype($_) == 'unknown type') llecho('$_ is a closed resource');
		}

		/* test: array() */
		if (version_compare(phpversion(), '7.3.0', '>=')) {
    	if (is_countable($_)) llecho('$_ is countable and has count ' . count($_));
		}

		/* test: [1,2,3] */
		if (version_compare(phpversion(), '8.1.0', '>=')) {
    	if (array_is_list($_)) llecho('$_ is a list');
		}
	}
	catch (Exception $e) {
		$_e = $e;
		llecho('$_e is an object of class ' . get_class($e));
		llecho('$_e->getMessage() returns \'' . $e->getMessage() . '\'');
	}
	catch (\Exception $e) {
		$_e = $e;
		llecho('$_e is an object of class ' . get_class($e));
		llecho('$_e->getMessage() returns \'' . $e->getMessage() . '\'');
	}
	catch (\Error $e) { // Error is the base class for all internal PHP error exceptions.
		$_e = $e;
		llecho('Internal PHP error - probably FATAL!');
		llecho('$_e is an object of class ' . get_class($e));
		llecho('$_e->getMessage() returns \'' . $e->getMessage() . '\'');
	}
};
