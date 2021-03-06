<?php
    // session_name("some_session_name");
	// session_set_cookie_params(0, '/', '.pobi.dom');

    //Demarrage de la sessions utilisateur
    session_start();
	
    //On augmente la valeur du maximum execution
    ini_set('max_execution_time', 1000);
    date_default_timezone_set('Europe/Paris');
    
    $titrepage = "Accueil";

    require_once("../vendor/autoload.php");
    require_once("../templates/functions.inc.php");

	use App\Service\RepertoireFonction;
    use App\Service\MySqlConnection;
    use App\Service\InformixConnection;
	
    
	$fonctions = new RepertoireFonction();
	if(isset($_POST["logout"]) && $_POST["logout"] == "true") {
		$_SESSION["connecte"] = false;
		session_destroy();
		$fonctions->notifier("Déconnexion réussie");
		header("Refresh:0; url=connexion.php?deconnexion=true");
	}
	
    require_once("../templates/navbar.php");
    require_once("../classes/password/passwordLib.php");
    require_once("../classes/detectmobile/Mobile_Detect.php");

    $mySql = new MySqlConnection();
    $bdd = $mySql->getConnectionStock();
    $informix = new InformixConnection("system");
	$bddSystem = $informix->getConnection();
	$structures = new InformixConnection("socpobi_precix");
	$bddStructures = $structures->getConnection();

    //Recuperation user agent
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	
	//Nou sexplosons celui-ci
	$explodeUserAgent = explode("; ", $userAgent);
	
	//Si nous arrivons a recuperer le type de mobile
    if(count($explodeUserAgent) >= 3) {
		$mobileType = $explodeUserAgent[2];
	} else {
		$mobileType = "";
	}
	
    $device = "other";
	//Si le device comme par TC alors zebra
    if(substr($mobileType, 0, 2) == "TC") {
        $device = "zebra";
    }
	
	$detect = new Mobile_Detect;	
	//Si nous recuperons quelque chose dans le login --> test autoconnect	
	if((!isset($_SESSION["connecte"]) || $_SESSION["connecte"] == false) && !isset($_POST["logout"]) && !isset($_GET["deconnexion"])) {
		if(($detect->isMobile() == false && $device != "zebra")){
			$login = connexionWindows();
		}else{
			//Si nous detectons que nous sommes sur un zebra nous lancons le login à vide comme firefox
			$login = "";
		}
			
		$ad = connexionAD($login);
		//Si nous arrivons a recupere le login windows
		if(!empty($ad)) {
			$_SESSION["connecte"] = true;
			$_SESSION["pseudo"] = $login;
			//On regarde si la personne connectée fait partie de ceux pouvant voir le bouton administration
			$testAdmin = $bdd->prepare("SELECT COUNT(*) FROM admin_appli WHERE login = ?");
            $testAdmin->execute(array($login));
			$nbTestAdmin = intval($testAdmin->fetch(PDO::FETCH_COLUMN));
			if($nbTestAdmin != 0) {
				$_SESSION["su"] = true;
			} else {
				$_SESSION["su"] = false;						
			}
		}
		
	//Si nous avons envoyé le formulaire de connexion (zebra et firefox)	
	} else if(isset($_POST["login"])) {
		
		// On explose l'identifiant en fonction de _
		$tableauIdentifiant = explode("_",$_POST["login"]);
		// Si il y a bien quelque chose en deuxieme position du tableau
		if(isset($tableauIdentifiant[1])) {
			$identifiant = trim($tableauIdentifiant[1]);
			// On recupere le mot de passe		
			$mdpForm = $_POST["password"];
			// On vérifie que l'identifiant existe bien
			$connexion = $bddSystem->prepare("SELECT COUNT(*) FROM adm_user WHERE nom_user = ?");
            $connexion->execute(array(trim(strtoupper($identifiant))));
			$nbResult = intval($connexion->fetch(PDO::FETCH_COLUMN));
			// Si il existe
			if($nbResult != 0) {
				// On recupere le mot de passe
				$mdp = $bddStructures->prepare("SELECT cns2, cns3 FROM informix.zz_adm_user_mdp WHERE nom_user =  ?");
				$mdp->execute(array(trim(strtoupper($identifiant))));
				$mdpBdd = $mdp->fetch(PDO::FETCH_ASSOC);
				$inactive = $bddSystem->prepare("SELECT inactif FROM adm_user WHERE nom_user = ?");
				$inactive->execute(array(trim(strtoupper($identifiant))));
				$inactif = $inactive->fetch(PDO::FETCH_COLUMN);
				// On vérifie le mot de passe
				if(password_verify($mdpForm, $mdpBdd["CNS2"].$mdpBdd["CNS3"]) && $inactif != "désactivé") {
					$_SESSION["connecte"] = true;
					$_SESSION["pseudo"] = $identifiant;
					//On regarde si la personne connecté fait partie de ceux pouvant voir le bouton administration
					$testAdmin = $bdd->prepare("SELECT COUNT(*) FROM admin_appli WHERE login = ?");
                    $testAdmin->execute(array($identifiant));
					$nbTestAdmin = intval($testAdmin->fetch(PDO::FETCH_COLUMN));
					if($nbTestAdmin != 0) {
						$_SESSION["su"] = true;
					} else {
						$_SESSION["su"] = false;						
					}
				} else {
					$fonctions->notifier("Le mot de passe renseigné est incorrect", false);
					// echo '<div class="alert alert-danger col-12 text-center mb-0" role="alert">Le mot de passe renseigné est incorrect.</div>';
				}	
			} else {
				$fonctions->notifier("Le login renseigné n\'existe pas dans la base de données.", false);
				// echo '<div class="alert alert-danger col-12 text-center mb-0" role="alert">Le login renseigné n\'existe pas dans la base de données.</div>';
			}	
		} else {
			$fonctions->notifier("Le login renseigné doit commencé par OPL_.", false);
			// echo '<div class="alert alert-danger col-12 text-center mb-0" role="alert">Le login renseigné doit commencé par OPL_.</div>';
		}	
	
	}  

    //Formulaire des applis --> si nous sommes connecté
	if(isset($_SESSION["connecte"]) && $_SESSION["connecte"] == true) {
		// $connecte = "true";
		// $fonctions->notifier("Connexion réussie");
		echo '<meta http-equiv="Refresh" content="0; url=home.php">';
	//Pas connecté --> formulaire de connexion	
	} else {
		$connecte = "false";
		echo 
            '<div class="row justify-content-center m-0" id="recherche">
                <div class="justify-content-center mb-2 col-12">
                    <span id="title" class="animate__animated  animate__backInDown inline-block">Formulaire de Connexion</span>
                </div>		
	    	</div>
            <form id="formConnexion" method="POST" action="connexion.php" class="pt-3 pb-3 form-control">
                <div class="row justify-content-center margin-auto">
                    <div class="col-11 col-md-6">
                        <label class="sr-only" for="login">Login</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-user"></i>&nbsp;</div>
                            </div>
                            <input type="text" name="login" class="form-control" id="login" placeholder="Login" required />
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center margin-auto">
                    <div class="col-11 col-md-6">
                        <label class="sr-only" for="password">Mot de passe</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-key"></i>&nbsp;</div>
                            </div>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Mot de passe" required />
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-3 margin-auto">
                    <button type="submit" name="envoyer" value="true" class="btn btn-success col-10 col-md-3"><i class="fa fa-power-off"></i> Connexion</button>
                </div>			
            </form>'
        ;
	}	
	echo '<br clear="all" />';
    echo '</body></html>';