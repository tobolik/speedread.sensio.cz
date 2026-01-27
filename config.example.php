<?php
/**
 * Database configuration for RSVP Rychločtečka
 * 
 * Copy this file to config.php and update credentials for your environment
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'speedread');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');

// Salt for IP hashing (change this to a random string)
define('IP_HASH_SALT', 'change-this-to-random-string-xyz123');

// CORS settings (set to your domain in production)
define('ALLOWED_ORIGIN', '*');
