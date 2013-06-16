#!/usr/bin/env php
<?php

// define build files here -- for eacht .html-file a .twig-file must exist
// in templates/
$FILES = array(
	'index.html' => array(
		'php_self' => 'index.html'
	),
	'contact.html' => array(
		'php_self' => 'contact.html'
	),
	'lidworden.html' => array(
		'php_self' => 'lidworden.html'
	),
	'agenda.html' => array(
		'php_self' => 'agenda.html'
	),
	'geschiedenis.html' => array(
		'php_self' => 'geschiedenis.html'
	)
);

// define constants
define('ASSETS_DIR', 'assets');
define('TEMPLATES_DIR', 'templates/');
define('DEFAULT_BUILD_DIR', 'build');

// include TWIG
require_once("vendor/autoload.php");

// default to build when argv is empty
if (count($argv) === 1) {
	echo "\$argv is empty, defaulting to build\n";
	$argv[] = 'build';
}

// help function
if (in_array('help', $argv)) {
	echo "Usage: php build.php <command> [build_path]\nCommands: help, build, clean";
} elseif (in_array('build', $argv)) {
	build($argv, $FILES);
} elseif (in_array('clean', $argv)) {
	clean($argv);
}

// build function
function build($argv, $FILES) {
	// start build
	$build_i = array_search('build', $argv);
	$build_dir = array_key_exists($build_i+1, $argv) ? $argv[$build_i+1] : DEFAULT_BUILD_DIR;
	if(!is_dir($build_dir)) {
		mkdir($build_dir, 0755, true);
	}
	if(php_uname('s') == 'FreeBSD') {
		echo 'cp -vr ' . ASSETS_DIR . '/* ' . $build_dir . "\n";
		system('cp -vr ' . ASSETS_DIR . '/* ' . $build_dir);
	} else {
		echo 'cp -uvr ' . ASSETS_DIR . ' ' . $build_dir . "\n";
		system('cp -uvr ' . ASSETS_DIR . ' ' . $build_dir);
	}

	// build new Twig Environment
	$twig = new Twig_Environment(new Twig_Loader_Filesystem(TEMPLATES_DIR), array(
			'auto_reload' => true,
			'strict_variables' => true,
			'autoescape' => false
		));

	// add a rot13 filter for email
	$rot13_filter = new Twig_SimpleFilter('rot13', 'str_rot13');
	$twig->addFilter($rot13_filter);

	// add email obscurify function
	$email_function = new Twig_SimpleFunction('email', function ($addr) {
    	return '<script type="text/javascript">document.write("' . str_rot13($addr) . '".rot13())</script>
<noscript>(email adres verborgen)</noscript>'; 
	});
	$twig->addFunction($email_function);

	foreach ($FILES as $filename => $context) {
		$modsrc = filemtime(TEMPLATES_DIR . basename($filename, '.html') . '.twig');
		if(file_exists($build_dir . '/' . $filename)) {
			$moddest = filemtime($build_dir . '/' . $filename);
		} else {
			$moddest = -1;
		}
		if ($moddest && $moddest > $modsrc) {
			continue;
		}
		echo "built $filename\n";
		$html = $twig->render(basename($filename, '.html') . '.twig', $context);
		file_put_contents($build_dir . '/' . $filename, $html);
	}
	echo "done\n";
}

// clean function
function clean($argv) {
	$build_i = array_search('clean', $argv);
	$dir = array_key_exists($build_i+1, $argv) ? $argv[$build_i+1] : DEFAULT_BUILD_DIR;
	if (! is_dir($dir)) {
		return;
	}
	echo 'rm -rf ' . $dir . "\n";
	system('rm -rf ' . $dir);
}

