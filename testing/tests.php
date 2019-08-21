<?php

require 'phpunit.phar';
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
        $this->assertSame('a', $testJob->getJobName(), 'Check job name is correct');
        $this->assertSame(array(), $testJob->getDependencyList(), 'No dependencies lead to an empty array');

        $testJob = new Job('b', 'c');
        $this->assertSame('b', $testJob->getJobName(), 'Check job name is correct');
        $this->assertNotSame(array(), $testJob->getDependencyList(), 'We have a dependency, so array should not be empty');
        $this->assertSame(array('c'), $testJob->getDependencyList(), 'Array should contain our dependency');

        $testJob = new Job('d', 'e, f, g');
        $this->assertSame('d', $testJob->getJobName(), 'Check job name is correct');
        $this->assertSame(array('e', 'f', 'g'), $testJob->getDependencyList(), 'Test case for string with spaces after comma. Should be 3 element array of our dependencies');

        $testJob = new Job('h', 'i,j,k');
        $this->assertSame('h', $testJob->getJobName(), 'Check job name is correct');
        $this->assertSame(array('i', 'j', 'k'), $testJob->getDependencyList(), 'Test case for string with no spaces after comma. Should be 3 element array of our dependencies');

        $testJob = new Job('l', 'm,,n,o,');
        $this->assertSame('l', $testJob->getJobName(), 'Check job name is correct');
        $this->assertSame(array('m', 'n', 'o'), $testJob->getDependencyList(), 'Test case for string with extraneous commas. Should be 3 element array of our dependencies');
    }

    public function testJobCreationArray() // Tests for job creation where dependencies are input as an array. Mostly copies the String creation tests
    {
        $testJob = new Job('a', array());
        $this->assertSame('a', $testJob->getJobName(), 'Check job name is correct');
        $this->assertSame(array(), $testJob->getDependencyList(), 'No dependencies in array should lead to an empty array');

        $testJob = new Job('b', array('c'));
        $this->assertSame('b', $testJob->getJobName(), 'Check job name is correct');
        $this->assertNotSame(array(), $testJob->getDependencyList(), 'We have a dependency, so array should not be empty');
        $this->assertSame(array('c'), $testJob->getDependencyList(), 'Array should contain our dependency');

        $testJob = new Job('d', array('e', 'f', 'g'));
        $this->assertSame('d', $testJob->getJobName(), 'Check job name is correct');
        $this->assertSame(array('e', 'f', 'g'), $testJob->getDependencyList(), 'Test case for string with spaces after comma. Should be 3 element array of our dependencies');
    }

    public function testJobCreationArrayException() // Job creation using a dependency array ahould only contain string elements. Test for exception if that's not the case
    {
        $this->expectException(Error::class);

        $testJob = new Job('a', array('b', false, 'c'));
    }

    public function testJobCreationInputException() // Job creation using accepts only either a string or an array in its dependency parameter. Test for exception if that's not the case
    {
        $this->expectException(Error::class);

        $testJob = new Job('a', false);
    }
}