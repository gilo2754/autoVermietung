<?php
$titleAddition = 'Registrieren';
//include_once('Oracle_Conn.php');
include_once('header.php');
include_once('funktionen.php');

/*
check_Session(); //

if( $_SERVER ['REQUEST_METHOD'] =='POST' ){
  $n_email = filter_var(($_POST ['email']), FILTER_SANITIZE_STRING); //NO SQL-INJEKTION
  $n_pss = $_POST ['password'];
  echo "$n_email . $n_pss";  


  $c = getConn();

 } 
 */


/*
 $stmt = $c->prepare ('SELECT * FROM NUTZERKONTO WHERE E_MAIL_ADRESSE=:email LIMIT 1')
 $stmt->execute(array(':email'=>$n_email));
 $result = $stmt->fetch();
if($result){
  echo "$n_email . ya existe";
}
*/

$success = null;
if (isset($_POST['submit'])){
 // echo "<pre>";
 // print_r($_POST);die;
 $success = neueNutzerkonto($_POST);

 $userId = login($_POST['email'], $_POST['password']);
 $_SESSION['userId'] = $userId;
}

if ($_SESSION['userId'] > 0) {
    header('Location: konto_zahlung.php');
    die();
}

if (array_key_exists('userId', $_SESSION) && $_SESSION['userId'] > 0) {
    header('Location: konto_zahlung.php');
    die();
}
?>
<html lang="en">
<head>
<link rel="stylesheet" href="css/globalCSS.css" type="text/css">
</head>


<div class="container">
  <div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
            <img style="width: 100%" src="<?php echo "https://images.unsplash.com/photo-1529369623266-f5264b696110?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=667&q=80"; ?>"/> 
        <div class="col-md-5 p-lg-5 mx-auto my-5 centered">
           <h1 class="display-4 font-weight-normal">Drive & Share</h1>

    
              <div>
                  <form name="formular" class="needs-validation" novalidate action="Register.php"
                    method="POST" <?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>>
                    <div class="row center-hor">
                                <div class="mb-3">
                                <label for="email"><span class="text-muted"></span></label>
                                <input name="email" type="email" class="form-control" id="email" placeholder="E-Mail Adresse" required>
                                <div class="invalid-feedback">
                                  Please enter a valid email address for shipping updates.
                                </div>
                              </div>

                              <div class="mb-3">
                                <label for="password"><span class="text-muted"></span></label>
                                <input name="password" type="password" class="form-control" id="password" placeholder="Passwort" required>
                              </div>

                              <?php if ($success !== null && is_string($success)): ?>
                                  <div class="alert alert-danger" role="alert">
                                      <?php preg_match_all ("/[\w\s\(\)]+?\:\s*ORA\-(\d+)/", $success, $matches);?>
                                      <?php $error = $matches[1][0] ?? '500' ?>
                                      <?php
                                          switch($error) {
                                              case '20002':
                                                  echo 'Passwort zu kurz!';
                                                  break;
                                              case '20003':
                                                  echo 'Das Passwort muss ein Sonderzeichen enthalten!';
                                                  break;
                                              case '20004':
                                                  echo 'Das Passwort muss einen Gro&szlig;buchstaben enthalten!';
                                                  break;
                                              case '20005':
                                                  echo 'Das Passwort muss eine Zahl enthalten!';
                                                  break;
                                                  case '01400':
                                                  echo 'Es muss eine E-Mail-Adresse enthalten sein!';
                                                  break;
                                                  case '20006':
                                                    echo 'Das Passwort ist zu lang!';
                                                    break;
                                                    case '28231':
                                                      echo 'Sie muessen etwas eingeben!';
                                                      break;
                                              default:
                                                  echo 'Unbekannter Fehler: ' . $error;
                                                  echo $success;
                                          } ?>
                                  </div>
                              <?php elseif ($success !== null): ?>
                                  <div class="alert alert-success" role="alert">Sie sind registriert</div>
                              <?php endif; ?>
                              <button class="btn btn-primary btn-lg btn-block" type="submit"  
                              value="submit" name="submit"  >Registrieren</button>
                       </div>
                    </form>
                </div>
         </div>
    </div>
  

</div>




    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous" />
      <script>
        window.jQuery || document.write('<script src="../assets/js/vendor/jquery.slim.min.js"><\/script>');
      </script>
      <script src="../assets/dist/js/bootstrap.bundle.js" />
      </body>
</html>
