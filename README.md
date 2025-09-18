# WhisPen — le stylo chuchotteur
 
*NFC → PWA → messagerie chiffrée, lecture unique*
 
Prototype réalisé dans le cadre du **Workshop SN2 2025-2026**. **WhisPen** est un stylo discret contenant un **tag NFC** qui ouvre une **PWA**. Après saisie d’un **code secret**, l’utilisateur prépare un message **chiffré** et ne peut l’**envoyer** qu’en **re-présentant** le stylo (2ᵉ lecture NFC). Le message est **lisible une seule fois** puis **supprimé**.
 
> ⚠️ **Usage strictement pédagogique.** Ce prototype n’est pas destiné à un usage réel d’espionnage.
 
---
 
## ✨ Fonctionnalités
 
* **Ouverture par NFC** (tap #1) → PWA.
* **Mode espion** déverrouillé par **code secret**.
* **Confirmation physique** par **NFC (tap #2)** pour autoriser l’envoi.
* **Chiffrement côté client** (**AES-GCM**), **intégrité** (**HMAC-SHA256**).
* **Anti-rejeu** : `timestamp` + `nonce` uniques.
* **Lecture unique** côté serveur (suppression après lecture).
 
---
 
## 🧱 Stack & exigences
 
* **Client** : PWA **HTML/CSS/JS** (Web NFC & Web Crypto).
* **Serveur** : **PHP 8+** + **SQLite3** (fichiers `send.php`, `inbox.php`).
* **NFC** : tag **NDEF** (URL) intégré au capuchon du stylo.
* **Navigateurs** : cible **Android/Chrome** (HTTPS recommandé pour la fiabilité Web NFC).
  *(iOS : Web NFC non supporté en PWA ; hors périmètre du prototype.)*
 
---
 
## 📁 Arborescence du dépôt
 
```
/OLYTS
├─ index.html          # UI PWA (détection stylo, code secret, envoi)
├─ style.css           # Styles
├─ script.js           # Logique NFC, préparation paquet, envoi
├─ index.php           # Accueil/serveur (si besoin)
├─ send.php            # API : réception, HMAC, anti-rejeu, enregistrement
├─ inbox.php           # API : lecture unique (retour + suppression)
├─ messages.db         # Base SQLite (démo) ou script de création équivalent
└─ README.md           # Ce fichier
```
 
---
 
## 🚀 Démarrage rapide
 
### 1) Pré-requis
 
* **PHP 8+** avec **sqlite3** activé.
* Un **smartphone Android** avec **NFC** et **Chrome**.
* Un **tag NFC** (NTAG 21x, etc.).
 
### 2) Lancer le serveur
 
Coller le NFC sur le telephone
 
### 3) Programmer le tag NFC
 
Avec l’app **NFC Tools** (Android) → **Write** → **Add a record** → **URL/URI** :
 
https://sunnybot56.free.nf/?i=1
 
Écris la fiche sur le tag et place-le dans le **capuchon**.
 
### 4) Démo (flux)
 
1. **Tap #1** : approche le stylo → la **PWA** s’ouvre & détecte le stylo.
2. Entre le **code secret** via le chatbot du site → l’UI passe en **mode espion**.
3. Rédige le **message** → clique **Préparer** (chiffrement + métadonnées).
4. **Tap #2** : re-présente le **même stylo** → **envoi** autorisé.
5. Côté destinataire : lecture **une seule fois** via `inbox.php` → suppression.
 
---
## 🔌 API (résumé)
 
### POST `/send.php`
 
**Entrée (JSON)** : `pen_id, ciphertext, iv, nonce, timestamp, hmac, target`
**Sortie** : `{"status":"ok","id":<int>}` ou erreur JSON.
 
### GET `/inbox.php?user=<id>`
 
Retourne **une seule fois** le prochain message pour `user`, puis **DELETE**.
**Sortie** : `{"ciphertext":"…","iv":"…","pen_id":"…","timestamp":…}` ou `404`.
 
> 🔐 **Confidentialité** : le **message en clair n’est jamais stocké** côté serveur.
 
---
 
## 🧪 Tests rapides
 
* **Ouverture NFC** ≤ 5 s.
* **Mauvais code** → refus ; **bon code** → accès.
* **Sans tap #2** → **pas d’envoi**.
* **Rejeu** du même paquet → **refus** (nonce déjà vu / timestamp périmé).
* **Lecture unique** : 1ʳᵉ OK, 2ᵉ → 404.
 
---
 
## 🧩 Dialogflow
 
Le chatbot **Dialogflow** est **lié** depuis la PWA. Cette brique est **indépendante** du flux **messagerie secrète** et est requise pour la démo NFC → envoi → lecture unique.
 
---
 
## 👥 Équipe
 
* **Programmation** : John, Steve — PWA, messagerie secrète, hébergement, Dialogflow.
* **Conception 3D & doc** : Katia, Lena — impression du stylo, intégration NFC, cahier des charges, slides.
 
---
 
## 📜 Licence & usage
 
* Projet **pédagogique**. Re-utilisation libre pour apprentissage ; ne pas employer en contexte réel d’espionnage.
 
diaporama : https://www.canva.com/design/DAGzOFYYxo0/6nJ-lQ8J5-0_ms0f14_Plw/edit?utm_content=DAGzOFYYxo0&ut…                                                                                                        trello : https://trello.com/b/UjipC8Nb/mon-tableau-trello
