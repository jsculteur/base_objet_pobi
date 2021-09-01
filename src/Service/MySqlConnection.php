<?php

namespace App\Service;

use \PDO;

class MySqlConnection {

    private $bddGenerale;
    private $bddUtilisateurs;

    public function __construct() {
        try {
            $ipServ = "10.250.230.107";
            $port = "3306";
            $login = "pobiadmin";
            $mdp = "POBI2021!";
            
            $bddDonneesGeneralesPobi = new PDO("mysql:host=".$ipServ."; port=".$port."; dbname=donnees_generales", $login, $mdp);
            $bddDonneesGeneralesPobi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $bddDonneesGeneralesPobi->exec("SET NAMES utf8");

            $bddUtilisateursPobi = new PDO("mysql:host=".$ipServ."; port=".$port."; dbname=utilisateursoutilspobi", $login, $mdp);
            $bddUtilisateursPobi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $bddUtilisateursPobi->exec("SET NAMES utf8");

            $this->bddGenerale = $bddDonneesGeneralesPobi;
            $this->bddUtilisateurs = $bddUtilisateursPobi;
        }
        catch (Exception $e) {
            echo $e.'<br />';
            echo 'Impossible de se connecter � la BDD MySQL.<br />';
        }
    }

    public function getConnectionDG() {
        return $this->bddGenerale;
    }

    public function getConnectionUtilisateurs() {
        return $this->bddUtilisateurs;
    }
}