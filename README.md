# On-The-Beach-Coding-Exercise
Exercise for On The Beach to demonstrate coding, problem solving, and design abilities.

### Initial Thoughts
* Define class for jobs, and create object for each job
* Each job should contain a reference to any dependencies and possibly dependents too
* The job object should contain a marker to say whether it's dependencies have been resolved already or whether it is currently being worked on to help with circular dependency resolution
* The job object should contain a marker to say whether it is already queued or not for the case of having multiple dependents, to prevent being queued more than once without having to search the queue
* It's not specified in the challenge description, but it seems logical that a job may have multiple dependencies, so account for this using an array
* Use an array to build the output sequence. While this case is computationally trivial, such an algorithm may be performance sensitive given a large number of jobs, so minimise expensive array manipulation where possible
* Write tests to cover all cases in the given specification along with any extensions, and any testable units
* Use PHP built in Exception handling for dealing with any errors encountered

### Thoughts whilst working on it
* Avoid making it unnecessarily complex
* Don't really need any dependencies. Make it as easy as possible for me to run along with anybody who may be looking at it
* Having the code run on a remote system means an annoying number of commits during testing and debugging
* Jobs should have a "run" member function that checks all dependencies are resolved (have been run), runs the job if they are, and throws an error if not. It then sets a member variable to demonstrate the job has finished
* Make a JobSequence class with a member variable of an array containing the Jobs to sequence, and member functions to sequence it
* Look for jobs without dependencies and sequence them first. Not strictly necessary, but could be a performance gain for large, complex job lists
* Core functionality seems to be complete and working as per the tests
* Does somebody need to be able to use the program to test it out? Make command line utility in sequencer.php
* Make a demo to show that all specifications in the challenge are met in demo.php