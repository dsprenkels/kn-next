#!/usr/bin/env php
<?php

// define build files here -- for eacht .html-file a .twig-file must exist
// in templates/
$FILES = array(
	'index.html' => array(
		'php_self' => 'index.html',
		'title' => null
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
	clean($argv);
	build($argv, $FILES);
} elseif (in_array('clean', $argv)) {
	clean($argv);
}

// build function
function build($argv, $FILES) {
	// Specify tidy-configuration
	$tidy_config = array(
		'indent' => true,
		'output-xhtml' => true,
		'wrap' => 200);

	// start build
	$build_i = array_search('build', $argv);
	$build_dir = array_key_exists($build_i+1, $argv) ? $argv[$build_i+1] : DEFAULT_BUILD_DIR;
	echo 'cp -r ' . ASSETS_DIR . ' ' . $build_dir . "\n";
	system('cp -r ' . ASSETS_DIR . ' ' . $build_dir);
	echo "building... ";
	$twig = new Twig_Environment(new Twig_Loader_Filesystem(TEMPLATES_DIR));
	foreach ($FILES as $filename => $context) {
		$html = $twig->render(basename($filename, '.html') . '.twig', $context);
		$tidy = new tidy;
		$tidy->parseString($html, $tidy_config, 'utf8');
		$tidy->cleanRepair();
		file_put_contents($build_dir . '/' . $filename, tidy_get_output($tidy));
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

