<<<<<<< HEAD
# Vite et Gourmand


# admin@gmail.com
# admin12345

# perso@gmail.com
# perso12345

# client@gmail.com
# client12345
=======
# 🚀 Guide de Déploiement Local - Application « Vite & Gourmand »


---

## 1. Copie des fichiers
1. Copiez le dossier du projet depuis git.
2. Collez-le directement dans le répertoire racine de XAMPP :  
  

---
## 2. Configuration de la Base de Données
1. Lancez le **Panneau de contrôle XAMPP** et démarrez **Apache** et **MySQL**.
2. Allez sur l'interface de gestion via votre navigateur : [http://localhost/phpmyadmin/]
3. Créez une nouvelle base de données nommée exactement : **`vite_et_gourmand`** (choisir l'interclassement `utf8mb4_general_ci`).
4. Cliquez sur l'onglet **Importer**, sélectionnez le fichier SQL que vous aurez téléchargé :  
   `
5. Cliquez sur **Importer** pour importer la bdd.

---

## 🔗 3. Vérification de la connexion (PHP)
Vérifiez que le fichier `includes/db_connect.php` utilise bien les identifiants par défaut de XAMPP :
* **Serveur :** `localhost`
* **Base de données :** `vite_et_gourmand`
* **Utilisateur :** `root`
* **Mot de passe :** ` ` (vide)
* Ensuite démaré le serveur local  dans votre terminal de l'éditeur de code : php -S localhost:8000 -t public

--- 

## 🚀 4. Lancement de l'Application

### Depuis le PC de développement :
Cliqué sur l'url du serveur dans le terminal

### Depuis un Smartphone (Démonstration Mobile) : 
Connexion mobile pas fonctionnel (j'ai pas réussi), mais site responsive possibilité de tester avec la fonction inspecter sur pc

* Cliqué sur inspecté sur une des pages, aller en haut a gauche
* Cliqué sur l'icone mobile, tablette et choisir la résolution
---

## 🔑 5. Comptes de Test inclus
* **Administrateur :** `admin@gmail.com` / Mot de passe : `admin12345`
* **Employé :** `toto@gmail.com` / Mot de passe : `toto12345`
* **Client :** `client@gmail.com` / Mot de passe : `client12345`
>>>>>>> development
