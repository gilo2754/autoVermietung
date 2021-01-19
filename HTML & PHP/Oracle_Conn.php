<?php
include('NoShareYourCredentials.php');

function getConn() {
global $conn_username, $conn_password;
$c = oci_connect($conn_username, $conn_password, '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST =studidb.gm.th-koeln.de)(PORT =1521)) (CONNECT_DATA = (SERVICE_NAME = ) (SID =vlesung)))');
if (!$c) {
$e = oci_error();
 $conn_username = "user";
  $conn_password = "password";
  trigger_error('Could not connect to database: '. $e['message'],E_USER_ERROR);
}

return $c;}
?>