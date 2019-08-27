<?php

// Boilerplate code for automatically loading classes
spl_autoload_register(function ($className) {
    include 'classes/'. $className . '.php';
});

/*
 * Specification 1:
 *
 * Given you’re passed an empty string (no jobs), the result should be an empty sequence.
 */

$input = '';
$output = '';

try
{
    $output = JobParser::parse($input)->getSequencedJobListString();
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Specification 1:', "\n", 'Input is: "', $input, '"', "\n", 'Output is: "', $output, '"', "\n\n";


/*
 * Specification 2:
 *
 * Given the following job structure:
 *
 *     a =>
 *
 * The result should be a sequence consisting of a single job a.
 */


$input = 'a => ';
$output = '';

try
{
    $output = JobParser::parse($input)->getSequencedJobListString();
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Specification 2:', "\n", 'Input is: "', $input, '"', "\n", 'Output is: "', $output, '"', "\n\n";


/*
 * Specification 3:
 *
 * Given the following job structure:
 *
 *     a =>
 *     b =>
 *     c =>
 *
 * The result should be a sequence that positions c before b, containing all three jobs abc.
 */

$input = 'a => , b => , c => ';
$output = '';

try
{
    $output = JobParser::parse($input)->getSequencedJobListString();
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Specification 3:', "\n", 'Input is: "', $input, '"', "\n", 'Output is: "', $output, '"', "\n\n";

/*
 * Specification 4:
 *
 * Given the following job structure:
 *
 *     a =>
 *     b => c
 *     c =>
 *
 * The result should be a sequence that positions c before b, containing all three jobs abc.
 */

$input = 'a => , b => c, c => ';
$output = '';

try
{
    $output = JobParser::parse($input)->getSequencedJobListString();
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Specification 4:', "\n", 'Input is: "', $input, '"', "\n", 'Output is: "', $output, '"', "\n\n";


/*
 * Specification 5:
 *
 * Given the following job structure:
 *
 *     a =>
 *     b => c
 *     c => f
 *     d => a
 *     e => b
 *     f =>
 *
 * The result should be a sequence that positions f before c, c before b, b before e and a before d containing all six jobs abcdef.
 */

$input = 'a => , b => c, c => f, d => a, e => b, f => ';
$output = '';

try
{
    $output = JobParser::parse($input)->getSequencedJobListString();
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Specification 5:', "\n", 'Input is: "', $input, '"', "\n", 'Output is: "', $output, '"', "\n\n";


/*
 * Specification 6:
 *
 * Given the following job structure:
 *
 *     a =>
 *     b =>
 *     c => c
 *
 * The result should be an error stating that jobs can’t depend on themselves.
 */

$input = 'a => , b => , c => c';
$output = '';

try
{
    $output = JobParser::parse($input)->getSequencedJobListString();
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Specification 6:', "\n", 'Input is: "', $input, '"', "\n", 'Output is: "', $output, '"', "\n\n";


/*
 * Specification 7:
 *
 * Given the following job structure:
 *
 *     a =>
 *     b => c
 *     c => f
 *     d => a
 *     e =>
 *     f => b
 *
 * The result should be an error stating that jobs can’t have circular dependencies.
 */

$input = 'a => , b => c, c => f, d => a, e => , f => b';
$output = '';

try
{
    $output = JobParser::parse($input)->getSequencedJobListString();
}
catch (Exception $e)
{
    $output = $e->getMessage();
}

echo 'Specification 7:', "\n", 'Input is: "', $input, '"', "\n", 'Output is: "', $output, '"', "\n\n";