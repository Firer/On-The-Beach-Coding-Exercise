<?php

// Boilerplate code for automatically loading classes
spl_autoload_register(function ($class_name) {
    include 'classes/'. $class_name . '.php';
});