<?php
$titleAddition = 'Nutzerkonto';
//include_once('Oracle_Conn.php');
include_once('header.php');
include_once('funktionen.php');
include_once('funktionenRechnung.php');

if (!array_key_exists('userId', $_SESSION) || $_SESSION['userId'] <= 0) {
        header('Location: index.php');
        die();
}


if (array_key_exists('submit', $_POST)) {
    $userData = [
        "nutzerkonto_id" => $_SESSION['userId'],
        "vorname" => $_POST['firstName'] ?? null,
        "nachname" => $_POST['lastName'] ?? null,
        "nutzername" => $_POST['nutzername'] ?? null,
        "mobilnummer" => $_POST['handynummer'] ?? null,
        "hausnummer" => $_POST['housenumber'] ?? null,
        "stadt" => $_POST['city'] ?? null,
        "land" => $_POST['country'] ?? null,
        "strasse" => $_POST['street'] ?? null,
        "zip" => $_POST['zip'] ?? null,
    ];
    $status = setUserData($userData);
}
$user = getUserData($_SESSION['userId']) ?: [];

?>
    <div class="container">
  <div class="row">
    
    <div class="col-md-10 order-md-1">
      <h4 class="mb-3">Persönliche Informationen</h4>
      <form name="formular" class="needs-validation" novalidate action="konto_zahlung.php"
      method="POST">
        <div class="row">
           
            
        <div class="col-12 mb-3">
          <label for="email">E-Mail Adresse<span class="text-muted"></span></label>
          <input name="email" type="email" class="form-control" id="email" placeholder="you@example.com" required
                 value="<?= $user['E_MAIL_ADRESSE'] ?? '' ?>">
          <div class="invalid-feedback">
            Please enter a valid email address for shipping updates.
          </div>
        </div>
           
          <div class="col-md-6 mb-3">
            <label for="firstName">Vorname</label>
            <input type="text" class="form-control" name="firstName" id="firstName" placeholder=""
                   value="<?= $user['VORNAME'] ?? '' ?>" required>
            <div class="invalid-feedback">
              Valid first name is required.
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="lastName">Nachname</label>
            <input name="lastName" type="text" class="form-control" id="lastName" placeholder=""
                   value="<?= $user['NACHNAME'] ?? '' ?>" not required>
            <div class="invalid-feedback">
              Valid last name is required.
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label for="nutzername">Nutzername</label>
            <input name="nutzername" type="text" class="form-control"
                   value="<?= $user['NUTZERNAME'] ?? '' ?>" id="nutzername" placeholder="Nutzername" required>
            <div class="invalid-feedback" style="width: 100%;">
              Your username is required.
            </div>
        </div>
         
        <div class="mb-3">
          <label for="handynummer">Handynummer<span class="text-muted"></span></label>
          <input name="handynummer" type="tel" class="form-control"
                 value="<?= $user['MOBILNUMMER'] ?? '' ?>" id="handynummer" placeholder="">
          <div class="invalid-feedback">
            Please enter a valid phone number
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 mb-3">
            <label for="street">Straße</label>
          <input name="street" type="text" class="form-control"
                 value="<?= $user['STRASSE'] ?? '' ?>" id="street" placeholder="" required>
        </div>

          <div class="col-md-4 mb-3">
          <label for="housenumber">Hausnummer<span class="text-muted"></span></label>
          <input name="housenumber" type="number" class="form-control"
                 value="<?= $user['HAUSNUMMER'] ?? '' ?>" id="housenumber" placeholder="" required>
          <div class="invalid-feedback">
            Please enter your street.
          </div>
        </div>
      </div>      
       

        <div class="row">
          <div class="col-md-5 mb-3">
            <label for="country">Land</label>
            <select name="country" class="custom-select d-block w-100" id="country" required>
              <option value="">Land...</option>
              <option <?= array_key_exists('LAND', $user) && $user['LAND'] === 'USA' ? 'selected' : ''?>>USA</option>
              <option <?= array_key_exists('LAND', $user) && $user['LAND'] === 'Deutschland' ? 'selected' : ''?>>Deutschland</option>
              <option <?= array_key_exists('LAND', $user) && $user['LAND'] === 'Spanien' ? 'selected' : ''?>>Spanien</option>
              <option <?= array_key_exists('LAND', $user) && $user['LAND'] === 'Russland' ? 'selected' : ''?>>Russland</option>
              <option <?= array_key_exists('LAND', $user) && $user['LAND'] === 'Italien' ? 'selected' : ''?>>Italien</option>
            </select>
            <div class="invalid-feedback">
              Please select a valid country.
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="state">Ort</label>
            <select name="city" class="custom-select d-block w-100" id="city" required>
              <option value="">Choose...</option>
              <option value="Dinkelsbuehl" <?= array_key_exists('STADT', $user) && $user['STADT'] === 'Dinkelsbuehl' ? 'selected' : ''?>>Dinkelsbühl</option>
              <option value="Berlin" <?= array_key_exists('STADT', $user) && $user['STADT'] === 'Berlin' ? 'selected' : ''?>>Berlin</option>
              <option value="Koeln" <?= array_key_exists('STADT', $user) && $user['STADT'] === 'Koeln' ? 'selected' : ''?>>Köln</option>
              <option value="Wacken" <?= array_key_exists('STADT', $user) && $user['STADT'] === 'Wacken' ? 'selected' : ''?>>Wacken</option>
              <option value="Hamburg" <?= array_key_exists('STADT', $user) && $user['STADT'] === 'Hamburg' ? 'selected' : ''?>>Hamburg</option>
            </select>
            <div class="invalid-feedback">
              Please provide a valid state.
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <label for="zip">PLZ</label>
            <input name="zip" type="text" class="form-control"
                   value="<?= $user['ZIP'] ?? '' ?>" id="zip" placeholder="" required>
            <div class="invalid-feedback">
              Zip code required.
            </div>
          </div>
        </div>
        <hr class="mb-4">
       
      <?php
      /*
        <h4 class="mb-3">Zahlungsmethode</h4>
        <small class="text-muted">Wählen Sie Ihre bevorzugte Zahlungsmethode</small>

        <div class="d-block my-3">
          <div class="custom-control custom-radio">
            <input id="paypal" name="paymentMethod" type="radio" class="custom-control-input" required>
            <label class="custom-control-label" for="paypal">PayPal</label>
          </div>
          <div class="col-md-6 mb-3">
            <label for="paypal"></label>
            <input type="text" class="form-control" id="paypal" placeholder="@" required>
            <div class="invalid-feedback">
              E-Mail Adresse ausfüllen
            </div>
          </div>

          <div class="custom-control custom-radio">
            <input id="debit" name="paymentMethod" type="radio" class="custom-control-input" >
            <label class="custom-control-label" for="debit">Lastschrift</label>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="cc-name">IBAN</label>
            <input type="text" class="form-control" id="cc-name" placeholder="" >
            <div class="invalid-feedback">
              Name on card is required
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="bankname">Bankname</label>
            <input type="text" class="form-control" id="bankname" placeholder="" >
            <div class="invalid-feedback">
              Bankname eingeben
            </div>
          </div>
        </div>
       
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="bic">BIC</label>
            <input type="text" class="form-control" id="bic" placeholder="" >
            <div class="invalid-feedback">
              BIC ergänzen
            </div>
          </div>
        </div>
        <hr class="mb-4">
        */
        ?>
        <button class="btn btn-primary btn-lg btn-block" type="submit"  
        value="submit" name="submit" >Speichern</button>
      </form>
    </div>
  </div>



  <footer class="my-5 pt-5 text-muted text-center text-small">
   
  </footer>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="../assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="../assets/dist/js/bootstrap.bundle.js"></script>
        <script src="form-validation.js"></script>
          
      
      
      
      </body>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="../assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="../assets/dist/js/bootstrap.bundle.js"></script></body>
</html>
