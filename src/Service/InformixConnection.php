<?php

namespace App\Service;

use \PDO;

class InformixConnection {

    private $bddProd;
    private $bddTest;

    public function __construct() {
        try {
            $servInformix  = "pobi-sylob.pobi.dom";  	// Serveur PROD
            $portInformix = "1526";							// port logique de la connexion
            $loginInformix = "informix";					// login
            $mdpInformix = "_yYQzqk6";						// password
            $serverInformix = "ol_gpao";					//Nom du serveur
            
            $prod = new PDO("informix:host=".$servInformix."; service=".$portInformix."; database=socpobi_precix; server=".$serverInformix."; protocol=onsoctcp; EnableScrollableCursors=1", $loginInformix, $mdpInformix);
            $prod->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $test = new PDO("informix:host=".$servInformix."; service=".$portInformix."; database=socpobi99_precix; server=".$serverInformix."; protocol=onsoctcp; EnableScrollableCursors=1", $loginInformix, $mdpInformix);
            $test->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->bddProd = $prod;
            $this->bddTest = $test;
            
        }
        catch (Exception $e) {
            echo utf8_encode($e).'<br />';
            echo 'Impossible de se connecter à la BDD Informix.<br />';
        }
    }

    public function getConnectionProd() {
        return $this->bddProd;
    }

    public function getConnectionTest() {
        return $this->bddTest;
    }
}