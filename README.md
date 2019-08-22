# On-The-Beach-Coding-Exercise
Exercise for On The Beach to demonstrate coding, problem solving, and design abilities.

### Initial Thoughts
* Define class for jobs, and create object for each job
* Each job should contain a reference to any dependencies and possibly dependants too
* The job object should contain a marker to say whether it's dependencies have been resolved already or whether it is currently being worked on to help with circular dependency resolution
* The job object should contain a marker to say whether it is already queued or not for the case of having multiple dependants, to prevent being queued more than once without having to search the queue
* It's not specified in the challenge description, but it seems logical that a job may have multiple dependencies, so account for this using an array
* Use an array to build the output sequence. While this case is computationally trivial, such an algorithm may be performance sensitive given a large number of jobs, so minimise expensive array manipulation where possible
* Write tests to cover all cases in the given specification along with any extensions, and any testable units
* Use PHP built in Exception handling for dealing with any errors encountered

### Thoughts whilst working on it
* Avoid making it unnecessarily complex
* Don't really need any dependencies. Make it as easy as possible for me to run along with anybody who may be looking at it
* Having the code run on a remote system means an annoying number of commits during testing and debugging