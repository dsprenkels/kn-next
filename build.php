#!/usr/bin/env php
<?php

define('ASSETS_DIR', 'assets');
define('TEMPLATES_DIR', 'templates/');
define('DEFAULT_BUILD_DIR', 'build');

require_once("vendor/autoload.php");

$FILES = array(
	'index.html' => array()	
);

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
	$build_i = array_search('build', $argv);
	$build_dir = array_key_exists($build_i+1, $argv) ? $argv[$build_i+1] : DEFAULT_BUILD_DIR;
	echo 'cp -r ' . ASSETS_DIR . ' ' . $build_dir . "\n";
	system('cp -r ' . ASSETS_DIR . ' ' . $build_dir);
	echo "building... ";
	$twig = new Twig_Environment(new Twig_Loader_Filesystem(TEMPLATES_DIR));
	foreach ($FILES as $filename => $context) {
		file_put_contents($build_dir . '/' . $filename,
			$twig->render(basename($filename, '.html') . '.twig', $context));
	}
	echo "done\n";
}

// clean function
function clean($argv) {
	$build_i = array_search('build', $argv);
	$dir = array_key_exists($build_i+1, $argv) ? $argv[$build_i+1] : DEFAULT_BUILD_DIR;
	echo "cleaning '$dir' directory... ";
	if (! is_dir($dir)) {
		goto clean_end;
	}
	$it = new RecursiveDirectoryIterator($dir);
	$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	foreach($files as $file) {
	    if ($file->getFilename() === '.' || $file->getFilename() === '..') {
	        continue;
	    }
	    if ($file->isDir()){
	        rmdir($file->getRealPath());
	    } else {
	        unlink($file->getRealPath());
	    }
	}
	rmdir($dir);
	clean_end:
	echo "done\n";
}

