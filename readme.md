Instructions d'installation : 

- Ouvrir un terminal dans le dossier et executer la commande suivante : 
```command
    composer install
```
La commande va automatiquement lancer l'installation de yarn par la suite pour les dépendances front (jquery, bootstrap, etc...)

- Définir un code outil dans http://utilisateurs.pobi.dom et venir mettre ce code ici (Par exemple avec l'appli BPSYLOB) en haut du fichier index.php : 
```php
// index.php
$codeOutil = "BPSYLOB";
```
*NB: Il faut lier l'outil à votre profil sinon vous ne pourrez pas accéder à l'outil lors de votre connexion sur celui-ci*

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

- Les fichiers js et css se trouvent dans le dossiers **assets**, à la racine du projet. Vous devez créer vos fichiers dans les dossiers appropriés. Une fois votre fichier créé, il suffite de l'inclure au debut du fichier header, qui se trouve ici : *templates* : 

```html
<!-- templates/header.php -->
<!-- JS utilisateur -->
<script type="text/javascript" src="assets/js/app.js"></script>
```

- Si vous avez besoin de créer vos propres pages en PHP, vous **devez** les placer dans le dossier *public*, à la racine du projet.
-  Il faut ***obligatoirement*** 3 lignes minimum dans chacune de vos pages PHP dans le dosser **public** : 
```php
// public/votre_page.php
session_start();
require_once("../vendor/autoload.php");
require_once("../templates/navbar.php");
```
* La première ligne démarre la session
* La deuxieme charge toutes les vendors (dépendances PHP) ainsi que vos classes eventuelles dans le dossier **src**
* La troisième permet d'inclure le menu de navigation supérieur en haut de votre page