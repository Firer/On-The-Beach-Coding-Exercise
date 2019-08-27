<?php

class JobParser // We need to be able to take a string and convert it into Jobs and and a JobSequence
{
    public static function parse($input) // Parse string into an array of matches
    {
        $matches = array();
        $regex = '/\s*            # Allow whitespace at the start of the pattern
                 ([a-z0-9]+)      # Match the job name, which goes in to group [1] of each match
                 \s*=>\s*         # Look for the => separator of job names and job dependencies, and allow whitespace around it
                 (?:              # Create non-capturing group to allow 2 match options (a bracketed array of dependencies, or a single unbracketed dependency)
                 \(\s*            # First matching option. Look for the opening bracket af an array of dependencies, and allow whitespace after it
                 ([a-z0-9,\s]*)   # Match the text inside the brackets
                 \s*\)\s*         # Find the closing bracket, and allow whitespace around it
                 (?:,|$)          # Look for the comma separator between jobs, or the end of the input string
                 |([a-z0-9]*)     # Second matching option. Look for character(s)
                 (?:,|$)          # Look for the comma separator between jobs, or the end of the input string
                 )/mix';          # m for multi-line matching, i for case-insensitive matching, and x for extended mode to allow comments in the regex
        preg_match_all($regex, $input, $matches, PREG_SET_ORDER);
        return self::inputToJobSequence($matches);
    }

    private static function inputToJobSequence(&$matches) // Take an array of string matches and return a JobSequence
    {
        $jobs = array();

        foreach ($matches as $match)
        {
            $dependencies = $match[2] !== '' ? $match[2] : $match[3]; // The match for the dependency string could be in either $match[2] or $match[3] depending on the input format
            $jobs[] = new Job($match[1], $dependencies); // The job name goes in to $match[1]
        }

        return new JobSequence($jobs);
    }
}