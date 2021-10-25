<?php
    
	### SSO ###
	
	/*
	 * Essaye de récupérer le login windows
	 * @return String Login windows si réussi, false si la tentative de récupération a échoué
	 */
	function connexionWindows(){
		
		$browser = getBrowser();
		
		// Si l'utilisateur n'utilise pas windows, ou si le navigateur n'est pas chrome ni IE, échoue
		if ($browser['platform'] != 'windows' || ( $browser['name'] != 'Google Chrome' && $browser['name'] != 'Internet Explorer')) {
			return false;
		}
	 
		$headers = apache_request_headers(); // Récupération des l'entêtes client
	 
		if (@$_SERVER['HTTP_VIA'] != NULL && !isset($headers['Authorization'])) { // nous verifions si un proxy est utilisé : parceque l'identification par ntlm ne peut pas passer par un proxy
			return false;
		}
	 
		if (!isset($headers['Authorization'])) {
			return false;
		}
	 
		if (substr($headers['Authorization'], 0, 5) != 'NTLM ') {
			return false; // on vérifie que le client soit en NTLM
		}
	 
		$chaine = $headers['Authorization'];
		$chaine = substr($chaine, 5); // recuperation du base64-encoded type1 message
		$chained64 = base64_decode($chaine); // decodage base64 dans $chained64
		if (ord($chained64{8}) == 1) {
			$retAuth = "NTLMSSP" . chr(000) . chr(002) . chr(000) . chr(000) . chr(000) . chr(000) . chr(000) . chr(000);
			$retAuth .= chr(000) . chr(040) . chr(000) . chr(000) . chr(000) . chr(001) . chr(130) . chr(000) . chr(000);
			$retAuth .= chr(000) . chr(002) . chr(002) . chr(002) . chr(000) . chr(000) . chr(000) . chr(000) . chr(000);
			$retAuth .= chr(000) . chr(000) . chr(000) . chr(000) . chr(000) . chr(000) . chr(000);
			$retAuth64 = base64_encode($retAuth); // encode en base64
			$retAuth64 = trim($retAuth64); // enleve les espaces de debut et de fin
			header("HTTP/1.1 401 Unauthorized"); // envoi le nouveau header
			header("WWW-Authenticate: NTLM $retAuth64"); // avec l'identification supplémentaire
			exit;
		} else if (ord($chained64{8}) == 3) {
			$lenght_domain = (ord($chained64[31]) * 256 + ord($chained64[30])); // longueur du domain
			$offset_domain = (ord($chained64[33]) * 256 + ord($chained64[32])); // position du domain.
			$domain = str_replace("\0", "", substr($chained64, $offset_domain, $lenght_domain)); // decoupage du du domain
	 
			$lenght_login = (ord($chained64[39]) * 256 + ord($chained64[38])); // longueur du login.
			$offset_login = (ord($chained64[41]) * 256 + ord($chained64[40])); // position du login.
			$login = str_replace("\0", "", substr($chained64, $offset_login, $lenght_login)); // decoupage du login
			if (!empty($login)) {
				return $login;
			}
		}
		return false;
	}
	
	 
	/**
	 * Récupère le type de navigateur de l'utilisateur avec sa version, son 
	 * @return array Tableau contenant des informations sur le navigateur de l'utilisateur
	 */
	function getBrowser() {
		$uAgent = $_SERVER['HTTP_USER_AGENT'];
		$platform = 'unknown';
		$bname = 'unknown';
	 
		if (preg_match('/linux/i', $uAgent)) {
			$platform = 'linux';
		} elseif (preg_match('/macintosh|mac os x/i', $uAgent)) {
			$platform = 'mac';
		} elseif (preg_match('/windows|win32/i', $uAgent)) {
			$platform = 'windows';
		}
	 
		// Next get the name of the useragent yes seperately and for good reason
		if (preg_match('/Firefox/i', $uAgent)) {
			$bname = 'Mozilla Firefox';
		} elseif (preg_match('/Chrome/i', $uAgent)) {
			$bname = 'Google Chrome';
		} elseif (preg_match('/Safari/i', $uAgent)) {
			$bname = 'Apple Safari';
		} elseif (preg_match('/Opera/i', $uAgent)) {
			$bname = 'Opera';
		} elseif (preg_match('/Netscape/i', $uAgent)) {
			$bname = 'Netscape';
		} else if (preg_match('/MSIE/i', $uAgent) OR ( preg_match('/Windows NT/i', $uAgent) AND preg_match('/Trident/i', $uAgent))) {
			$bname = 'Internet Explorer';
		}
	 
		return array(
			'name' => $bname,
			'platform' => $platform
		);
	}
	
	
	//Connexion à l'active directory qui remonte les infos
	function connexionAD($login){
		
		// Eléments d'authentification LDAP
		$ldaprdn  = 'adru@pobi.dom';     // DN ou RDN LDAP
		$ldappass = '@st2oo7!*';  // Mot de passe associé

		// Connexion au serveur LDAP
		// $ldapconn = ldap_connect("annuaire.pobi.dom")
		$ldapconn = ldap_connect("pobi-dc03.pobi.dom")
			or die("Impossible de se connecter au serveur LDAP.");

		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			
		if ($ldapconn) {

			// Connexion au serveur LDAP
			$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
			
			// Vérification de l'authentification
			if($ldapbind == true){
				
				$filter = "(sAMAccountName=$login)";

				$result = ldap_search($ldapconn,"OU=POBI Utilisateurs,OU=POBI,DC=pobi,DC=dom",$filter);
				
				@ldap_sort($ldapconn,$result,"sn");
				
				$info = ldap_get_entries($ldapconn, $result);
				
				// return($info);
				if(!empty($info)){
					return $info;
				}else{
					return false;
				}
										
			}elseif($ldapbind == false) {
				// return "Connexion LDAP échouée...";
				return false;
			}

		}
		
	}
	
?>