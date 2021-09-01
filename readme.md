Instructions d'installation : 

- Ouvrir un terminal dans le dossier et executer la commande suivante : 
```command
    composer install
```

- Executer ensuite la commande suivante : 
```command
    yarn install
```

- Définir un code outil dans http://utilisateurs.pobi.dom et venir mettre ce code ici (Par exemple avec l'appli BPSYLOB) en haut du fichier index.php : 
```php
// index.php
$codeOutil = "BPSYLOB";
```

- Si vous souhaitez ajouter des liens dans la navbar, vous pouvez venir les ajouter ici : 
```html
<!-- templates/navbar.php -->
<div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                Accueil
            </a>
        </li>
        <!-- Rajouter un lien comme ci-dessous -->
        <!-- <li class="nav-item">
            <a class="nav-link" href="cible_du_lien" role="button" aria-haspopup="true" aria-expanded="false">
                Texte du lien
            </a>
        </li> -->
    </ul>
</div>
```

