<?php
	//Variable qui permet de mettre la bonne heure lors de l'utilisation de la fonction date ();
	date_default_timezone_set('Europe/Paris');
?>

<!DOCTYPE html>

<html lang="fr-FR">

<head>
		
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"> 
	<meta name="viewport" content="width=device-width" />
	<!-- $titrepage contient le titre de la page, renvoyé par la page précédent l'appel de ce fichier -->
	<title>BL Complémentaire Sylob - <?php echo $titrepage; ?></title>
	<!-- Favicon -->
	<link rel="icon" href="assets/img/favicon.png" />
	<!-- Style -->
	<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
	<link rel="stylesheet" href="node_modules/font-awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css" />
	<!-- JS -->
	<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Librairie momentjs pour formater les dates plus facilement -->
	<script type="text/javascript" src="node_modules/moment/min/moment-with-locales.min.js"></script>
	<script type="text/javascript" src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
	<!-- JS spécifique -->
	<script type="text/javascript" src="assets/js/app.js"></script>
	
</head>
<body id="body" class="center">
<?php
	// include("../includes/logout.php");
?>