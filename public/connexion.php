<?php

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
    use App\Service\InformixConnection;

    $connectionInformix = new InformixConnection("socpobi99_precix");
    $connectionMySql = new MySqlConnection();
    $baseInformix = $connectionInformix->getConnection();
    $baseUtilisateurs = $connectionMySql->getConnectionUtilisateurs();
    $baseDG = $connectionMySql->getConnectionDG();
    $login = connexionWindows();
    $codeOutil = "BPSYLOB";
    $sqlIdOutil = $baseUtilisateurs->prepare("SELECT idOutil FROM outil WHERE codeOutil = ?");
    $sqlIdOutil->execute(array($codeOutil));
    $idOutil = $sqlIdOutil->fetch(PDO::FETCH_COLUMN);
    //Si le login n'est pas vide on regarde si il est dans l'active directory
	if(!empty($login)) {
		$ad = connexionAD($login);
		$testLogin = "true";
	} else {
		$testLogin = "false";	
	}

    if(empty($_SESSION["connecte"]) || $_SESSION["connecte"] !== true) {
        if(!empty($_POST["identifiant_form"]) && !empty($_POST["mot_de_passe_form"])) {
            $identifiant = trim($_POST["identifiant_form"]);

			$sqlConnection = $baseUtilisateurs->prepare("SELECT idUt, login, password, validite, idTypeUt FROM utilisateur WHERE login = ?");
            $sqlConnection->execute(array($identifiant));
			$resultConnexion = $sqlConnection->fetch(PDO::FETCH_ASSOC);

            if(password_verify($_POST["mot_de_passe_form"], $resultConnexion["password"])) {
                $_SESSION["connecte"] = true;
				$_SESSION["idUt"] = $resultConnexion["idUt"];
				$_SESSION["pseudo"] = $resultConnexion["login"];

                //On regarde si il est su ou non
				$sqlAdmin = $baseUtilisateurs->prepare("SELECT * FROM vue_administrateur WHERE Pseudo = ?");
                $sqlAdmin->execute(array($_SESSION["pseudo"]));
                $estAdmin = $sqlAdmin->fetch(PDO::FETCH_ASSOC);
                if ($estAdmin === false) {
					$_SESSION["su"] = false; // su pour Super User, soit administrateur.
				} else {
					if (!empty($estAdmin["Pseudo"]) && $estAdmin["Pseudo"] === $_SESSION["pseudo"]) { // ce qui est le cas d'après la requete même
						$_SESSION["su"] = true;
					}
				}
                notifier('Connexion réussie !');
                // header("refresh:3;");
                header("refresh:3;url=home.php");
            }
        } else if(!empty($login) && $testLogin == "true" && !isset($_POST["enregLoginAd"]) && empty($_POST["logout"])) {
            $loginClause = strtolower($login);
            $testUtilisateur = $baseUtilisateurs->prepare("SELECT * FROM utilisateur INNER JOIN tj_outil_ut ON tj_outil_ut.idUt = utilisateur.idUt	INNER JOIN outil ON tj_outil_ut.idOutil = outil.idOutil	WHERE login = ? AND codeOutil = ?");
            $testUtilisateur->execute(array($loginClause, $codeOutil));

			$nbRetour = $testUtilisateur->rowCount();
            //Si la personne fait bien parti de notre base de données utilisateur alors on la connecte en automatique
			if($nbRetour != 0) {
                $resultConnexion = $testUtilisateur->fetch(PDO::FETCH_ASSOC);
                $_SESSION["connecte"] = true;
				$_SESSION["idUt"] = $resultConnexion["idUt"];
				$_SESSION["pseudo"] = $resultConnexion["login"];
				$_SESSION["codeOutil"] = $codeOutil;

                //On regarde si il est su ou non
				$sqlestAdmin = $baseUtilisateurs->prepare("SELECT * FROM vue_administrateur WHERE Pseudo = ?");
                $sqlestAdmin->execute(array($_SESSION["pseudo"]));
                $estAdmin = $sqlestAdmin->fetch(PDO::FETCH_ASSOC);
				if ($estAdmin === false) {
					$_SESSION["su"] = false; // su pour Super User, soit administrateur.
				} else {
					if (!empty($estAdmin["Pseudo"]) && $estAdmin["Pseudo"] === $_SESSION["pseudo"]) { // ce qui est le cas d'après la requete même
						$_SESSION["su"] = true;
					}
				}
                notifier('Connexion réussie !');
                header("refresh:3;url=home.php");
            } else {
				notifier('Vous n\'avez pas accès à cet outil', false);
			}
        } else {
            if(!empty($_POST["connexion"])) {
				$message_erreur = 'Pour vous connecter :';
				if (empty($_POST["identifiant_form"])) {
					$message_erreur .= 'Veuillez préciser un identifiant. <br />';
				}
				if (empty($_POST["mot_de_passe_form"])) {
					$message_erreur .= 'Veuillez préciser un mot de passe.';
				}
				notifier($message_erreur, false);
			}
            if(!isset($_SESSION["connecte"])) {
                echo 
                    '<form action="index.php" method="post">' . 
                        '<div class="formulaire jumbotron col-6 center">' . 
                            '<h1 class="display-4">Connexion</h1>' . 
                            '<div class="form-group">' . 
                                '<div class="input-prepend">' . 
                                    '<div class="input-group mb-2">' . 
                                        '<div class="input-group-prepend">' . 
                                            '<div class="input-group-text"><i class="fa fa-user"></i></div>' . 
                                        '</div>' . 
                                        '<input type="text" class="form-control" name="identifiant_form" id="identifiant_form" placeholder="Identifiant">' . 
                                    '</div>' . 
                                '</div>' . 
                            '</div>' . 
                            '<div class="form-group">' . 
                                '<div class="input-prepend">' . 
                                    '<div class="input-group mb-2">' . 
                                        '<div class="input-group-prepend">' . 
                                            '<div class="input-group-text"><i class="fa fa-key"></i></div>' . 
                                        '</div>' . 
                                        '<input type="password" class="form-control" id="mot_de_passe_form" name="mot_de_passe_form" placeholder="Mot de passe">' . 
                                    '</div>' . 
                                '</div>' . 
                            '</div>' . 
                            '<input type="submit" class="btn btn-primary" name="connexion" value="Connexion">' . 
                        '</div>' . 
                    '</form>' . 
                    '<br clear="all" />'
                ;
            }
        }
    } else {
        header("refresh:0;url=home.php");
    }