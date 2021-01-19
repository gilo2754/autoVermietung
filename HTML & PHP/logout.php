<?php
$titleAddition = 'Abmelden';
//include_once('Oracle_Conn.php');
include_once('header.php');
include_once('funktionen.php');
$basePath = '/drive&share';

if (array_key_exists('userId', $_SESSION)) {
    $_SESSION['userId'] = -1;
}
    header('Location: index.php');
    die();