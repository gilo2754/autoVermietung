<?php
$titleAddition = 'Anmelden';
//include_once('Oracle_Conn.php');
include_once('header.php');
include_once('funktionen.php');
$basePath = '/drive&share';

if (isset($_POST['submit'])){
$userName = $_POST['email'];
$password = $_POST['password'];
 $userId = login($userName, $password);

 if ($userId > 0) {
    $_SESSION['userId'] = $userId;
 }
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
                <form name="formular" class="needs-validation" novalidate action="index.php"      method="POST">
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
                              <div class="invalid-feedback">
                                Please enter a valid password
                              </div>
                            </div>
                      </div>  
                      
                      <button class="btn btn-primary btn-lg btn-block" type="submit"  value="submit" name="submit" >Anmelden</button>
              
                </form>
                <p class="mt-3">
                    <a href="<?= $basePath ?>/Register.php">
                    <b> Sie haben noch kein Konto? Hier können Sie sich registrieren!</b>
                    </a>
                </p>
                <hr>
              </div>
          </div>

       <!-- </div>   --> 
  
      </div>
  </div>  

  
  <!--<div class="product-device shadow-sm d-none d-md-block"></div>
  <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div> -->




<footer class="container py-5">
  <div class="row">

  </div>
 

</footer>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="../assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="../assets/dist/js/bootstrap.bundle.js"></script></body>

</html>
