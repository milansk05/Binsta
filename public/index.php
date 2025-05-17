<?php

// Zorg dat dit bovenaan staat
require_once __DIR__ . '/../vendor/autoload.php';

// Laad environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database configuratie
require_once __DIR__ . '/../app/core/database.php';

// Laad de router
require_once __DIR__ . '/../app/core/router.php';
