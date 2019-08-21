<?php

// Boilerplate code for automatically loading classes
spl_autoload_register(function ($className) {
    include 'classes/'. $className . '.php';
});