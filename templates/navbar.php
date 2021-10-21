<?php
    include("header.php");
?>

<!-- Création d'une navbar afin de naviguer plus facilement entre les différents éléments (Nomenclature ou réplication de base) -->
<nav id="navbar" class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="http://papp.pobi.dom">
        <img src="../assets/img/logo.png" width="60" height="30" class="d-inline-block align-top" alt="Logo POBI Industrie">&nbsp;&nbsp;<?php echo $nomAppli; ?>&nbsp;&nbsp;
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="home.php" role="button" aria-haspopup="true" aria-expanded="false">
                    Accueil
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="maPage.php" role="button" aria-haspopup="true" aria-expanded="false">
                    Ma Page
                </a>
            </li> -->
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <!-- Si la personne n'est pas connectée, on n'affiche ni l'utilisateur ni le dropdown pour se déconnecter -->
                <?php if(isset($_SESSION["pseudo"]) && !empty($_SESSION)) { ?>
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user"></i> <span id="currentUser"><?php echo strtoupper($_SESSION["pseudo"]); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <form class="form-nav" method="post" action="connexion.php" id="logout">
                            <button name="logout" value="true" type="submit" class="btn btn-sm btn-danger btn-nav"><i class="fa fa-power-off"></i> Déconnexion</button>
                        </form>
                    </div>
                <?php } ?>
            </li>
        </ul>
    </div>
</nav>
<div id="contenu">
    <div id="container" class="container">