<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// link file
require_once __DIR__ . '/dbcon/Database.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Security.php';
require_once __DIR__ . '/classes/CsrfProtection.php';
require_once __DIR__ . '/classes/DatabaseFunctions.php';
require_once __DIR__ . '/fun/fun.php';
require_once __DIR__ . '/fun/CategoryManager.php';
require_once __DIR__ . '/fun/ProductFun.php';
require_once __DIR__ . '/email/email.php';
require_once __DIR__ . '/classes/emailtemplate.php';
$urlval = "http://localhost/hotelina/";
date_default_timezone_set('Asia/Karachi');

$currentDate = date('Y-m-d'); 
$currentTime = date('H:i:s');
$currentDateTime = date('Y-m-d H:i:s'); 


// classes and object
$db = new Database();
$pdo = $db->getConnection();
$security = new Security('fennec');
$CsrfProtection = new CsrfProtection(); 
$dbFunctions = new DatabaseFunctions($db, $security);
$fun = new Fun($db, $security, $dbFunctions,$urlval);
$categoryManager = new CategoryManager($db, $security, $dbFunctions, $urlval);
$productFun = new Productfun($db, $security, $dbFunctions, $urlval,$currentDate);
$emialTemp = new EmailTemplate();







?>