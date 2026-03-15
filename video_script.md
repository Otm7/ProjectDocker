# Script vidéo (5 minutes) — TP Docker 3IW ESGI

> 🎬 Objectif : te donner un script que tu peux lire mot pour mot pendant la vidéo, comme si tu avais un prompteur.

---

## 1️⃣ Introduction (30s)
> 🎤 Parle face caméra, souris, et présente-toi.

- "Salut, je m'appelle **Othman DOHO** et aujourd'hui je vous présente mon **TP Docker** pour le cours 3IW à l'ESGI."
- "Le sujet : **faire tourner deux sites PHP avec deux Nginx**, mais **avec une seule base de données MySQL partagée**."
- "Tout est automatisé avec Docker Compose et un script, et ça respecte toutes les consignes du cahier des charges (réseau, noms de services, port 3307, header personnalisé, etc.)."

🔎 *Conseil : Tu peux commencer par montrer ton écran, ouvrir le dossier du projet, et expliquer rapidement la structure.*

---

## 2️⃣ Architecture Docker (45s)
> 🎥 Partage ton écran sur l'éditeur (VS Code) et ouvre `docker-compose.yml`.

- "Le fichier principal, c'est **docker-compose.yml**. C'est ce qui va démarrer l'ensemble du TP en un seul `docker compose up`." 

### Points à expliquer en montrant le fichier:
- "On utilise un réseau Docker appelé **tp3iw_esgi_net** (c’est imposé par le TP)."
- "Le service **mysql-db** est la base de données, avec un **hostname `esgi-mysql`** et le port **3307** (important, on ne prend pas 3306)."
- "On a deux services PHP : **php1** et **php2**. Ils utilisent **la même image** `php-laravel-esgi` (c’est aussi imposé)."
- "On a deux serveurs Nginx : **nginx1** sur le port 8081, et **nginx2** sur le port 8082. Chaque Nginx envoie les requêtes vers son PHP correspondant."
- "J’ai aussi ajouté un volume `esgi_tp_final_cache` monté en lecture seule sur chaque service PHP (c’était une contrainte du TP, même si on ne l’utilise pas réellement)."

💡 *Dis : “Du coup on a la même base de données pour les deux sites, mais chaque site tourne dans son propre conteneur PHP + Nginx.”*

---

## 3️⃣ Dockerfile PHP (1 min)
> 🎥 Ouvre `php/Dockerfile`.

- "On part d'une image **php:8.2-fpm**.
- On installe toutes les dépendances dont on a besoin : Git, Curl, Node, NPM...
- Et surtout : on installe **Composer** via le binaire officiel." 

📌 Mention obligatoire :
> -> Il y a un commentaire exact `# ESGI-3IW-Docker-TP` juste avant l'installation de Composer.

- "C'est important parce que le TP demande de ne pas utiliser d'image toute prête, et de montrer qu'on sait installer Composer correctement."

---

## 4️⃣ Automatisation (1 min)
> 🎥 Ouvre `php/init-laravel-3iw.sh`.

- "Ce script est exécuté automatiquement par Docker dès que le conteneur PHP démarre."
- Il fait plusieurs choses :
  1. copie `.env.example` en `.env` si besoin
  2. installe les dépendances PHP (`composer install`)
  3. installe les dépendances JS (`npm install` + `npm run build`)
  4. génère la clé Laravel (`php artisan key:generate`)
  5. exécute la migration + seed (`php artisan migrate:fresh --seed`)
  6. lance PHP-FPM (serveur PHP)

🔑 *Tu peux dire clairement : “C’est ça qui permet de tout automatiser, je ne lance rien à la main, Docker le fait pour moi.”*

---

## 5️⃣ Démonstration en live (1 min)
> 🎥 Basculer sur un terminal.

1. Lancer :
   ```powershell
   docker compose up --build
   ```
2. Montrer que les conteneurs démarrent (dans le terminal).
3. Ouvrir ces deux URL dans le navigateur :
   - http://localhost:8081 → **Serveur 1**
   - http://localhost:8082 → **Serveur 2**

🎯 Explique : "Les deux affichent la même page, mais le label change grâce à une variable envoyée par Nginx (Serveur 1 / Serveur 2)." 

---

## 6️⃣ Connexion / inscription (1 min)
> 🎥 Montre le formulaire sur les deux serveurs.

- "J’ai ajouté un petit système d’authentification basique en PHP, sans Laravel complet, pour respecter l'esprit du TP (aucun langage supplémentaire, tout est dans Docker et PHP)."
- Montre l’inscription sur le serveur 1, puis sur le serveur 2.

---

## 7️⃣ Vérification de la base de données (45s)
> 🎥 Retourne dans le terminal.

Explique : 
- "Les deux sites utilisent la même base MySQL. Maintenant, je vais prouver que les deux comptes sont bien dans la même table `users`."

Commande :
```powershell
docker compose exec mysql-db mysql -u laravel -plaravel -e "SELECT * FROM users;" laravel
```

👉 Montre le résultat : 2 utilisateurs présents.

---

## 8️⃣ Conclusion + troll (20s)
- "Voilà, j'ai deux serveurs Nginx (deux maisons), deux backends PHP (deux colocataires), et une seule cuisine commune (MySQL)."
- « **Troll** : On a deux colocataires qui utilisent la même cuisine mais qui ne se parlent jamais — ils mangent leur propre bouffe (chaque serveur a son PHP), mais le frigo est commun (la base). »
- "Merci, et n’oubliez pas : Docker, c’est comme une cuisine de coloc — organisé, isolé, et on évite les conflits de port."

---

🎬 **Astuce vidéo** : Si tu veux faire durer à 5 min, fais une pause sur chaque page (montre le code, lis une phrase, passe à la suivante). Monologue clair + gestes = impression pro.
