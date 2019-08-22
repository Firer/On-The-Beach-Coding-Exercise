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

        if (gettype($dependencies) === 'string') // Job list is comma separated list of names. Split it and create an array
        {
            if ($splitList = explode(',', $dependencies)) {
                $dependencies = array_map('trim', $splitList); // Remove whitespace from job names
                $dependencies = array_filter($dependencies, 'strlen'); // Remove empty elements (strings of length 0) from the array)
                $dependencies = array_values($dependencies); // Fix the array indexing. The previous operation preserves the old indexes, converting it to an associative array
                return $dependencies;
            }
        } else if (gettype($dependencies) === 'array') {
            foreach ($dependencies as $dependency) {
                if (gettype($dependency) !== 'string') throw new Exception('Job '. $this->jobName . ': An array of dependencies must contain string values only');
            }

            return $dependencies;
        } else {
            throw new Exception('Job '. $this->jobName . 'A job\'s dependencies must be a comma separated string, or an array of strings');
        }

        return array();
    }
}