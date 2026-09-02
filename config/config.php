<?php
declare(strict_types=1);

session_start();
date_default_timezone_set('America/Sao_Paulo');

const APP_NAME = 'Garimpo Brechó';
const BASE_URL = '';
if (is_file(__DIR__ . '/local.php')) require __DIR__ . '/local.php';
$mysqlUrl = getenv('MYSQL_URL') ?: getenv('MYSQL_PUBLIC_URL') ?: '';
$mysqlConnection = $mysqlUrl ? parse_url($mysqlUrl) : [];
define('DB_HOST', getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: ($mysqlConnection['host'] ?? (defined('LOCAL_DB_HOST') ? LOCAL_DB_HOST : '127.0.0.1')));
define('DB_PORT', getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: ($mysqlConnection['port'] ?? '3306'));
define('DB_NAME', getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: (isset($mysqlConnection['path']) ? ltrim($mysqlConnection['path'], '/') : (defined('LOCAL_DB_NAME') ? LOCAL_DB_NAME : 'agenda_viva')));
define('DB_USER', getenv('DB_USER') ?: getenv('MYSQLUSER') ?: ($mysqlConnection['user'] ?? (defined('LOCAL_DB_USER') ? LOCAL_DB_USER : 'root')));
define('DB_PASS', getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: ($mysqlConnection['pass'] ?? (defined('LOCAL_DB_PASS') ? LOCAL_DB_PASS : '')));
