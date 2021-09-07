<?php

namespace App\Service;

use \PDO;

class InformixConnection {

    private $bddInformix;

    public function __construct($bdd) {
        try {
            $servInformix  = "pobi-sylob.pobi.dom";  	// Serveur PROD
            $portInformix = "1526";							// port logique de la connexion
            $loginInformix = "informix";					// login
            $mdpInformix = "_yYQzqk6";						// password
            $serverInformix = "ol_gpao";					//Nom du serveur
            
            $connexion = new PDO("informix:host=".$servInformix."; service=".$portInformix."; database=".$bdd."; server=".$serverInformix."; protocol=onsoctcp; EnableScrollableCursors=1", $loginInformix, $mdpInformix);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->bddInformix = $connexion;
            
        }
        catch (Exception $e) {
            echo utf8_encode($e).'<br />';
            echo 'Impossible de se connecter à la BDD Informix.<br />';
        }
    }

    public function getConnection() {
        return $this->bddInformix;
    }
}