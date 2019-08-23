<?php

class Job
{
    private $jobName = ''; // String containing the job's name
    private $jobQueued = false; // Is the job already queued or not?
    private $jobWasRun = false; // The job's run status
    private $dependenciesResolved = 0; // Have the job's dependencies been resolved yet? -1 -> in progress, 0 -> false, 1 -> true
    private $dependencyList = array(); // Array containing the names of the job's dependencies
    private $dependencies = array(); // Array containing references to the job's dependencies
    private $dependents = array(); // Array containing references to the job's dependents. Possibly not used yet, but could be a useful addition

    function __construct($name, $dependencies) // Accepts name parameter, and dependency list in either string or array format
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

    public function jobWasRun()
    {
        return $this->jobWasRun;
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

    public function getDependents()
    {
        return $this->dependents;
    }

    public function addDependency(&$dependency) // Explicitly use references to jobs
    {
        if (!($dependency instanceof Job))  throw new Exception('Job '. $this->jobName . '. Tried to add dependency that isn\'t a Job'); // Check the job is a Job object
        $this->dependencies[] = $dependency;
    }

    public function addDependent(&$dependent) // Explicitly use references to jobs
    {
        if (!($dependent instanceof Job))  throw new Exception('Job '. $this->jobName . '. Tried to add dependent that isn\'t a Job'); // Check the job is a Job object
        $this->dependents[] = $dependent;
    }

    public function resolveDependencies(&$jobList) // Explicitly use references to jobs
    {
        if ($this->dependenciesResolved === 1) return; // This job has been resolved already
        if ($this->dependenciesResolved === -1) throw new Exception('Job '. $this->jobName . '. This job contains a circular dependency'); // We are attempting to resolve this job when we are already trying to resolve it, so it must have a circular dependency

        $this->dependenciesResolved = -1; // Set flag to signify this Job's dependencies are currently being resolved

        foreach ($this->dependencyList as $dependencyName)
        {
            if ($dependencyName === $this->jobName) throw new Exception('Job '. $this->jobName . ' . A job cannot depend on itself'); // Throw an error if the job depends on itself. It otherwise would have been caught as a circular dependency
            if (!array_key_exists($dependencyName, $jobList)) throw new Exception('Job '. $this->jobName . ' . This job has a dependency which does not exist: ' . $dependencyName); // Check we don't have a dependency on a job that doesn't exist in our job list
            $this->addDependency($jobList[$dependencyName]); // Add our dependency to this Job
            $jobList[$dependencyName]->resolveDependencies($jobList); // Recursively resolve the added job's dependencies to find if any are circular
            $jobList[$dependencyName]->addDependent($this); // Add our self as a dependent to the dependency
        }

        $this->dependenciesResolved = 1; // This job's dependencies were successfully resolved
    }

    public function run() // Run the job!
    {
        if ($this->dependenciesResolved !== 1) throw new Exception('Job '. $this->jobName . '. Cannot run job before dependencies are resolved'); // Dependencies must be resolved before we can continue
        foreach ($this->dependencies as $dependency)
        {
            if (!$dependency->jobWasRun()) throw new Exception('Job '. $this->jobName . '. Tried to run job before a dependency has been run'); // Cannot run a job before its dependencies have been run
        }

        // Call code to do the job here

        $this->jobWasRun = true;
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
                if (gettype($dependency) !== 'string') throw new Exception('Job '. $this->jobName . '. An array of dependencies must contain string values only');
            }

            return $dependencies;
        } else {
            throw new Exception('Job '. $this->jobName . '. A job\'s dependencies must be a comma separated string, or an array of strings');
        }

        return array();
    }
}