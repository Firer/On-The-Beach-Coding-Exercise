<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Error;

// Boilerplate code for automatically loading classes
spl_autoload_register(function ($className) {
    include '../classes/'. $className . '.php';
});

class JobTest extends TestCase
{
    public function testJobCreationString() // Tests for job creation where dependencies are input as a string
    {
        $testJob = new Job('a', '');
        $this->assertSame('a', $testJob->getJobName(), 'Assertion 1.01: Check job name is correct');
        $this->assertSame(array(), $testJob->getDependencyList(), 'Assertion 1.02: No dependencies lead to an empty array');

        $testJob = new Job('b', 'c');
        $this->assertSame('b', $testJob->getJobName(), 'Assertion 1.03: Check job name is correct');
        $this->assertNotSame(array(), $testJob->getDependencyList(), 'Assertion 1.04: We have a dependency, so array should not be empty');
        $this->assertSame(array('c'), $testJob->getDependencyList(), 'Assertion 1.05: Array should contain our dependency');

        $testJob = new Job('d', 'e, f, g');
        $this->assertSame('d', $testJob->getJobName(), 'Assertion 1.06: Check job name is correct');
        $this->assertSame(array('e', 'f', 'g'), $testJob->getDependencyList(), 'Assertion 1.07: Test case for string with spaces after comma. Should be 3 element array of our dependencies');

        $testJob = new Job('h', 'i,j,k');
        $this->assertSame('h', $testJob->getJobName(), 'Assertion 1.08: Check job name is correct');
        $this->assertSame(array('i', 'j', 'k'), $testJob->getDependencyList(), 'Assertion 1.09: Test case for string with no spaces after comma. Should be 3 element array of our dependencies');

        $testJob = new Job('l', 'm,,n,o,');
        $this->assertSame('l', $testJob->getJobName(), 'Assertion 1.10: Check job name is correct');
        $this->assertSame(array('m', 'n', 'o'), $testJob->getDependencyList(), 'Assertion 1.11: Test case for string with extraneous commas. Should be 3 element array of our dependencies');
    }

    public function testJobCreationArray() // Tests for job creation where dependencies are input as an array. Mostly copies the String creation tests
    {
        $testJob = new Job('a', array());
        $this->assertSame('a', $testJob->getJobName(), 'Assertion 1.12: Check job name is correct');
        $this->assertSame(array(), $testJob->getDependencyList(), 'Assertion 1.13: No dependencies in array should lead to an empty array');

        $testJob = new Job('b', array('c'));
        $this->assertSame('b', $testJob->getJobName(), 'Assertion 1.14: Check job name is correct');
        $this->assertNotSame(array(), $testJob->getDependencyList(), 'Assertion 1.15: We have a dependency, so array should not be empty');
        $this->assertSame(array('c'), $testJob->getDependencyList(), 'Assertion 1.16: Array should contain our dependency');

        $testJob = new Job('d', array('e', 'f', 'g'));
        $this->assertSame('d', $testJob->getJobName(), 'Assertion 1.17: Check job name is correct');
        $this->assertSame(array('e', 'f', 'g'), $testJob->getDependencyList(), 'Assertion 1.18: Test case for string with spaces after comma. Should be 3 element array of our dependencies');
    }

    public function testJobCreationArrayException() // Job creation using a dependency array should only contain string elements. Test for exception if that's not the case
    {
        $this->expectExceptionMessage('An array of', 'Assertion 1.19: Job dependency array with non string element');

        $testJob = new Job('a', array('b', false, 'c'));
    }

    public function testJobCreationInputException() // Job creation accepts only either a string or an array in its dependency parameter. Test for exception if that's not the case
    {
        $this->expectExceptionMessage('A job\'s dependencies', 'Assertion 1.20: Job dependence not a string or array');

        $testJob = new Job('a', false);
    }

    public function testJobAddDependency() // Adding dependencies to jobs that exist
    {
        $testJob1 = new Job('a', array('b', 'c'));
        $testJob2 = new Job('b', array('c', 'd'));
        $testJob3 = new Job('c', array());

        $testJob1->addDependency($testJob2);
        $testJob1->addDependency($testJob3);

        $this->assertSame(array($testJob2, $testJob3), $testJob1->getDependencies(), 'Assertion 1.21: Adding dependencies to jobs');
    }

    public function testJobAddDependent() // Adding dependents to jobs that exist
    {
        $testJob1 = new Job('a', array('b', 'c'));
        $testJob2 = new Job('b', array('c', 'd'));
        $testJob3 = new Job('c', array());

        $testJob2->addDependent($testJob1);
        $testJob3->addDependent($testJob1);
        $testJob3->addDependent($testJob2);

        $this->assertSame(array($testJob1), $testJob2->getDependents(), 'Assertion 1.22: Adding dependents to jobs');
        $this->assertSame(array($testJob1, $testJob2), $testJob3->getDependents(), 'Assertion 1.23: Adding dependents to jobs');
    }

