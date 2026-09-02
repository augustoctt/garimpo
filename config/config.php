<?php
declare(strict_types=1);

session_start();
date_default_timezone_set('America/Sao_Paulo');

const APP_NAME = 'Garimpo Brechó';
const BASE_URL = '';
if (is_file(__DIR__ . '/local.php')) require __DIR__ . '/local.php';
define('DB_HOST', getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: (defined('LOCAL_DB_HOST') ? LOCAL_DB_HOST : '127.0.0.1'));
define('DB_PORT', getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: (defined('LOCAL_DB_NAME') ? LOCAL_DB_NAME : 'agenda_viva'));
define('DB_USER', getenv('DB_USER') ?: getenv('MYSQLUSER') ?: (defined('LOCAL_DB_USER') ? LOCAL_DB_USER : 'root'));
define('DB_PASS', getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: (defined('LOCAL_DB_PASS') ? LOCAL_DB_PASS : ''));
