Instructions d'installation : 

- Ouvrir un terminal dans le dossier et executer la commande suivante : 
```command
    composer install
```
La commande va automatiquement lancer l'installation de yarn par la suite pour les dépendances front (jquery, bootstrap, etc...)

- Définir un code outil dans http://utilisateurs.pobi.dom et venir mettre ce code ici (Par exemple avec l'appli BPSYLOB) en haut du fichier connexion.php : 
```php
// public/connexion.php
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
<!-- Le type est "module" car il permet d'inclure d'autres scripts -->
<script type="module" src="../assets/js/app.js"></script>
```

- Si vous avez besoin de créer vos propres pages en PHP, vous **devez** les placer dans le dossier *public*, à la racine du projet.
-  Il faut ***obligatoirement*** 4 lignes minimum dans chacune de vos pages PHP dans le dosser **public** : 
```php
// public/votre_page.php
session_start();
$titrepage = "Titre de la page sur l'onglet";
require_once("../vendor/autoload.php");
require_once("../templates/navbar.php");
```
* La première ligne démarre la session
* La deuxieme définit le titre de votre onglet, après le nom de l'appli
* La troisième charge toutes les vendors (dépendances PHP) ainsi que vos classes eventuelles dans le dossier **src**
* La quatrième permet d'inclure le menu de navigation supérieur en haut de votre page


Pour le javascript, une petite explication s'impose : 

```html
<script type="module" src="../assets/js/app.js"></script>
```
Ici, le type du fichier est "module" et non pas "text/javascript" comme d'habitude.

Cela va permettre d'enregistrer ce fichier **tools.js** comme module, ce qui permet d'importer autant de fichiers js que possible au sein de celui-ci.

L'importation de ce fichier tools se fait de la facon suivante : 
```javascript
// assets/app.js
"use strict";
import * as tools from './tools.js';
```
* Ici, toutes les fonctions qui seraient déclarées dans **tools.js** seront disponibles graçe à la variable tools (vous pouvez donner le nom que vous voulez à cette variable)

* Dans le fichier **tools.js**, Chaque fonction est préfixée de l'attribut ***export*** car c'est ce qui permet de l'utiliser en dehors du fichier **tools.js** : 
```javascript
// assets/tools.js
export function maFonctionExportee() {

}
```
* Dans le fichier **app.js**, si je souhaite utiliser la fonction *maFonctionExportee*, je devrais procéder de la façon suivante : 
```javascript
// assets/app.js
"use strict";
import * as tools from './tools.js';
tools.maFonctionExportee();
```
*NB: Bien evidement, si la fonction que vous souhaitez utiliser possède des paramètres, vous devrez également les renseigner lors de l'appel de cette fonction*