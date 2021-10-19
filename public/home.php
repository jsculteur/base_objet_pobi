<?php
    //Demarrage de la sessions utilisateur
    session_start();
    $titrepage = "Nom Page";
    require_once("../vendor/autoload.php");
    require_once("../templates/navbar.php");

    // Rediriger vers votre page
    // header("Refresh:0;url=chemin_vers_ma_page.php");