# App Sortir

## 📖 Vue d'ensemble
Le projet **App Sortir** est une plateforme web permettant aux stagiaires actuels et anciens de l'ENI de planifier et de participer à des sorties extra-scolaires.  
Il vise à améliorer la communication et l'organisation des événements, qui étaient auparavant gérés manuellement par le Bureau Des Élèves (BDE).

## 🎯 Objectifs
- Digitaliser l'organisation des sorties pour une meilleure accessibilité.  
- Faciliter la communication entre stagiaires grâce à une plateforme centralisée.  
- Automatiser la gestion des inscriptions et des participants.  
- Permettre une organisation géographique des événements via un rattachement aux campus.  

## ⚙️ Caractéristiques
- Création de comptes avec gestion des autorisations (administrateurs, organisateurs, participants).  
- Gestion des sorties : description, lieu, date, nombre de participants.  
- Interface intuitive et responsive (PC & mobile).  
- Sécurité renforcée avec authentification et validation des inscriptions.  

## 🛠️ Technologies utilisées
- **Symfony** 6.4  
- **PHP** 8.3.14  
- **MySQL** 8.0.32  
- **Composer** pour la gestion des dépendances  

## 🚀 Installation

1. Cloner le projet :
   ```bash
   git clone <url-du-depot>
   cd appsortir
   ```

2. Installer les dépendances :
   ```bash
   composer install
   ```

3. Configurer la base de données dans le fichier `.env` :
   ```env
   DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
   ```
   ➝ Remplacer `app` (nom d'utilisateur de la BDD), `!ChangeMe!` (Password de l'utilisateur), `app` (Nom de la BDD), `serverVersion` (Votre version de MySQL) par vos valeurs.

4. Créer et initialiser la base de données :
   ```bash
   symfony console doctrine:database:create
   symfony console doctrine:migration:migrate
   ```

5. (Optionnel) Charger des données de test :
   ```bash
   symfony console doctrine:fixtures:load
   ```

## 🌐 Déploiement
Configurer un serveur de déploiement (Apache/Nginx avec PHP 8.3 et MySQL).  
Assurez-vous que les permissions des répertoires `var/` et `public/` soient correctement configurées.  

## ✨ Fonctionnalités principales
- Création et gestion de sorties (événements).  
- Inscriptions/désinscriptions aux événements.  
- Gestion des rôles et permissions (admin, organisateur, participant).  
- Affichage des sorties par campus.  
- Interface responsive et moderne.  
- Sécurité et authentification robustes.  

## 👥 Contributeurs
- Benjamin
- Samos
- Mamuka
- Parishad

---

👥 Projet réalisé dans le cadre d’un travail de groupe à l’**ENI**.
