<?php
$titleAddition = 'Fahrzeuge';
//include_once('Oracle_Conn.php');
include_once('header.php');
include_once('funktionen.php');

if (!array_key_exists('userId', $_SESSION) || $_SESSION['userId'] <= 0) {
        header('Location: home.php');
        die();
}


if($_GET){
                if(($_GET['aktion']) ==='buchen')
                {
                  //echo " <hr></hr>Farhzeug BUCHEN GET f.PHP: " . $_GET['f_id'];
                  $fahrzeugF =fahrzeugBuchen($_SESSION['userId'], $_GET);
                }

                if(($_GET['aktion']) ==='loeschen')

                {
                  //echo " <hr></hr>Farhzeug wird GELÖSCHT" . $_GET['f_id'];
                  $fahrzeugF =fahrzeugLoeschen($_GET);
                }
        }

if (isset($_POST['add_Farhzeug'])){
          neuesFahrzeug($_POST);  
          //echo "Ein neues Fahrzeug wurde hinzugefügt";


        }      
/*
if (isset($POST["buchen"])){
 //echo "<pre>";
echo " <hr>Farhzeug POST: " . $fahrzeugF;
$fahrzeugF = $_GET["f_id"];
}
*/

?>


<html lang="en">

<main role="main">
  <section class="jumbotron text-center">
  


  <div>
    <form name="neueF" class="needs-validation" novalidate action="fahrzeug.php" 
      method="POST" <?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>>
        <div class="row">
           <div class="mb-3">
          <label for="marke"><span class="text-muted"></span></label>
          <input name="marke" type="text" class="form-control" id="marke" placeholder="Marke" required>
          <div class="invalid-feedback">
            Please enter a valid branch address for shipping updates.
          </div>
        </div>

        <div class="mb-3">
          <label for="fPreis"><span class="text-muted"></span></label>
          <input name="fPreis" type="number" class="form-control" id="fPreis" placeholder="Faktor Preis" required>
          <div class="invalid-feedback">
            Please enter a valid price factor
          </div>
        </div>

        <div class="mb-3">
          <label for="fKennzeichen"><span class="text-muted"></span></label>
          <input name="fKennzeichen" type="text" class="form-control" id="fKennzeichen" placeholder="FFZ-KENNZEICHEN" required>
          <div class="invalid-feedback">
            Please enter a valid NUMBER factor
          </div>

          <div class="mb-3">
          <button  type="submit" class="btn btn-dark form-control" 
                  value="add_Farhzeug" name="add_Farhzeug" id="add_Farhzeug">Hinzufügen</button>
                  </div> 
        </div> 


      </form>
      </div>




        <!-- Zeige nur verfuegbare Fahrzeuge
        <a name="status" href="fahrzeug.php?f_status=   ?php echo $fahrzeug['FAHRZEUG_ID']; ?>"
         action="fahrzeug.php" class="btn btn-secondary my-2" method="POST" value="avaliable">Verfügbare</a> -->

    </div>
  </section>

  <div class="album py-5 bg-light container">
    <div class="row">

    <?php $fahrzeuge = getFahrzeug($_SESSION['userId']);
    foreach ($fahrzeuge as $fahrzeug): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card mb-4 shadow-sm">
            <img style="width: 100%" src="<?php echo $fahrzeug['FAHRZEUG_BILD']; ?>"/>
            <div class="card-body">
              <p class="card-text"> <?php echo $fahrzeug['FAHRZEUG_MARKE']; ?></p>

              <p class="card-text" > <b>Fahrzeug</b> <?php echo $fahrzeug['FAHRZEUG_ID']; ?> </p>
              <p class="card-text" > <b>KENNZEICHEN:</b>  <?php echo $fahrzeug['FAHRZEUGKENNZEICHEN']; ?></p>

              <p name="f_status" class="text-muted"><?php echo $fahrzeug['FAHRZEUG_STATUS']; ?></p>
              <p name="f_status" class="text-muted"><b>Faktor-Preis:</b>  <?php echo $fahrzeug['FAHRZEUG_PREIS_FAKTOR']; ?> EUR</p>

              <div class="btn-group" >
                  <a type="button" class="btn btn-success <?= $fahrzeug['MEINS'] !== null || $fahrzeug['FAHRZEUG_STATUS'] === 'RESERVIERT' ? 'disabled' : '' ?>" method="POST"
                  value="buchen" name="buchen"
                  href="fahrzeug.php?f_id=<?php echo $fahrzeug['FAHRZEUG_ID']; ?>&aktion=buchen"
                   > Buchen</a>
               <a type="button" class="btn btn-danger <?= $fahrzeug['MEINS'] !== null || $fahrzeug['FAHRZEUG_STATUS'] === 'RESERVIERT'  ? 'disabled' : '' ?>" method="POST"
                  value="loeschen" name="loeschen"  href="fahrzeug.php?f_id=<?php echo $fahrzeug['FAHRZEUG_ID']; ?>&aktion=loeschen">Löschen</a>
             </div>


            </div>
           </div>
          </div>


          <?php endforeach; ?>
    
  </div>
  </div>


</main>

<footer class="text-muted">
  <div class="container">
    <p class="float-right">
      <a href="#">Back to top</a>
    </p>
    
  </div>
</footer>
<script src="http://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
      
      <script src="../assets/dist/js/bootstrap.bundle.js"></script>
      
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 


      </body>
</html>
