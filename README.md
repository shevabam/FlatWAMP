# Flat WAMP

![](https://raw.github.com/shevabam/FlatWAMP/master/screenshot-1.png)
![](https://raw.github.com/shevabam/FlatWAMP/master/screenshot-2.png)

## Installation

- Rendez-vous dans le répertoire `www` de WAMP
- Renommez le fichier `index.php` en ce que vous voulez, pour garder une sauvegarde
- Collez-y les fichiers de Flat WAMP
- Sur votre navigateur, allez sur `http://localhost/` et admirez ;)

----------

- Go to your `www` directory
- Rename the `index.php` file in what you want to keep a backup
- Paste it the Flat WAMP files
- On your browser, go to `http://localhost/` and enjoy ;)

## Fonctionnement / Features

Les virtual hosts sont récupérés via trois méthodes :

1. Parsage des fichiers de configuration des virtual hosts dans le dossier `./vhosts`
2. Parsage du fichier `httpd-vhosts.conf` afin de récupérer les directives `ServerName` de chaque virtual host
3. Récupération des dossiers dans `www/`, avec la possibilité d'en exclure (voir la section *Paramètres*)

Les virtual hosts sont classés par nom (URL).  
Les couleurs des blocs sont définies dans les paramètres (voir ci-dessous). Elles sont utilisées de manière aléatoire sur les blocs.

Le p'tit bonus, c'est que vous pouvez utiliser les **flèches de votre clavier** pour naviguer dans les virtual hosts !
Utilisez la touche `entrée` pour accéder au projet sélectionné !

----------

Virtual hosts are recovered through three methods :

1. Parsing configuration files of virtual hosts in the `./vhosts` folder
2. Parsing `httpd-vhosts.conf` file to recover `ServerName` directives for each virtual host
3. Retrieving folders in `www/`, with the possibility to exclude (see *Parameters* section)

Virtual hosts are ordered by name (URL).  
Block's colors are defined in the parameters (see below). They are used randomly on the blocks.

You can use the **arrows on your keyboard** to navigate the virtual hosts !
Use the `enter` key to go to the selected project !


## Paramètres / Parameters

Au début du fichier `index.php` se trouve un tableau nommé `$config`. Il contient les variables de configuration du script. Voici celles que vous pouvez modifier :
 
- `$config['title']` : titre de la page
- `$config['wampConfFile']` : chemin vers le fichier de configuration de WAMP
- `$config['dirsToHide']` : dossiers à exclure dans le cas du parsage du dossier `www/`
- `$config['colors']` : liste des différentes couleurs disponibles pour l'affichage des virtual hosts

----------

At the beginning of the `index.php` file there is an array called `$config`. It contains the configuration variables for the script. Here are the ones you can change :

- `$config['title']` : page title
- `$config['wampConfFile']` : path to the WAMP configuration file
- `$config['dirsToHide']` : folders to be excluded in the case of parsing the `www/` folder 
- `$config['colors']` : list of different colors available for displaying virtual hosts


## Changelog

### v1.2.1 - 5 Sept 2014

- Fix on vhosts file path

### v1.2 - 5 Sept 2014

- Upgrade jQuery version to 2.1.1
- Responsive improved (design and keyboard navigation)
- Footer is now correctly positioned (always at the bottom)

### v1.1 - 7 Nov 2013

- Fix on retrieving Apache and PHP version

### v1.0 - 7 Nov 2013

- Initial version