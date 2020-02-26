# MyWishList.app

## C'est quoi ?

MyWishList.app est un projet universitaire composé de :
+ **Pierre Marcolet** *(AlasDiablo et lIotaMiu(Compte utilisé suite a des problèmes sous windows))*
+ **Lucas Burté** *(lucasburte)*
+ **Aurélien Rethiers** *(Aurel-11)*
+ **Émilien Visentini** *(Safyrus)*

## Comment l'installer

1) Clonez le depôt git dans un serveur apache avec `PHP-7.0.0` ou supérieur.

2) Après ceci faire la commande `composer install`, si vous ne l'avez pas, référez vous à [getcomposer.org](https://getcomposer.org/).

3) Pour la création de la base de données, récupérez le fichier SQL situé dans le dossier 'sql' et éxécutez-le sur votre serveur MySQL/MariaDB

4) Pour finir l'installation, créez un fichier `conf.ini` dans `src/conf/` et insérez les données suivantes:
    ```ini
    driver=VosDrivers
    username=VotreUsername
    password=VotreMotdepasse
    host=VotreIp
    database=VotreBaseDeDonnées
    charset=utf-8
    ```

## Lien d'utilisation

+ [Webetu](https://bit.ly/2QSRep8), requiert un compte de l'utiliversité
+ Statut du Deploiement:
    + [x] Site web
    + [x] Base de données
    + [x] Deploiement
+ Comptes utilisateurs pour le professeur
    ```
    username 1      : professeur
    mots de passe 1 : php
  
    username 2      : Behemote
    mots de passe 2 : godOfTheSea
  
    username 3      : BraveHurricane
    mots de passe 3 : BCRYPTforever
  
    username 4      : Didier
    mots de passe 4 : cMoiDidier
  
    username 5      : Doriant
    mots de passe 5 : horribleFruit
    ```

## Droits et utilisation

Code sous licence **AGPL-3.0**, lire la licence [ici](https://github.com/AlasDiablo/php-project-2019/blob/master/LICENSE).

## Tâches à faire/en cours

### Niveau 1

+ [x] **~~1 - Afficher une liste de souhaits~~**
+ [x] **~~2 - Afficher un item d'une liste~~**
+ [x] **~~3 - Réserver un item~~**
+ [x] **~~6 - Créer une liste~~**
+ [x] **~~8 - Ajouter des items~~**
+ [x] **~~14 - Partager une liste~~**

### Niveau 2

+ [x] **~~4 - Ajouter un message avec sa réservation~~**
+ [x] **~~5 - Ajouter un message sur une liste~~**
+ [ ] ***7 - Modifier les informations générales d'une de ses listes***
+ [x] **~~9 - Modifier un item~~**
+ [x] **~~10 - Supprimer un item~~**
+ [x] **~~15 - Consulter les réservations d'une de ses listes avant échéance~~**
+ [X] **~~16 - Consulter les réservations et messages d'une de ses listes après échéance~~**
+ [x] **~~20 - Rendre une liste publique~~**
+ [x] **~~21 - Afficher les listes de souhaits publiques~~**

### Niveau 3

+ [x] **~~11 - Rajouter une image à un item~~**
+ [x] **~~12 - Modifier une image d'un item~~**
+ [x] **~~13 - Supprimer une image d'un item~~**
+ [x] **~~17 - Créer un compte~~**
+ [x] **~~18 - S'authentifier~~**
+ [x] **~~19 - Modifier son compte~~**
+ [ ] 22 - Créer une cagnotte sur un item
+ [ ] 23 - Participer à une cagnotte
+ [x] **~~24 - Uploader une image~~**
+ [x] **~~25 - Créer un compte participant~~**
+ [x] **~~26 - Afficher la liste des créateurs~~**
+ [x] **~~27 - Supprimer son compte~~**
+ [x] **~~28 - Joindre des listes à son compte~~**

### Autres

+ [x] **~~Mise en pages~~**
+ [ ] Responsive Web Design
+ [x] **~~Ajouter un avatar pour les utilisateurs~~**
+ [x] **~~Gestion des erreurs HTTP~~**
+ [x] **~~Mise en correlation du code créer par chacun~~**
+ [x] **~~Corrections de bugs majeurs/failles de sécurité~~**
+ [X] **~~Création du set de donnée sur webetu~~**
+ [x] **~~Documentation du code~~**
+ [x] **~~Deploiement du projet sur webetu~~**
