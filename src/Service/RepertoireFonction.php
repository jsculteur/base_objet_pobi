<?php

namespace App\Service;

use \PDO;
use App\Service\InformixConnection;
use App\Service\MySqlConnection;

class RepertoireFonction {
    private $baseInformix;
    private $baseConfigurateur;
    
    public function __construct() {
        $informix = new InformixConnection("socpobi_precix");
        $informixConnection = $informix->getConnection();

        $connectionMySql = new MySqlConnection();
        $mySqlConnection = $connectionMySql->getConnectionConfigurateur();

        $this->baseInformix = $informixConnection;
        $this->baseConfigurateur = $mySqlConnection;
    }

    public function getConnectionInformix() {
        return $this->baseInformix;
    }

    public function getArticlesAccm($noAccm): array {
        $articlesAccmArray = array();
        $listeArticlesAccm = $this->baseInformix->prepare("SELECT no_cposant, qte_necess, design FROM pro_ofcomp WHERE no_of = ?");
        $listeArticlesAccm->execute(array($noAccm));
        $articlesAccm = $listeArticlesAccm->fetchAll(PDO::FETCH_ASSOC);
        foreach($articlesAccm as $accm) {
            $sTab = array();
            $article = utf8_encode(trim($accm["NO_CPOSANT"]));
            $sTab["article"] = $article;
            $sTab["design"] = utf8_encode(trim($accm["DESIGN"]));
            $sTab["quantite"] = intval($accm["QTE_NECESS"]);
            array_push($articlesAccmArray, $sTab);
        }
    
        return $articlesAccmArray;
    }

    public function menDesignationFixer($article): string {
        $dormantDigit = substr($article, 3, 1);
        $couleurDigit = substr($article, 4, 1);
        $menDigit = substr($article, 5, 1);
        $dimensionsDigit = substr($article, 6, 1);
        $vitrageDigit = substr($article, 7, 1);
        $voletDigit = substr($article, 8, 1);
        $vantauxDigit = substr($article, 12, 1);
        $sqlGetDimensions = $this->baseConfigurateur->prepare("SELECT libelleDimensionsMenuiserie FROM dimensionsmenuiserie WHERE indiceDimensionsMenuiserie = ?");
        $sqlGetDimensions->execute(array($dimensionsDigit));
        $tmpDimensions = $sqlGetDimensions->fetch(PDO::FETCH_COLUMN);
        $dimensions = str_replace(" ", "", $tmpDimensions);
        $dimensions = strtoupper($dimensions);
        $tableauMortaises = array('6','7','C','D','E','K','M','O','Q','S','U','W','Z');
        $designation = "";
        // Switch Type Menuiserie
        switch($menDigit) {
            case '2':
            case '7':
            case 'A':
            case 'D':
            case '1':
            case '6':
            case '0':
            case 'C':
                $designation = $designation.= "Fenêtre ";
                if($vantauxDigit == '1') {
                    $designation .= "1 VT ";
                } else {
                    $designation .= "2 VTX ";
                }
            break;

            case 'B':
            case 'E':
            case 'J':
            case 'K':
                $designation = $designation.= "Porte Fenêtre ";
            break;

            case 'L':
            case 'M':
            case 'N':
            case 'O':
                $designation = $designation.= "Coulissant ";
            break;

            case 'P':
            case 'Q':
            case 'R':
            case 'S':
            case 'T':
            case 'U':
            case 'V':
            case 'W':
                $designation = $designation.= "Fenêtre Coulissante ";
            break;

            case 'X':
            case 'Z':
                $designation = $designation.= "Abattant ";
            break;
        }
        
        $designation = $designation .= $dimensions;

        // Switch sur les dormants
        switch($dormantDigit) {
            case '1':
            case '2':
                $designation = $designation .= " - D120 ";
            break;

            case '8':
            case '9':
                $designation = $designation .= " - D140 ";
            break;

            default:
                $designation = $designation .= " - ";
            break;
        }

        // Switch sur la couleur
        switch($couleurDigit) {
            case '1':
                $designation = $designation .= " - Blanche - ";
            break;

            case '2':
                $designation = $designation .= " - Beige - ";
            break;

            case '3':
                $designation = $designation .= " - Grise - ";
            break;

            case '4':
                $designation = $designation .= " - Grise Anthracite Plaxée 2 faces - ";
            break;

            case '5':
                $designation = $designation .= " - Irish Oak Plaxée 2 faces - ";
            break;

            case '6':
                $designation = $designation .= " - Blanche / Grise Anthracite - ";
            break;

            case '7' : 
                $designation = $designation .= " - Blanche / Irish Oak - ";
            break;
        }

        // Switch sur les vitrages
        switch($vitrageDigit) {
            case '4':
            case '9':
            case 'N':
                $designation = $designation .= "Vitrage Normal ";
            break;

            case '0':
            case '5':
            case 'P':
                $designation = $designation .= "Vitrage imprimé ";
            break;

            case '2':
            case '7':
                $designation = $designation .= "Vitrage sécurit ";
            break;

            case '3':
            case '8':
                $designation = $designation .= "Vitrage sécurit Imprimé ";
            break;

            case '1':
            case '6':
                $designation = $designation .= "Vitrage accoustique ";
            break;

            case 'A':
            case 'B':
                $designation = $designation .= "Vitrage accoustique Imprimé ";
            break;
        }

        // Condition mortaises
        if(in_array($menDigit, $tableauMortaises)) {
            $designation = $designation .= "(Usinage Mortaise seule) - ";
        } else {
            $designation = $designation .= "- ";
        }

        // Switch volets
        switch($voletDigit) {
            case '3':
            case '7':
            case '(':
            case 'e':
            case 'j':
            case 'o':
                $designation = $designation .= "BBI Radio - ";
            break;

            case 'A':
            case 'D':
            case '+':
                $designation = $designation .= "Monobloc Radio - ";
            break;

            case '2':
            case '6':
            case 'I':
            case 'J':
            case '&':
            case '@':
            case 'c':
            case 'd':
            case 'h':
            case 'i':
            case 'm':
            case 'n':
                $designation = $designation .= "BBI Électrique - ";
            break;

            case '9':
            case 'C':
            case 'K':
            case 'L':
            case 'µ':
            case '¤':
                $designation = $designation .= "Monobloc Électrique - ";
            break;

            case '1':
            case '5':
            case 'E':
            case 'F':
            case 'Z':
            case '#':
            case 'a':
            case 'b':
            case 'f':
            case 'g':
            case 'k':
            case 'l':
                $designation = $designation .= "BBI Tringle - ";
            break;

            case '8':
            case 'B':
            case 'G':
            case 'H':
            case '%':
            case '§':
                $designation = $designation .= "Monobloc Tringle - ";
            break;
        }

        return $designation;
    }

    public function getDesign2Article($article): string {
        $sqlGetDesign2 = $this->baseInformix->prepare("SELECT TRIM(design2) AS design2 FROM bas_art WHERE no_art = ?");
        $sqlGetDesign2->execute(array($article));
        $design2 = $sqlGetDesign2->fetch(PDO::FETCH_COLUMN);

        return $design2;
    }
}