<?php


if (!$titleAddition) {
    $titleAddition = '';
} else {
    $titleAddition = ' - ' . $titleAddition;
}
session_start();
include_once('funktionen.php');

$user = getUserData($_SESSION['userId']) ?: [];


//include_once('konto_zahlung.php');
?>
<!doctype html>
<html lang="en">
  <head>
   <script type = "text/javascript" src = "bootstrap.js" ></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.0.1">
    <title>Drive & Share<?= $titleAddition ?></title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/album/">

    <!-- Bootstrap core CSS -->
<link href="css/bootstrap.css" rel="stylesheet">
<nav class="site-header sticky-top py-1">
  <div class="container d-flex flex-column flex-md-row justify-content-between">
    <a class="py-2" href="index.php" aria-label="Product">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="d-block mx-auto" role="img" viewBox="0 0 24 24" focusable="false"><title>Product</title><circle cx="12" cy="12" r="10"/><path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83m13.79-4l-5.74 9.94"/></svg>
    </a>
   
 
<script>
/*
// Set the date we're counting down to
var countDownDate = new Date("Jun 22, 2020 08:55:00").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  // Find the distance between now and the count down date
  var distance = countDownDate - now;

  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Display the result in the element with id="demo"
  document.getElementById("demo").innerHTML = days + "d " + hours + "h "
  + minutes + "m " + seconds + "s " ;

  // If the count down is finished, write some text
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("demo").innerHTML = "EXPIRED";
  }
}, 1000);
*/
</script>


    <?php if (array_key_exists('userId', $_SESSION) && $_SESSION['userId'] > 0): ?>
      <a class="py-2 d-none d-md-inline-block"> <?php echo $user['VORNAME'] ?? '' ?></a>


    <a class="py-2 d-none d-md-inline-block" href="./konto_zahlung.php">Konto </a>
   
    <a class="py-2 d-none d-md-inline-block" href="./fahrzeug.php">Fahrzeug</a>
    <a class="py-2 d-none d-md-inline-block" href="./buchung.php">Buchungen</a>    
    <a class="py-2 d-none d-md-inline-block" href="./rechnung.php">Rechnungen</a>
    <a class="py-2 d-none d-md-inline-block">Summe aller Rechnungen: <?php echo $user['SALDO'] ?? '' ?> EUR</a>
    <?php endif; ?>

    <?php if (array_key_exists('userId', $_SESSION) && $_SESSION['userId'] > 0): ?>
    <a class="py-2 d-none d-md-inline-block" href="./logout.php">Logout</a>
    <?php endif; ?>
  </div>
</nav>


<p   id="demo"></p>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="globalCSS.css" rel="stylesheet">
  </head>
<body>