# Presentation
Cette API Cocktails, comme son nom l'indique, permet à ses utilisateurs de consulter ou bien de créer des recettes de cocktails.

## BDD
* Table ingrédient
* Table recette
* Table instruction
* Table picture
* Table user

##### Ingrédients
* Consulter tous les ingrédients
* Consulter un ingrédient
* Supprimer un ingrédient (Admin)
* Créer un ingrédient (Admin)
* Modifier un ingrédient (Admin)
* Ajouter une recette à un ingrédient (Admin)
* Supprimer une recette associée à un ingrédient (Admin)
* Ajouter une image à un ingrédient (Admin)
* Supprimer une image associée à un ingrédient (Admin)

##### Recettes
* Consulter toutes les recettes
* Consulter une recette
* Supprimer une recette (Admin)
* Créer une recette (Admin)
* Modifier une recette (Admin)
* Ajouter un ingrédient à une recette (Admin)
* Supprimer un ingrédient associé à une recette (Admin)
* Ajouter une image à une recette (Admin)
* Supprimer une image associée à une recette (Admin)
* Ajouter une instruction à une recette (Admin)
* Supprimer une instruction associée à une recette (Admin)

##### Instructions
* Consulter toutes les instructions
* Consulter une instruction
* Supprimer une instruction (Admin)
* Créer une instruction (Admin)
* Modifier une instruction (Admin)
* Ajouter une recette à une instruction (Admin)
* Supprimer une recette associée à une instruction (Admin)

##### Pictures
* Consulter toutes les images
* Consulter une image
* Supprimer une image (Admin)
* Créer une image (Admin)
* Modifier une image (Admin)

##### Users
* Consulter tous les utilisateurs (Admin)
* Consulter un utilisateur (Admin)
* Supprimer un utilisateur (Admin)
* Créer un utilisateur (Admin)
* Modifier un utilisateur (Admin)

##### Fonctionnalités :
* Obtenir toutes les recettes associées à un ingrédient entré par l'utilisateur

# Installation
Cloner le projet :
```bash
git clone https://github.com/JarodLaquetDev/002_api_cocktails/edit/develop/README.md
```
Supprimer le dossier vendor
Créer un .env.local
Installer composer :
```bash
composer install
```
Créer un JWT TOKEN :
```bash
php bin/console lexik:jwt:generator-keypair
```
Créer la base de données :
```bash
php bin/console d:d:c
php bin/console d:s:u --force
php bin/console d:f:l
```
Démarrer le serveur symfony
```bash
symfony serve
```

# Contact
Jarod LAQUET -email: jarod.laquet@ynov.com
Alexis JEANJEAN -email : alexis.jeanjean@ynov.com


