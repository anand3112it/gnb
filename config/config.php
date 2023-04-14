<?php
error_reporting(0);
ini_set('display_errors', 0);

require('functions.php');
require('validation.php');

$input = $_POST;

define('DATA_URL', dirname(__DIR__).'/data/');
define('ASSETS_URL', 'assets/');

?>