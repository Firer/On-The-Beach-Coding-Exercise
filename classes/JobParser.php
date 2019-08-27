<?php

class JobParser // We need to be able to take a string and convert it into Jobs and and a JobSequence
{
    public static function parse($input) // Parse string into an array of matches
    {
        $matches = array();
        $regex = '/\s*            // Allow whitespace at the start of the pattern
                 ([a-z0-9]+)      // Match the job name, which goes in to group [1] of each match
                 \s*=>\s*         // Look for the => separator of job names and job dependencies, and allow whitespace around it
                 (?:              // Create a non-matching group to allow 2 match options (a bracketed array of dependencies, or a single unbracketed dependency)
                 \(\s*            // First matching option. Look for the opening bracket af an array of dependencies, and allow whitespace after it
                 ([a-z0-9,\s]*)   // Match the text inside the brackets
                 \s*\)\s*         // Find the closing bracket, and allow whitespace around it
                 ,?               // Look for the comma separator between jobs which may not exist at the end of the input string
                 |([a-z0-9]*),?   // Second matching option. Look for character(s) followed by a comma separator
                 )/gmix';         // g for global matching, m for multi-line matching, i for case-insensitive matching, and x for extended mode to allow comments in the regex
        preg_match_all($regex, $input, $matches, PREG_SET_ORDER);
        return self::inputToJobSequence($matches);
    }

    private static function inputToJobSequence(&$matches) // Take an array of string matches and return a JobSequence
    {
        $jobs = array();

        foreach ($matches as $match)
        {
            $jobs[] = new Job($match[1], $match[2]);
        }

        return new JobSequence($jobs);
    }
}