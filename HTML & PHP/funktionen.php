<?php
/*
<!-- Autoren: Carlo Menjivar und Christiane Maurer
    DB2 SS 2020
    Gruppe 39
    
    Kommentare:
    
    **Using Bind Variables**
    Bind variables enable you to re-execute statements with new values, without the overhead of reparsing the statement. Bind variables improve code reusability, and can reduce the risk of SQL Injection attacks.
    To use bind variables in this example, perform the following steps.
    https://www.oracle.com/webfolder/technetwork/tutorials/obe/db/oow10/php_db/php_db.htm?print=preview
 -->
*/

include_once('Oracle_Conn.php');


set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

$c = getConn();
/*
function check_Session(){
if( isset($_SESSION['email']) ){
    header('Location:Home.php');
    }
    $c = getConn();

    $stmt = oci_parse($c, 
    "SELECT * FROM  NUTZERKONTO WHERE E_MAIL_ADRESSE= :email_bv");
    oci_bind_by_name($stmt, ':email_bv', $email);    
    oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

    if($etwas = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)){
        echo "<h3>Welcome . $email </h3>";
    }
    else {echo "<h3>$email nO EXISTE </h3>";}
 }
*/

function neueNutzerkonto($formular){
    global $c;

    if (!$c) {
        $m = oci_error();
        trigger_error(htmlentities($m['message']), E_USER_ERROR);
    }

    $email = $formular['email'];
    $password = $formular['password'];
    $nutzerkontoSql = "INSERT INTO NUTZERKONTO (E_MAIL_ADRESSE, PASSWORT)  VALUES(:email_bv, :pass_bv) RETURNING nutzerkonto_id INTO :row_id";
    $selectNutzerkontoSql = "SELECT nutzerkonto_id FROM NUTZERKONTO WHERE E_Mail_Adresse = :email_bv AND ZEICHENKETTE_ENTSCHLUESSELN(passwort) = :pass_bv";
    $nutzerSql = 'INSERT INTO nutzer (nutzerkonto_id, nutzer_bewertung, nutzer_status, saldo) VALUES (:nutzerkonto_id, 5.0, \'AKTIV\', 0)';
    $bewertungSql = '';

    $stmt = oci_parse($c, $nutzerkontoSql);
    oci_bind_by_name($stmt, ':email_bv', $email);    
    oci_bind_by_name($stmt, ':pass_bv', $password);
    oci_bind_by_name($stmt, ":row_id", $userId);
    try {
      oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    } catch (Exception $e) {
      oci_rollback($c);
      return $e->getMessage();
   }

    $stmt = oci_parse($c, $nutzerSql);
    oci_bind_by_name($stmt, ':nutzerkonto_id', $userId, -1, SQLT_INT);
    oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

    oci_free_statement ($stmt);
    oci_close($c);
    return $stmt;
}

function login($userName, $password) {
    $c = getConn();

    if (!$c) {
        $m = oci_error();
        trigger_error(htmlentities($m['message']), E_USER_ERROR);
    }
    #SELECT * FROM NUTZERKONTO WHERE E_Mail_Adresse = 'christianemaurer1@web.de' AND ZEICHENKETTE_ENTSCHLUESSELN( passwort) = 'Hallo123'
    $stmt = oci_parse($c,
    "SELECT nutzerkonto_id FROM NUTZERKONTO WHERE E_Mail_Adresse = :email_bv AND ZEICHENKETTE_ENTSCHLUESSELN(passwort) = :pass_bv");
    oci_bind_by_name($stmt, ':email_bv', $userName);
    oci_bind_by_name($stmt, ':pass_bv', $password);
    oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

    $user = oci_fetch_array($stmt, OCI_ASSOC);
    $userId = intval($user['NUTZERKONTO_ID'] ?? -1);
    return $userId;
}

function getUserData($userId) {
   $c = getConn();
    $sql = "SELECT nk.e_mail_adresse, n.nutzer_id, n.vorname, n.nachname, n.nutzername, n.mobilnummer, n.saldo as saldo, n.anschrift.strasse as strasse, n.anschrift.Hausnummer as hausnummer, n.anschrift.PLZ as zip, n.anschrift.ort as stadt, n.anschrift.land as land FROM nutzerkonto nk LEFT JOIN nutzer n ON n.nutzerkonto_id = nk.nutzerkonto_id WHERE nk.nutzerkonto_id = :user_id";
    $stmt = oci_parse($c, $sql);
    oci_bind_by_name($stmt, ':user_id', $userId, -1, SQLT_INT);
    oci_execute($stmt, OCI_DEFAULT);
    $user = oci_fetch_array($stmt, OCI_ASSOC);
    return $user;
}

