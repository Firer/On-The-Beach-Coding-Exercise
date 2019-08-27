<?php

// Boilerplate code for automatically loading classes
spl_autoload_register(function ($className) {
    include 'classes/'. $className . '.php';
});

if (!isset($argv) || !isset($argv[1])) exit('This is a command line utility. Use: \'php sequencer.php "job-list"\'' . "\n"); // Check we are running from the command line and the appropriate arguments exist

switch ($argv[1]) // Look for help argument, and output helpful information
{
    case 'help':
    case '--help':
    case '-h':
        echo 'Use: \'php sequencer.php "job-list"\'', "\n\n", 'The job list must be quoted. For example:', "\n", 'php sequencer.php "a => b, b => (c, d), c => , d => , e => (a)"', "\n";
}
$input = '';
if (!preg_match('/["\'](.*?)["\']/', $argv[1], $input)) exit('Bad job list input. See "php sequencer.php help" for more info'); // Find the quoted job list string
$output = '';

try
{
    $output = JobParser::parse($input[1])->getSequencedJobListString(); // Our job string should be in $input[1] from the regular expression match
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Job sequencer:', "\n", 'Input is: "', $input[1], '"', "\n", 'Output is: "', $output, '"', "\n\n";