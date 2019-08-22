<?php

class JobSequence
{
    private $allDependenciesResolved = false; // Have all job dependencies been resolved yet?
    private $allJobsWereRun = false; // Have all jobs been run already?
    private $jobsWereSequenced = false; // Have all jobs been sequenced already?
    private $rawJobList = array(); // Raw job list input
    private $sequencedJobList = array(); // Sequenced job list

    function __construct($jobArray)
    {
        foreach ($jobArray as $job)
        {
            if (!($job instanceof Job))  throw new Exception('Tried to create JobSequence with object that isn\'t a Job'); // Check the job is a Job object
            if (array_key_exists($job->getJobName(), $this->rawJobList)) throw new Exception('Multiple jobs exist with the same name'); // Job names should be unique
            $this->rawJobList[$job->getJobName()] = $job; // Use associative array to create raw job list
        }
    }

    public function getAllDependenciesResolved()
    {
        return $this->allDependenciesResolved;
    }

    public function getAllJobsWereRun()
    {
        return $this->allJobsWereRun;
    }

    public function getJobsWereSequenced()
    {
        return $this->jobsWereSequenced;
    }

    public function getRawJobList()
    {
        return $this->rawJobList;
    }

    public function resolveDependencies()
    {
        if ($this->allDependenciesResolved) return; // Dependencies already resolved

        foreach ($this->rawJobList as $job)
        {
            if ($job->getDependenciesResolved() === 1) break; // This Job has been resolved already
            $job->resolveDependencies($this->rawJobList); // Give the job access to the raw job list to resolve its own dependencies
        }
    }
}