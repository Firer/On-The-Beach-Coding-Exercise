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
        $testJob = new Job('a', '', true);
        $this->assertSame('a', $testJob->getJobName(), 'Assertion 01: Check job name is correct');
        $this->assertSame(array(), $testJob->getDependencyList(), 'Assertion 02: No dependencies lead to an empty array');

        $testJob = new Job('b', 'c', true);
        $this->assertSame('b', $testJob->getJobName(), 'Assertion 03: Check job name is correct');
        $this->assertNotSame(array(), $testJob->getDependencyList(), 'Assertion 04: We have a dependency, so array should not be empty');
        $this->assertSame(array('c'), $testJob->getDependencyList(), 'Assertion 05: Array should contain our dependency');

        $testJob = new Job('d', 'e, f, g', true);
        $this->assertSame('d', $testJob->getJobName(), 'Assertion 06: Check job name is correct');
        $this->assertSame(array('e', 'f', 'g'), $testJob->getDependencyList(), 'Assertion 07: Test case for string with spaces after comma. Should be 3 element array of our dependencies');

        $testJob = new Job('h', 'i,j,k', true);
        $this->assertSame('h', $testJob->getJobName(), 'Assertion 08: Check job name is correct');
        $this->assertSame(array('i', 'j', 'k'), $testJob->getDependencyList(), 'Assertion 09: Test case for string with no spaces after comma. Should be 3 element array of our dependencies');

        $testJob = new Job('l', 'm,,n,o,', true);
        $this->assertSame('l', $testJob->getJobName(), 'Assertion 10: Check job name is correct');
        $this->assertSame(array('m', 'n', 'o'), $testJob->getDependencyList(), 'Assertion 11: Test case for string with extraneous commas. Should be 3 element array of our dependencies');
    }

    public function testJobCreationArray() // Tests for job creation where dependencies are input as an array. Mostly copies the String creation tests
    {
        $testJob = new Job('a', array(), true);
        $this->assertSame('a', $testJob->getJobName(), 'Assertion 12: Check job name is correct');
        $this->assertSame(array(), $testJob->getDependencyList(), 'Assertion 13: No dependencies in array should lead to an empty array');

        $testJob = new Job('b', array('c'), true);
        $this->assertSame('b', $testJob->getJobName(), 'Assertion 14: Check job name is correct');
        $this->assertNotSame(array(), $testJob->getDependencyList(), 'Assertion 15: We have a dependency, so array should not be empty');
        $this->assertSame(array('c'), $testJob->getDependencyList(), 'Assertion 16: Array should contain our dependency');

        $testJob = new Job('d', array('e', 'f', 'g'), true);
        $this->assertSame('d', $testJob->getJobName(), 'Assertion 17: Check job name is correct');
        $this->assertSame(array('e', 'f', 'g'), $testJob->getDependencyList(), 'Assertion 18: Test case for string with spaces after comma. Should be 3 element array of our dependencies');
    }

    public function testJobCreationArrayException() // Job creation using a dependency array ahould only contain string elements. Test for exception if that's not the case
    {
        $this->expectException(Error::class, 'Assertion 19: ');

        $testJob = new Job('a', array('b', false, 'c'), true);
    }

    public function testJobCreationInputException() // Job creation using accepts only either a string or an array in its dependency parameter. Test for exception if that's not the case
    {
        $this->expectException(Error::class, 'Assertion 20: ');

        $testJob = new Job('a', false, true);
    }
}