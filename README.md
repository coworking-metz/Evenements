### Application d'organisation d'événements

---

#### À propos de l'application
Cette application aide à organiser des événements et à collecter les réponses des invités. Pour les organisateurs, il existe une section spéciale où vous pouvez créer des événements, voir qui a répondu, et gérer les détails de l'événement.

#### Comment ça marche ?
On peut envoyer par mail le lien d'évènement, et les invités peuvent répondre directement sur le site de l'événement.
L'app propose un tableau de bord où on peut gérer les événements, voir qui a accepté ou décliné l'invitation, et mettre à jour les informations de l'événement à tout moment.

#### Détails techniques pour l'installation
L'application est composée de plusieurs fichiers et dossiers qui supportent son fonctionnement :
- **Fichiers principaux** :
  - **index.php** : La page principale que visitent les utilisateurs.
  - **admin.php** : La page d'administration pour créer et gérer les événements.
  - **ping.php** : Un script pour vérifier le bon fonctionnement du site.

#### Comment installer et démarrer ?
1. **Préparation** :
   - Créez un fichier de configuration pour les secrets de l'application en copiant `secrets.inc.php.modele` en `secrets.inc.php` et en le remplissant avec les informations nécessaires.
2. **Utilisation de Docker** :
   - Installez Docker si ce n'est pas déjà fait.
   - Lancez l'application avec la commande `docker-compose up` dans le répertoire de l'application.
3. **Installation manuelle** :
   - Configurez un serveur web local capable de gérer PHP (Apache est recommandé).
   - Placez l'application dans le dossier du serveur et assurez-vous que le module mor_rewrite est actif.

#### Utilisation 
-  Ouvrir `admin.php` pour visualiser la liste des événements.

