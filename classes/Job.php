<?php

class Job
{
    private $jobName = ''; // String containing the job's name
    private $jobQueued = false; // Is the job already queued or not?
    private $dependenciesResolved = 0; // Have the job's dependencies been resolved yet? -1 -> in progress, 0 -> false, 1 -> true
    private $dependencyList = array();
    private $dependencies = array(); // Array containing references to the job's dependencies
    private $dependants = array(); // Array containing references to the job's dependants. Possibly not used yet, but could be a useful addition

    function __construct($name, $dependencies)
    {
        $this->jobName = $name;
        $this->dependencyList = $this->processDependencyList($dependencies);
    }

    public function getJobName()
    {
        return $this->jobName;
    }

    public function getJobQueued()
    {
        return $this->jobQueued;
    }

    public function getDependenciesResolved()
    {
        return $this->dependenciesResolved;
    }

    public function getDependencyList()
    {
        return $this->dependencyList;
    }

    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function getDependants()
    {
        return $this->dependants;
    }

    private function processDependencyList($dependencies) // Dependencies are accepted in multiple forms. Do input validation to make sure they are what is expected and process it
    {
        try
        {
            if (gettype($dependencies) === 'string') // Job list is comma separated list of names. Split it and create an array
            {
                if ($splitList = explode(',', $dependencies))
                {
                    return array_filter(array_map('trim', $splitList), 'strlen'); // Remove whitespace from job names with array_map callback to trim, and remove empty elements (strings of length 0) with array_filter callback to strlen
                }
            }
            else if (gettype($dependencies) === 'array')
            {
                foreach ($dependencies as $dependency)
                {
                    if (gettype($dependency) !== 'string') throw new Exception('An array of dependencies must contain string values only');
                }

                return $dependencies;
            }
            else
            {
                throw new Exception('A job\'s dependencies must be a comma separated string, or an array of strings');
            }
        }
        catch (Exception $e)
        {
            echo 'Caught exception: ',  $e->getMessage(), "\n", 'Exiting now!', "\n";
            exit;
        }

        return array();
    }
}