    public function testJobResolveDependencies()
    {
        $testJob1 = new Job('a', array('b', 'c'));
        $testJob2 = new Job('b', array('c'));
        $testJob3 = new Job('c', array());

        $jobArray = array('a' => $testJob1, 'b' => $testJob2, 'c' => $testJob3);

        foreach ($jobArray as $job)
        {
            $job->resolveDependencies($jobArray);
        }

        $this->assertSame(array($testJob2, $testJob3), $testJob1->getDependencies(), 'Assertion 1.24: Job 1 should have 2 dependencies');
        $this->assertSame(array($testJob3), $testJob2->getDependencies(), 'Assertion 1.25: Job 2 should have 1 dependency');
        $this->assertSame(array(), $testJob3->getDependencies(), 'Assertion 1.26: Job 3 should have no dependencies');

        $this->assertSame(array(), $testJob1->getDependents(), 'Assertion 1.27: Job 1 should have no dependents');
        $this->assertSame(array($testJob1), $testJob2->getDependents(), 'Assertion 1.27: Job 2 should have 1 dependent');
        $this->assertSame(array($testJob1, $testJob2), $testJob3->getDependents(), 'Assertion 1.28: Job 3 should have 2 dependents');
    }

    public function testJobResolveDependenciesCircularException()
    {
        $testJob1 = new Job('a', array('b', 'c'));
        $testJob2 = new Job('b', array('c'));
        $testJob3 = new Job('c', array('a'));

        $jobArray = array('a' => $testJob1, 'b' => $testJob2, 'c' => $testJob3);

        $this->expectExceptionMessage('circular dependency', 'Assertion 1.29: Test for circular dependency exception');

        foreach ($jobArray as $job)
        {
            $job->resolveDependencies($jobArray);
        }
    }

    public function testJobResolveDependenciesSelfDependencyException()
    {
        $testJob1 = new Job('a', array('b', 'c'));
        $testJob2 = new Job('b', array('c'));
        $testJob3 = new Job('c', array('c'));

        $jobArray = array('a' => $testJob1, 'b' => $testJob2, 'c' => $testJob3);

        $this->expectExceptionMessage('depend on itself', 'Assertion 1.30: Test for self dependency exception');

        foreach ($jobArray as $job)
        {
            $job->resolveDependencies($jobArray);
        }
    }

    public function testJobResolveDependenciesNonExistentDependencyException()
    {
        $testJob1 = new Job('a', array('b', 'c'));
        $testJob2 = new Job('b', array('c'));
        $testJob3 = new Job('c', array('d'));

        $jobArray = array('a' => $testJob1, 'b' => $testJob2, 'c' => $testJob3);

        $this->expectExceptionMessage('does not exist', 'Assertion 1.31: Test for self dependency exception');

        foreach ($jobArray as $job)
        {
            $job->resolveDependencies($jobArray);
        }
    }

    public static function testJobRun()
    {
        $testJob1 = new Job('a', array('b', 'c'));
        $testJob2 = new Job('b', array('c'));
        $testJob3 = new Job('c', array());

        $jobArray = array('a' => $testJob1, 'b' => $testJob2, 'c' => $testJob3);

        foreach ($jobArray as $job)
        {
            $job->resolveDependencies($jobArray);
        }

        $jobList = array('c', 'b', 'a'); // Manually order job list so dependencies are resolved

        $resultArray = array();

        foreach ($jobList as $jobName)
        {
            $jobArray[$jobName]->run();
            $resultArray[] = $jobArray[$jobName]->jobWasRun();
        }

        $this->assertSame(array(true, true, true), $resultArray, 'Assertion 1.32: Test that jobs run successfully');
    }

    public function testJobRunResolutionException()
    {
        $testJob1 = new Job('a', array('b', 'c'));

        $this->expectExceptionMessage('before dependencies are resolved', 'Assertion 1.33: Test trying to run a job before dependencies are resolved exception');

        $testJob1->run();
    }

    public function testJobRunDependencyException()
    {
        $testJob1 = new Job('a', array('b', 'c'));
        $testJob2 = new Job('b', array('c'));
        $testJob3 = new Job('c', array());

        $jobArray = array('a' => $testJob1, 'b' => $testJob2, 'c' => $testJob3);

        foreach ($jobArray as $job)
        {
            $job->resolveDependencies($jobArray);
        }

        $jobList = array('a', 'b', 'c'); // Manually order job list so dependencies are not resolved

        $this->expectExceptionMessage('before a dependency has been run', 'Assertion 1.34: Test trying to run a job before dependencies are run');

        foreach ($jobList as $jobName)
        {
            $jobArray[$jobName]->run();
        }

        $testJob1->run();
    }

    public function testJobSequenceCreation() // JobSequence creation using test jobs
    {
        $testJob1 = new Job('a', array());
        $testJob2 = new Job('b', array('c', 'd'));

        $testSequence = new JobSequence(array($testJob1, $testJob2));
        $this->assertSame(array('a' => $testJob1, 'b' => $testJob2), $testSequence->getRawJobList(), 'Assertion 2.1: Test simple JobSequence creation');

    }

    public function testJobSequenceCreationExceptionNotAJob() // Check for exception when trying to create a JobSequence when an input is not a Job object
    {
        $testJob1 = new Job('a', array());
        $testJob2 = true;

        $this->expectExceptionMessage('isn\'t a Job', 'Assertion 2.2: Creating JobSequence when input is not a job');

        $testSequence = new JobSequence(array($testJob1, $testJob2));
    }

    public function testJobSequenceCreationDuplicateJobNames() // Check for exception when trying to create a JobSequence when multiple Jobs have the same name
    {
        $testJob1 = new Job('a', array());
        $testJob2 = new Job('a', array('b', 'c'));

        $this->expectExceptionMessage('Multiple jobs exist', 'Assertion 2.3: Testing JobSequence creation with multiple jobs with the same name');

        $testSequence = new JobSequence(array($testJob1, $testJob2));
    }
}