<?php
    session_name("some_session_name");
	session_set_cookie_params(0, '/', '.pobi.dom');

    //Demarrage de la sessions utilisateur
    session_start();
    //On augmente la valeur du maximum execution
    ini_set('max_execution_time', 1000);
    date_default_timezone_set('Europe/Paris');
    
    $titrepage = "Accueil";

    require_once("../vendor/autoload.php");
    require_once("../templates/functions.inc.php");
    require_once("../templates/navbar.php");
    require_once("../classes/password/passwordLib.php");


    use App\Service\MySqlConnection;
    use App\Service\RepertoireFonction;
    use App\Service\InformixConnection;

    $connectionMySql = new MySqlConnection();
    $connectionSystem = new InformixConnection("system");
    $fonctions = new RepertoireFonction();
    
    if(empty($_SESSION["connecte"]) || $_SESSION["connecte"] !== true ) {
        if(!empty($_POST["identifiant"])) {
            //On explose l'identifiant en fonction de _
            $tableauIdentifiant = explode("_",$_POST["identifiant"]);

            //Si il y a bien quelque chose en deuxieme position du tableau
			if(isset($tableauIdentifiant[1])){
				$identifiant = trim($tableauIdentifiant[1]);
			}else{
				$identifiant = "";
			}
			
			//On recupere le mot de passe
			$mdpForm = $_POST["mdpForm"];

            //On vérifie que l'identifiant existe bien
			$connexion = $connectionSystem->prepare("SELECT COUNT(*) FROM adm_user WHERE nom_user = ?");
            $connexion->execute(array(strtoupper($identifiant)));
			$nbResult = intval($connexion->fetch(PDO::FETCH_COLUMN));

            //Si il existe
			if($nbResult != 0) {
                //On recupere le mot de passe
				$mdp = $connectionSystem->prepare("SELECT cns2, cns3, inactif FROM adm_user WHERE nom_user = ?");
                $mdp->execute(array(strtoupper($identifiant)));
				$mdpBdd = $mdp->fetch(PDO::FETCH_ASSOC);
        
                //On vérifie le mot de passe
				if (password_verify($mdpForm, $mdpBdd["CNS2"].$mdpBdd["CNS3"]) && $mdpBdd["INACTIF"] != "désactivé") {
                    $_SESSION["connecte"] = true;
					$_SESSION["pseudo"] = trim(strtoupper($identifiant));

                    $notif = $fonctions->notifier("Connexion réussie !");
                    echo $notif;

                } else {
                    if($mdpBdd["INACTIF"] == "désactivé"){
                        $erreur = $fonctions->notifier("Le compte est désactivé !");
					} else {
                        $erreur = $fonctions->notifier("Le couple login/mot de passe est incorrect !");
					}
                    echo $erreur;
                }
            } else {
                $erreur = $fonctions->notifier("Identifiant incorrect");
                echo $erreur;
            }
        }
        echo 
            '<div class="formulaire jumbotron col-6 center">' . 
                '<h3>Connexion</h3>' . 
                '<div class="form-group">' . 
                    '<div class="input-prepend">' . 
                        '<div class="input-group mb-2">' . 
                            '<div class="input-group-prepend">' . 
                                '<div class="input-group-text"><i class="fa fa-user"></i></div>' . 
                            '</div>' . 
                            '<input type="text" class="form-control" id="user-sylob" placeholder="Identifiant">' . 
                        '</div>' . 
                    '</div>' . 
                '</div>' . 
                '<div class="form-group">' . 
                    '<div class="input-prepend">' . 
                        '<div class="input-group mb-2">' . 
                            '<div class="input-group-prepend">' . 
                                '<div class="input-group-text"><i class="fa fa-key"></i></div>' . 
                            '</div>' . 
                            '<input type="password" class="form-control" id="mdp-sylob" placeholder="Mot de passe">' . 
                        '</div>' . 
                    '</div>' . 
                '</div>' . 
                '<input id="connexion-operateur" type="button" class="btn btn-primary" name="connexion" value="Connexion">' . 
            '</div>' . 
            '<br clear="all" />'
        ;
    } else {
		header("Refresh:0;url=home.php");
    }
