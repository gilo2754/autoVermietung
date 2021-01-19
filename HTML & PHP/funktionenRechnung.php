<?php
/*
<!-- Autoren: Carlo Menjivar und Christiane Maurer
    DB2 SS 2020
    Gruppe 39

*/

include_once('Oracle_Conn.php');

$c = getConn();


function getRechnung($userId){
     $c = getConn();
     $sql = "SELECT r.* FROM rechnung r WHERE r.buchung_id IN (SELECT b.buchung_id FROM buchung b JOIN nutzer n ON n.nutzer_id = b.fahrer_id WHERE n.nutzerkonto_id = :nutzerkonto_id) order by r.rechnung_status desc, r.rechnung_datum desc";
     $rechnung = oci_parse($c, $sql);
     oci_bind_by_name($rechnung, ':nutzerkonto_id', $userId, -1, SQLT_INT);
     $status = oci_execute($rechnung, OCI_DEFAULT);
     if (!$status) {
         return [];
     }
     $result = [];
     oci_fetch_all($rechnung, $result, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
     return $result;
 }

//Rechnung bezahlen 


//TODO: Betrag einer bezahlten Rechung von dem Saldo abziehen
function rechnungBezahlen($userId, $r_id) {
    $c = getConn();

      // var_dump($b_id);
      $rechnung_status = 'BEZAHLT';

       $stmt = oci_parse($c,
       "UPDATE Rechnung SET Rechnung_Status=:rechnung_status_bv WHERE Rechnung_ID=:rechnung_bv AND Buchung_ID IN (SELECT b.buchung_id FROM buchung b JOIN nutzer n ON n.nutzer_id = b.fahrer_id WHERE n.nutzerkonto_id = :nutzerkonto_id)");

        oci_bind_by_name($stmt, ':rechnung_bv', $r_id);
        oci_bind_by_name($stmt, ':rechnung_status_bv', $rechnung_status);
     oci_bind_by_name($stmt, ':nutzerkonto_id', $userId, -1, SQLT_INT);

        $status = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

       oci_free_statement ($stmt);

    oci_close($c);
     return $status;
 }
?>
