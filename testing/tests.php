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