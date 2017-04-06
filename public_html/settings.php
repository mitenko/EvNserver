<?php
/**
 * Configurations
 */
date_default_timezone_set('America/Vancouver');

/**
 * Paths
 */
define ('__API__', __DIR__ . '/../api/');
define ('__PAGES__', __DIR__ . '/pages/');
define ('__IMGDIR__', __DIR__ . '/img/');
define ('__SERVER_PAGES__', $_SERVER['SERVER_NAME'] . '/pages/');

/**
 * App Constants
 */
define ('MAX_LOGIN_ATTEMPTS', 5);
define ('DATETIME_FORMAT', 'H:i M d, Y');
define ('DATE_FORMAT', 'M d, Y');
?>