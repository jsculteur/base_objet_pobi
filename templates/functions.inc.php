<?php

		### SSO ###
	
	/*
	 * Essaye de r�cup�rer le login windows
	 * @return String Login windows si r�ussi, false si la tentative de r�cup�ration a �chou�
	 */
	function connexionWindows(){
		
		$browser = getBrowser();
		
		// Si l'utilisateur n'utilise pas windows, ou si le navigateur n'est pas chrome ni IE, �choue
		// if ($browser['platform'] != 'windows' || ( $browser['name'] != 'Google Chrome' && $browser['name'] != 'Internet Explorer')) {
			// return false;
		// }
		
	 
		$headers = apache_request_headers(); // R�cup�ration des l'ent�tes client		
	
	 
		if (@$_SERVER['HTTP_VIA'] != NULL) { // nous verifions si un proxy est utilis� : parceque l'identification par ntlm ne peut pas passer par un proxy
			return false;
		} 
		elseif (!isset($headers['Authorization'])) { //si l'entete autorisation est inexistante
			header("HTTP/1.1 401 Unauthorized"); //envoi au client le mode d'identification
			header("Connection: Keep-Alive");
			header("WWW-Authenticate: Negotiate");
			header("WWW-Authenticate: NTLM"); //dans notre cas le NTLM
			exit;
		}
		
		if (!isset($headers['Authorization'])) {
			return false;
		}
		
		if (substr($headers['Authorization'], 0, 5) != 'NTLM ') {
			return false; // on v�rifie que le client soit en NTLM
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
			header("WWW-Authenticate: NTLM $retAuth64"); // avec l'identification suppl�mentaire
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
	 * R�cup�re le type de navigateur de l'utilisateur avec sa version, son 
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
	
	
	//Connexion � l'active directory qui remonte les infos
	function connexionAD($login){
		
		// El�ments d'authentification LDAP
		$ldaprdn  = 'adru@pobi.dom';     // DN ou RDN LDAP
		$ldappass = '@st2oo7!*';  // Mot de passe associ�

		// Connexion au serveur LDAP
		// $ldapconn = ldap_connect("annuaire.pobi.dom")
		$ldapconn = ldap_connect("pobi-dc03.pobi.dom")
			or die("Impossible de se connecter au serveur LDAP.");

		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
			
		if ($ldapconn) {

			// Connexion au serveur LDAP
			$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
			
			// V�rification de l'authentification
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
				// return "Connexion LDAP �chou�e...";
				return false;
			}

		}
		
	}
    
	function remove_accent($str) { // fonction cr��e par "hello at weblap dot ro" (commentaire sur fr2.php.net/preg_replace)
				// utilis�e dans la fonction de recherche pour ne pas prendre en compte les accents et lettre doubles
		// $a = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', 
		// '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', 
		// '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c',
		// 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', '�', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G',
		// 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k',
		// 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', '�', '�', 'R', 'r',
		// 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', '�', '�', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U',
		// 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', '�', 'Z', 'z', 'Z', 'z', '�', '�', '?', '�', 'O', 'o', 'U', 'u', 'A', 'a', 'I',
		// 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?');
		// $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N',
		// 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e',
		// 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c',
		// 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G',
		// 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 
		// 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r',
		// 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U',
		// 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I',
		// 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		
			
		$unwanted_array = array('�'=>'S', '�'=>'s', '�'=>'Z', '�'=>'z', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'E',
                            '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I', '�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U',
                            '�'=>'U', '�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'c',
                            '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'i', '�'=>'i', '�'=>'i', '�'=>'i', '�'=>'o', '�'=>'n', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o',
                            '�'=>'o', '�'=>'o', '�'=>'u', '�'=>'u', '�'=>'u', '�'=>'y', '�'=>'b', '�'=>'y');
		return strtr( $str, $unwanted_array );
		
		return str_replace($a, $b, $str);
	}
	
	
	//Fonction qui permet d'enlever les "-" et les "'" des insertions
	function removeCaractere($string){
		
		$stringModi = str_replace("-"," ",$string);
		$stringModi = str_replace("'"," ",$stringModi);
		$stringModi = str_replace('"',' ',$stringModi);
		$stringModi = str_replace('�',' ',$stringModi);
		$stringModi = str_replace('/',' ',$stringModi);
		$stringModi = str_replace('\\',' ',$stringModi);		
		
		return $stringModi;
		
	}
	
	function notifier($message, $bool_reussite = true, $fermer_la_boite = true)	{ /* C.Coulon, mai 2016 ; remplace la pr�c�dente fonction notification */

		$boite_notif = '<div class="notif '; // classe notif de la notification, suivie de classe echec ou succes
		if ($bool_reussite === false)
			$boite_notif .= 'echec"><p><span style="color:darkred"><i class="fa fa-times"></i></span>';
		else
			$boite_notif .= 'succes"><p><span style="color:darkgreen"><i class="fa fa-check"></i></span>';
		
		$boite_notif .= '<br />'.$message.'</p>';

		if ($fermer_la_boite === true) 
			$boite_notif .= '</div><br /><br />';
		
		echo $boite_notif;
	}

	function pendingMessage($message) {
		$boite_notif = '<div class="notif warning"><p><span style="color:darkred"><i class="fa fa-times"></i></span>';
		$boite_notif .= '<br />'.$message.'</p>';

		echo $boite_notif;
	}
	
	// Fonction permettant d'afficher sur 5 caract�re les ID
	function afficheId($id){
		$nombre = strlen($id);
		
		switch($nombre){
			case(1): $idRetour = '0000'.$id;
				break;
			case(2): $idRetour = '000'.$id;
				break;
			case(3): $idRetour = '00'.$id;
				break;
			case(4): $idRetour = '0'.$id;
				break;
			case(5): $idRetour = $id;
				break;
		}
		
		return $idRetour;
	}
	
	//Fonction permettant d'afficher la date en francais
	function affichedate($date){
		
		$date_creation = date("d/m/Y", strtotime($date));
		$heure_creation = substr($date,10);
		
		
		return $date_creation." � ".$heure_creation;
	}
	
	//Fonction permettant d'afficher la date en francais
	function afficheDateSeule($date){
		
		$date_creation = date("d/m/Y", strtotime($date));
		$heure_creation = substr($date,10);
		
		
		return $date_creation;
	}
	
	//Fonction permettant de decrypter les coefs
	function decrypter($maCleDeCryptage, $maChaineCrypter){
		$maCleDeCryptage = md5($maCleDeCryptage);
		$letter = -1;
		$newstr = "";
		$maChaineCrypter = base64_decode($maChaineCrypter);
		$strlen = strlen($maChaineCrypter);
		for ( $i = 0; $i < $strlen; $i++ ){
			$letter++;
			if ( $letter > 31 ){
				$letter = 0;
			}
			$neword = ord($maChaineCrypter{$i}) - ord($maCleDeCryptage{$letter});
			if ( $neword < 1 ){
				$neword += 256;
			}
			$newstr .= chr($neword);
		}
		return $newstr;
	}

	function file_get_contents_curl($url) {
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
	
		$data = curl_exec($ch);
		curl_close($ch);
	
		return $data;
	}

 ?>