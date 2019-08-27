<?php

/*
 * This is a command line utility, which takes a string job list input,
 * and outputs a sequenced list.
 *
 * See "php sequencer.php help" for more information
 */

// Boilerplate code for automatically loading classes
spl_autoload_register(function ($className) {
    include 'classes/'. $className . '.php';
});

if (!isset($argv) || !isset($argv[1])) exit('This is a command line utility. Use: \'php sequencer.php "job-list"\', see \'php sequencer.php help\' for more information' . "\n"); // Check we are running from the command line and the appropriate arguments exist

switch ($argv[1]) // Look for help argument, and output helpful information
{
    case 'help':
    case '--help':
    case '-h':
        echo 'Use: \'php sequencer.php "job-list"\'', "\n\n", 'The job list must be quoted. For example:', "\n", 'php sequencer.php "a => b, b => (c, d), c => , d => , e => (a)"', "\n";
        exit;
}

$input = $argv[1];
$output = '';

try
{
    $output = JobParser::parse($input)->getSequencedJobListString(); // Parse our job string
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Job sequencer:', "\n", 'Input is: "', $input, '"', "\n", 'Output is: "', $output, '"', "\n\n";