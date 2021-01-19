<?php

$titleAddition = 'Rechnungen';
//include_once('Oracle_Conn.php');
include_once('funktionenRechnung.php');
include_once('header.php');

if (!array_key_exists('userId', $_SESSION) || $_SESSION['userId'] <= 0) {
        header('Location: home.php');
        die();
}

$basePath = '/drive&share';

if (array_key_exists('rechnung', $_GET)) {
    $success = rechnungBezahlen($_SESSION['userId'], $_GET['rechnung']);
}

$billings = getRechnung($_SESSION['userId']);
?>

<div class="container">
    <h2 class="mb-4" >Ihre Rechnungen</h2>
    <div class="row">
        <?php foreach($billings as $bill): ?>
        <div class="col-12 col-md-6 mb-4" >
            <div class="card" >
                <div class="card-body" >
                    <?php $bill = (object)$bill; ?>
                    <h5 class="card-title"><?= $bill->RECHNUNG_BEZEICHNUNG ?? 'Ihre Rechnung'?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?='Ihre Rechnung vom ' . ($bill->RECHNUNG_DATUM ?? 'dd.mm.yy')?></h6>
                    <p></p>
                    <p class="card-text"><?= 'Aktueller Rechnungsstatus: ' . $bill->RECHNUNG_STATUS; ?></p>
                    <p><?= 'Rechnungsbetrag: ' . $bill->ENDBETRAG . '€'; ?></p>
                    <a class="btn btn-primary"
                       href="<?= $basePath ?>/buchung.php#buchung-<?= $bill->BUCHUNG_ID ?>">
                        Zur Buchung
                    </a>
                    <?php if($bill->RECHNUNG_STATUS === 'BEZAHLT'): ?>
                        <a class="btn btn-primary disabled"
                           href="#">
                            Bereits bezahlt
                        </a>
                    <?php else: ?>
                        <a class="btn btn-danger"
                           href="<?= $basePath ?>/rechnung.php?rechnung=<?= $bill->RECHNUNG_ID ?>">
                            Jetzt abbuchen
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>