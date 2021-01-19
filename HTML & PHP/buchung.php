<?php
$titleAddition = 'Buchungen';
//include_once('Oracle_Conn.php');
include_once('header.php');
include_once('funktionen.php');
include_once('funktionenRechnung.php');

if (!array_key_exists('userId', $_SESSION) || $_SESSION['userId'] <= 0) {
        header('Location: home.php');
        die();
}

if($_GET){
  if(($_GET['aktion']) ==='beenden')
  {
    $b_id =buchungAktualisieren($_GET);
  }

  if(($_GET['aktion']) ==='bewerten')

  {
    echo " <hr></hr>B Bewerten" . $_GET['b_id'];
    $b_id =buchungBewerten($_GET);    
  }
}





if (isset($POST['beenden'])){
  //echo "<pre>";
 echo " <hr></hr>Buchung ID: " . $_GET["b_id"];
 $b_id = $_GET["b_id"];
}


?>
<main role="main">

  <section class="jumbotron text-center">
    <div class="container">
      <h1>Meine Buchungen</h1>
      <p>
       <!--  <a href="#" class="btn btn-primary my-2">Alle</a>
        <a href="#" class="btn btn-secondary my-2">Verfügbare</a>
        -->
      </p>
    </div>
  </section>

  <div class="album py-5 bg-light">
    <div class="container">
       <div class="row">
        <?php
          $buchungen = getBuchung($_SESSION['userId']);
          foreach ($buchungen as $index => $buchung):
          ?>

<div class="col-12 col-md-6 col-lg-4 mb-4" id="buchung-<?= $buchung['BUCHUNG_ID'] ?>">
       <div class="card" >
          <img width=100% src="<?php echo $buchung['FAHRZEUG_BILD'] ?>"/>
          <div class="card-body">
              <p class="card-text"><b>Buchung:</b> <?php echo $buchung['BUCHUNG_ID']?> </p>
              <p><b>Fahrer:</b> <?php echo $buchung['FAHRER_ID']?></p>
              <p><b>   Dauer:</b> <?php echo $buchung['BUCHUNG_DAUER']; ?> Minuten</p>
              <p><b>Fahrzeug:</b> <?php echo $buchung['FAHRZEUG_ID']?></p>
              <p class="card-text"><b>Start:</b> <?php echo $buchung['BUCHUNG_START']; ?> </p>
              <p class="card-text"><b>Ende:</b> <?php echo $buchung['BUCHUNG_ENDE']; ?></p>
              <p class="card-text"><b>Buchungspreis:</b> <?php echo $buchung['BUCHUNG_END_PREIS']; ?> EUR</p>
      
               <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <a type="button" class="btn btn-sm btn-outline-secondary <?= $buchung['BUCHUNG_STATUS'] === 'BEENDET' ? 'disabled' : '' ?>"
                  name="beenden" method="POST" value="beenden" href="buchung.php?b_id=<?php echo $buchung['BUCHUNG_ID']; ?>&aktion=beenden">Beenden</a>
                 </div>
                <small class="text-muted"><b>Status: </b><?php echo $buchung['BUCHUNG_STATUS']; ?> </small>
              </div>

          <!-- Bewertung  -->
              <a type="button" class="btn btn-sm btn-outline-secondary"  
                  name="bewerten" method="POST" value="bewerten" href="buchung.php?b_id=<?php echo $buchung['BUCHUNG_ID']; ?>&aktion=bewerten">Bewerten</a>    

                  <small class="text-muted"><b>Bewertung: </b><?php echo $buchung['BUCHUNG_BEWERTUNG']; ?> </small>

           <div class="mb-2" width="50">
            <label for="b_bewertung"><span class="text-muted"></span></label>
            <input name="b_bewertung" type="number" class="form-control" id="b_bewertung" placeholder="Bewertung" required>
            <!-- HIDDEN  -->

            <input type="hidden" name="b_bewertung" value="<?php echo $_GET['b_bewertung'] ?? '';?>">

            <div class="invalid-feedback">Please enter a valid price factor</div>
           </div>
          </div>
       </div>
</div>
          <?php endforeach; ?>
       
          </div>
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
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="../assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="../assets/dist/js/bootstrap.bundle.js"></script></body>
</html>
