Instructions d'installation : 

- Ouvrir un terminal dans le dossier et executer la commande suivante : 
```command
    composer install
```
La commande va automatiquement lancer l'installation de yarn par la suite pour les dépendances front (jquery, bootstrap, etc...)

- Si vous souhaitez ajouter des liens dans la navbar, vous pouvez venir les ajouter ici : 
```html
<!-- templates/navbar.php -->
<div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link" href="home.php" role="button" aria-haspopup="true" aria-expanded="false">
                Accueil
            </a>
        </li>
        <!-- Rajouter un lien comme ci-dessous -->
        <!-- <li class="nav-item">
            <a class="nav-link" href="maPage.php" role="button" aria-haspopup="true" aria-expanded="false">
                Ma Page
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

- Une vérification est faite pour empecher les utilisateurs utilisant **Internet Explorer** de pouvoir accéder à l'application. Celle-ci est faite directement dans le fichier index.php à la racine : 

```php
// index.php

// On ajoute les vendors pour pouvoir charger les classes
require_once("vendor/autoload.php");
// Ajout de la classe 'RepertoireFonctions'
use App\Service\RepertoireFonction;
$fonctions = new RepertoireFonction();
// Récupération du navigateur courant
$browser = $fonctions->getBrowser();
// Si le navigateur n'est pas Chrome ni Firefox, on bloque l'accès à l'outil
if($browser['name'] != 'Google Chrome' && $browser['name'] != 'Mozilla Firefox') {
    echo '<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css" />';
    echo '<link rel="stylesheet" href="assets/css/style.css" />';
    echo '<p class="col-8 center alert alert-danger">Merci d\'utiliser Google CHROME pour pouvoir accèder à cette application !</p>';
} else {
    // Sinon on redirige vers la page de connexion
    header("Refresh:0;url=public/connexion.php");
}
```

- Si vous avez besoin de créer vos propres pages en PHP, vous **devez** les placer dans le dossier *public*, à la racine du projet.
-  Il faut ***obligatoirement*** ces quelques lignes minimum dans chacune de vos pages PHP dans le dosser **public** : 
```php
// public/votre_page.php
// session_name("some_session_name");
// session_set_cookie_params(0, '/', '.pobi.dom');

//Demarrage de la session utilisateur
session_start();
$titrepage = "Accueil";
require_once("../vendor/autoload.php");
require_once("../templates/navbar.php");

// Ajout de la classe 'RepertoireFonctions'
use App\Service\RepertoireFonction;
$fonctions = new RepertoireFonction();

// Ici, mettre votre code HTML
echo '';
```

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