<?php
    require_once("vendor/autoload.php");
    // Ajout de la classe 'RepertoireFonctions'
    use App\Service\RepertoireFonction;
    $fonctions = new RepertoireFonction();
	// Récupération du navigateur courant
	$browser = $fonctions->getBrowser();
	// Si le navigateur n'est pas Chrome ni Firefox, on bloque l'accès à l'outil
	if($browser['name'] != 'Google Chrome' && $browser['name'] != 'Mozilla Firefox') {
        echo '<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css" />';
        echo '<link rel="stylesheet" href="assets/css/style.css" />';
		echo '<p class="col-8 center alert alert-danger">Merci d\'utiliser Google CHROME pour pouvoir accèder à cette application !</p>';
	} else {
        header("Refresh:0;url=public/connexion.php");
    }
?>