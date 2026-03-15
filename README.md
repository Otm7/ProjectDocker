# TP Docker : 2 Nginx + 2 PHP + MySQL (ESGI 3IW)

Ce projet est une solution de démonstration Docker / Docker Compose qui lance deux serveurs **Nginx** (ports 8081 et 8082), deux services **PHP** (mêmes images) et une base de données **MySQL** (port 3307) partagée entre les deux.

> ✅ Conformité totale avec les consignes (réseau `tp3iw_esgi_net`, service `mysql-db`, hostname `esgi-mysql`, header `X-Esquel-3IW: online`, port 3307, script d’automatisation, etc.)

---

## 📦 Structure du projet

```
├── docker-compose.yml
├── php/
│   ├── Dockerfile
│   └── init-laravel-3iw.sh
├── nginx1/
│   └── default.conf
├── nginx2/
│   └── default.conf
├── mysql/
│   └── data/
└── app/
    ├── .env
    ├── .env.example
    ├── artisan
    ├── public/
    │   └── index.php
    └── resources/views/welcome.blade.php
```

---

## 🚀 Lancer le projet

Depuis la racine du dossier (`c:\Users\dohoo\Documents\docker project`):

```powershell
docker compose up --build
```

Ensuite :

- Serveur 1 : http://localhost:8081
- Serveur 2 : http://localhost:8082

---

## 🧠 Comment ça marche

### ✅ Automatisation Laravel
Le script `php/init-laravel-3iw.sh` est exécuté par les deux conteneurs PHP au démarrage (via `command:`) et exécute :

- `composer install`
- `npm install && npm run build`
- `php artisan key:generate`
- `php artisan migrate:fresh --seed`

### 🔥 Deux serveurs distincts, même base
Les deux serveurs Nginx partagent le même code (`./app`) mais utilisent chacun un conteneur PHP différent (`php1` et `php2`). Ils pointent tous les deux sur la même base de données MySQL exposée sur le port **3307**.

### 🧩 Customisation SERVEUR 1 / SERVEUR 2
La page d’accueil (`welcome.blade.php`) affiche un label différent selon le serveur, injecté via FastCGI (`SERVER_LABEL`) par les configs Nginx.

---

## 🧪 Vérifier que tout fonctionne

1. Créer un utilisateur via le formulaire (ou via les routes Laravel) sur **nginx1**
2. Créer un deuxième utilisateur (différent) sur **nginx2**
3. Vérifier dans MySQL (port 3307) :

```sql
SELECT * FROM users;
```

Tu devrais voir **2 utilisateurs**.

---

## 🛠️ Remarques / Prochaines étapes (facultatif)

- Pour avoir un vrai projet Laravel, clone **ton repo Laravel complet** dans `app/` et remplace les fichiers placeholder ici.
- Tu peux ajouter Traefik, Mailpit, Minio ou Redis (bonus points) en ajoutant un service dans `docker-compose.yml` sur le même réseau `tp3iw_esgi_net`.

---

## 🧾 GitHub
Pense à pousser ce projet sur GitHub en public (obligatoire pour la note) :

```powershell
git init
git add .
git commit -m "TP Docker - 2 Nginx / 2 PHP / MySQL"
git remote add origin <ton_repo>
git push -u origin main
```