function setUserData($userData) {
    $c = getConn();
    $testSql = "SELECT nutzer_id FROM nutzer WHERE nutzerkonto_id = :nutzerkonto_id";
    $stmt = oci_parse($c, $testSql);
    oci_bind_by_name($stmt, ':nutzerkonto_id', $userData['nutzerkonto_id'], -1, SQLT_INT);
    oci_execute($stmt, OCI_DEFAULT);
    $user = oci_fetch_array($stmt, OCI_ASSOC);

    $sql = <<<SQL
        UPDATE
            nutzer
        SET
           vorname = :vorname,
           nachname = :nachname,
           nutzername = :nutzername,
           mobilnummer = :mobilnummer,
           anschrift = anschrift_t(
                        :strasse,
                        :hausnummer,
                        :zip,
                        :stadt,
                        :land)
        WHERE
            nutzerkonto_id = :nutzerkonto_id

SQL;

    $stmt = oci_parse($c, $sql);
    oci_bind_by_name($stmt, ':nutzerkonto_id', $userData['nutzerkonto_id'], -1, SQLT_INT);
    oci_bind_by_name($stmt, ':vorname', $userData['vorname']);
    oci_bind_by_name($stmt, ':nachname', $userData['nachname']);
    oci_bind_by_name($stmt, ':nutzername', $userData['nutzername']);
    oci_bind_by_name($stmt, ':mobilnummer', $userData['mobilnummer']);
    oci_bind_by_name($stmt, ':hausnummer', $userData['hausnummer']);
    oci_bind_by_name($stmt, ':strasse', $userData['strasse']);
    oci_bind_by_name($stmt, ':zip', $userData['zip']);
    oci_bind_by_name($stmt, ':stadt', $userData['stadt']);
    oci_bind_by_name($stmt, ':land', $userData['land']);
    $success = oci_execute($stmt, OCI_DEFAULT);
    if ($success) {
        oci_commit($c);
    }
    return $success;
}

