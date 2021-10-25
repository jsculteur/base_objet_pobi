<?php
	//Variable qui permet de mettre la bonne heure lors de l'utilisation de la fonction date ();
	date_default_timezone_set('Europe/Paris');
	// Definition de la variable $nomAppli qui sera répercutée partout
	$nomAppli = "Visualisation des OF"
?>

<!DOCTYPE html>

<html lang="fr-FR">

<head>
		
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"> 
	<meta name="viewport" content="width=device-width" />
	<!-- $titrepage contient le titre de la page, renvoyé par la page qui appelle ce fichier -->
	<title><?php echo $nomAppli." - ".$titrepage; ?></title>
	<!-- Favicon -->
	<link rel="icon" href="../assets/img/favicon.png" />
	<!-- Style -->
	<link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../classes/jquery-ui-1.13.0/jquery-ui.min.css" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/copyToClipboard.css" />
    <link rel="stylesheet" href="../assets/css/visualisationOf.css" />
	<link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css" />
	<link rel="stylesheet" href="../node_modules/font-awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" href="../classes/datatables/datatables.min.css" />
	<!-- Bootstrap 4 -->
	<!-- <link rel="stylesheet" href="../classes/datatables/Responsive-2.2.9/css/responsive.bootstrap4.min.css" /> -->
	<!-- Bootstrap 5 -->
	<link rel="stylesheet" href="../classes/datatables/Responsive-2.2.9/css/responsive.bootstrap5.min.css" />
	<!-- JS -->
	<script type="text/javascript" src="../node_modules/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="../classes/jquery-ui-1.13.0/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" src="../classes/datatables/datatables.min.js"></script>
	<script type="text/javascript" src="../classes/datatables/Responsive-2.2.9/js/dataTables.responsive.min.js"></script>
	
	<!-- Librairie momentjs pour formater les dates plus facilement -->
	<script type="text/javascript" src="../node_modules/moment/min/moment-with-locales.min.js"></script>
	<script type="text/javascript" src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>

	<!-- JS utilisateur -->
	<!-- Le type est "module" car il permet d'inclure d'autres scripts -->
	<script type="module" src="../assets/js/app.js"></script>
	
</head>
<body id="body">