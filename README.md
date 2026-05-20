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
5. Cliquez sur **Importer** en bas de la page pour injecter les tables et le jeu d'essai.

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

### Depuis un Smartphone (Démonstration Mobile) : ( pas fonctionnel )
1. Récupérez l'adresse IP du PC de test (via la commande `ipconfig` dans l'invite de commandes Windows, ex: `192.168.1.45`).
2. Connectez le smartphone sur le **même réseau Wi-Fi** que le PC.
3. Désactivez temporairement le **Pare-feu Windows** (réseau privé).
4. Saisissez l'URL suivante sur le navigateur du smartphone :  
   👉 `http://192.168.1.45/julie_jose/public/index.php`

---

## 🔑 5. Comptes de Test inclus
* **Administrateur :** `admin@gmail.com` / Mot de passe : `admin12345`
* **Employé :** `toto@gmail.com` / Mot de passe : `toto12345`
* **Client :** `client@gmail.com` / Mot de passe : `client12345`