function neuesFahrzeug($neueF){

    $c = getConn();

    if (!$c) {
        $m = oci_error();
        trigger_error(htmlentities($m['message']), E_USER_ERROR);
    }

    $marke = $neueF['marke'];
    $fKennzeichen = $neueF['fKennzeichen'];
    $fPreis = $neueF['fPreis'];
    $fBild = "https://images.unsplash.com/photo-1516771317026-14d76f5396e5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=600&q=60";
    $Fahrzeughalter_id=1;
    $fStatus = 'VERFUEGBAR';
    

    $stmt = oci_parse($c, 
    "INSERT INTO Fahrzeug(Fahrzeughalter_id, Fahrzeug_marke, Fahrzeug_Preis_Faktor, FAHRZEUGKENNZEICHEN, Fahrzeug_status, Fahrzeug_bild)
    VALUES(:f_Halter_bv, :marke_bv, :fPreis_bv, :fKennzeichen_bv, :fStatus_bv, :fBild_bv)");

    oci_bind_by_name($stmt, ':marke_bv', $marke);    
    oci_bind_by_name($stmt, ':fKennzeichen_bv', $fKennzeichen);
    oci_bind_by_name($stmt, ':fPreis_bv', $fPreis);

    oci_bind_by_name($stmt, ':fBild_bv', $fBild);
    oci_bind_by_name($stmt, ':fStatus_bv', $fStatus);
    oci_bind_by_name($stmt, ':f_Halter_bv', $Fahrzeughalter_id);
    oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    oci_commit($c);

    oci_free_statement ($stmt);
    oci_close($c);
    return $stmt;

 
}

function fahrzeugBuchen($userId, $fahrzeugF) {
    $c = getConn();
          echo "<hr></hr>";
      // var_dump($fahrzeugF);
      // $fahrzeug_id= print json_encode ($fahrzeugF);
      $fahrzeug_id = $fahrzeugF['f_id'];
       //echo "<h3>Fahrzeug gebucht: $fahrzeug_id</h3>";
       
    $nutzerkonto_id = $userId;
    $sql = 'SELECT nutzer_id FROM nutzer WHERE nutzerkonto_id = :nutzerkonto_id';

    $stmt = oci_parse($c, $sql);
    oci_bind_by_name($stmt, ':nutzerkonto_id', $nutzerkonto_id, -1, SQLT_INT);
    oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

    $user = oci_fetch_array($stmt, OCI_ASSOC);

    if ($user === false) {
        return null;
    }


    $fahrer_id = $user['NUTZER_ID'];
    $fBuchen = oci_parse($c, 
     'begin Fahrzeug_buchen_P(:fahrer_bv, :fahrzeug_bv); end;');
 
     oci_bind_by_name($fBuchen, ':fahrer_bv', $fahrer_id, -1, SQLT_INT);
     oci_bind_by_name($fBuchen, ':fahrzeug_bv', $fahrzeug_id, -1, SQLT_INT);
    
    oci_execute($fBuchen, OCI_COMMIT_ON_SUCCESS);
    oci_commit($c);
    oci_free_statement ($fBuchen);
    oci_close($c);
     return null;
 }


 //Fahrzeug löschen
function fahrzeugLoeschen($fahrzeugF) {
    $c = getConn();
          echo "<hr></hr>";
      // var_dump($fahrzeug_id);
      // $fahrzeug_id= print json_encode ($fahrzeugF);
      $fahrzeug_id = $fahrzeugF['f_id'];
       echo "<h3>Fahrzeug gelöcht: $fahrzeug_id</h3>";
       var_dump($fahrzeug_id);
      // $sql="DELETE FROM FAHRZEUG WHERE FAHRZEUG_ID=:fahrzeug_bv";
    $fLoeschen = oci_parse($c, 
    'begin Fahrzeug_loeschen_P(:fahrzeug_bv); end;'); 
 
     oci_bind_by_name($fLoeschen, ':fahrzeug_bv', $fahrzeug_id);
    
    oci_execute($fLoeschen, OCI_COMMIT_ON_SUCCESS);
    oci_commit($c);
    oci_free_statement ($fLoeschen);
    oci_close($c);
     return Null;
 }
 //

 function buchungAktualisieren($b_id) {
    $c = getConn();
   
    echo "<hr></hr>";
    // var_dump($b_id);
     $b_id = $b_id['b_id']; //Var->Array[0]
     $aktion='BEENDET';
     echo "<h3>Buchung zu beenden: $b_id</h3>";
     
  $bBeenden = oci_parse($c, 
   'begin Buchung_aktuallisieren(:buchung_bv, :aktion); end;');  

   oci_bind_by_name($bBeenden, ':buchung_bv', $b_id);     
   oci_bind_by_name($bBeenden, ':aktion', $aktion);    

   oci_execute($bBeenden, OCI_COMMIT_ON_SUCCESS);
  oci_commit($c);
  oci_free_statement ($bBeenden);
 
  oci_close($c);
   return Null;
 }

//Buchung bewerten
function buchungBewerten($b_id) {
    $c = getConn();
   
      // var_dump($b_id);
      $buchung_bewertung = 4.8;
      //$buchung_bewertung = $buchung['b_bewertung'];

       $b_id = $b_id['b_id']; //Var->Array[0]
       echo "<h3>Buchung zu bewerten: $b_id</h3>";
       
       $stmt = oci_parse($c, 
       "UPDATE Buchung SET Buchung_BEWERTUNG=:buchung_bewertung_bv WHERE BUCHUNG_ID=:buchung_bv");
    
        oci_bind_by_name($stmt, ':buchung_bv', $b_id);     
        oci_bind_by_name($stmt, ':buchung_bewertung_bv', $buchung_bewertung);    
   
        oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
       oci_commit($c);
       oci_free_statement ($stmt);
   
    oci_close($c);
     return Null;
 } 


 function getFahrzeug($userId){
   $c = getConn();
    $sql = <<<SQL
    SELECT
        nf.nutzer_id as meins,
        n.nutzername,
        fs.*
    FROM fahrzeug fs
        JOIN nutzer n
            ON n.nutzer_id = fs.fahrzeughalter_id
        LEFT JOIN nutzer nf
            ON nf.nutzer_id = fs.fahrzeughalter_id
            AND nf.nutzerkonto_id = :nutzerkonto_id
    ORDER BY
        meins ASC
SQL;
    $stmt = oci_parse($c, $sql);
    oci_bind_by_name($stmt, ':nutzerkonto_id', $userData['nutzerkonto_id'], -1, SQLT_INT);
    oci_execute($stmt, OCI_DEFAULT);
    $lnCount = oci_fetch_all($stmt, $fahrzeuge, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
    return $fahrzeuge ?: [];
}

function getBuchung($userId){
    $c = getConn();
     $sql = "SELECT b.*, f.fahrzeug_bild FROM buchung b JOIN nutzer n ON b.fahrer_id = n.nutzer_id join fahrzeug f ON f.fahrzeug_id = b.fahrzeug_id WHERE n.nutzerkonto_id = :nutzerkonto_id order by buchung_id desc";
     $buchung = oci_parse($c, $sql);
    oci_bind_by_name($buchung, ':nutzerkonto_id', $userId, -1, SQLT_INT);
     oci_execute($buchung, OCI_DEFAULT);

     $throwAway = oci_fetch_all($buchung, $buchungen, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
     return $buchungen;
 }







 //Errores




?>