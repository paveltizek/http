<?php

if (!is_file($autoload = __DIR__ . '/../vendor/autoload.php')) {
	echo "Tester not found. Install Nette Tester using 'composer update --dev'.\n";
	exit(1);
}
require $autoload;
unset($autoload);


Tester\Environment::setup();
date_default_timezone_set('UTC');


function test(\Closure $f) {
	$f();
}


function getBaseUrl() {
	$listen = getenv('TESTS_HTTP_LISTEN');
	if ($listen === FALSE) {
		Tester\Environment::skip("The 'TESTS_HTTP_LISTEN' environment variable is missing. Do you use '--setup tests/setup.php' option?");
	}
	return "http://$listen";
}


function getBaseSslUrl() {
	$listen = getenv('TESTS_HTTPS_LISTEN');
	if ($listen === FALSE) {
		Tester\Environment::skip("The 'TESTS_HTTPS_LISTEN' environment variable is missing. Do you use '--setup tests/setup.php' option?");
	}
	return "https://$listen";
}


function getTempDir() {
	static $dir;

	if ($dir === NULL) {
		@mkdir($base = __DIR__ . DIRECTORY_SEPARATOR . 'temp'); // @ - may already exist
		$dir = $base . DIRECTORY_SEPARATOR . getmypid();
		Tester\Helpers::purge($dir);
		register_shutdown_function(function() use ($dir) {
			Tester\Helpers::purge($dir);
			rmdir($dir);
		});
	}

	return $dir;
}
