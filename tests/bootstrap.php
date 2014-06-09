<?php

/** @var \Composer\Autoload\ClassLoader $autoloader */
$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->setPsr4('MockaMocks\\', __DIR__ . '/mocks');
