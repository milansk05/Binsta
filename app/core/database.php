<?php

use RedBeanPHP\R as R;

// Database configuratie
$dbhost = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'binsta';
$dbuser = $_ENV['DB_USER'] ?? 'root';
$dbpass = $_ENV['DB_PASS'] ?? '';

// RedBean setup
R::setup("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
R::freeze(false); // true voor productie