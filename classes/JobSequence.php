<?php

class JobSequence
{
    private $allDependenciesResolved = false; // Have all job dependencies been resolved yet?
    private $allJobsWereRun = false; // Have all jobs been run already?
    private $jobsWereSequenced = false; // Have all jobs been sequenced already?
    private $rawJobList = array(); // Raw job list input
    private $sequencedJobList = array(); // Sequenced job list

    function __construct($jobArray) // Accepts a simple array of jobs
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

    public function resolveDependencies() // Resolves dependencies for all jobs
    {
        if ($this->allDependenciesResolved) return; // Dependencies already resolved

        foreach ($this->rawJobList as $job)
        {
            $job->resolveDependencies($this->rawJobList); // Give the job access to the raw job list to resolve its own dependencies
        }

        $this->allDependenciesResolved = true;
    }

    public function getSequencedJobList() // Get a sequenced the job list, so that jobs are run after their dependencies
    {
        if (!$this->allDependenciesResolved) $this->resolveDependencies(); // Dependencies must be resolved before creating a sequenced job list
        if ($this->rawJobList === array()) return array(); // If we have no jobs, return an empty array

        $this->createSequencedJobList();
        return $this->sequencedJobList;
    }

    public function getSequencedJobListString() // Get the sequenced job list as a string
    {
        return implode(', ', $this->getSequencedJobList());
    }

    private function createSequencedJobList() // Creates the sequenced job list based on making sure jobs are run after their dependencies
    {
        $jobNamesToSequence = array(); // Create a temporary array of job names that are not jet in the list and populate it

        foreach ($this->rawJobList as $jobName => $job)
        {
            $jobNamesToSequence[] = $jobName;
        }

        // The main pieces of this loop could also be done in the above loop for efficiency, but it is more clear when separated
        for ($i = 0; $i < count($jobNamesToSequence); $i++) // First pass of job list, to add jobs with no dependencies to the sequence first. This should result in fewer searches in the job sequence array. The sequencing will still work fine without this entire loop.
        {
            $job = $this->rawJobList[$jobNamesToSequence[$i]];
            if ($job->getDependencies() === array()) // The job has no dependencies. Add it to the sequence and remove it from the to-be-sequenced list
            {
                $this->sequencedJobList[] = $job->getJobName();
                unset($jobNamesToSequence[$i]); // Job has been added, so remove it from the array of jobs to add
            }
        }


        $this->addToSequencedJobList($jobNamesToSequence);
    }

    private function addToSequencedJobList(&$jobNamesToSequence) // Recursive function for sequencing jobs after their dependencies
    {
        $startNumberOfJobs = count($jobNamesToSequence); // Track how many jobs we started with to make sure we made progress
        if ($startNumberOfJobs === 0) return;
        for ($i =0; $i < $startNumberOfJobs; $i++) // Iterate through all jobs, and add them if possible
        {
            foreach ($this->rawJobList[$jobNamesToSequence[$i]]->getDependencyList() as $dependencyName) // We must check that all of a job's dependencies are already sequenced
            {
                if (!in_array($dependencyName, $this->sequencedJobList)) continue 2; // If a job's dependency isn't jet sequenced, stop the loop up 2 levels and continue
            }

            $this->sequencedJobList[] = $jobNamesToSequence[$i]; // The job can be sequenced as all dependencies already happen first
            unset($jobNamesToSequence[$i]); // Remove the job from the list
        }

        if (count($jobNamesToSequence) === 0) return; // No jobs left. No need to continue!
        if (count($jobNamesToSequence) === $startNumberOfJobs) throw new Exception('Cannot sequence jobs due to infinite recursion. Probably some circular dependency slipped through'); // Shouldn't ever end up here if circular dependency checking works correctly

        $this->addToSequencedJobList($jobNamesToSequence); // We still have jobs, so use recursion to carry on
    }
}