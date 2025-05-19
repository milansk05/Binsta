# Binsta - Instagram voor Code

Binsta is een socialmediaplatform waar programmeurs hun code snippets kunnen delen, liken en becommentariëren. Het combineert het beste van Instagram met de behoeften van ontwikkelaars door een visueel aantrekkelijke manier te bieden om code te delen met syntax highlighting, gebruikersprofielen, en een interactieve feed.

## 📑 Inhoudsopgave

- [Features](#-features)
- [Technische Stack](#-technische-stack)
- [Projectstructuur](#-projectstructuur)
- [Installatie](#-installatie)
- [Database Schema](#-database-schema)
- [Controllers & Routes](#-controllers--routes)
- [Models](#-models)
- [Views/Templates](#-viewstemplates)
- [Authenticatie & Beveiliging](#-authenticatie--beveiliging)
- [Frontend Functionaliteiten](#-frontend-functionaliteiten)
- [Testgebruikers](#-testgebruikers)
- [Bekend Issues & Beperkingen](#-bekende-issues--beperkingen)
- [Toekomstige Uitbreidingen](#-toekomstige-uitbreidingen)
- [License](#-license)

## 🌟 Features

- **Modern Design**
  - Responsive interface voor desktop en mobiel
  - Donkere modus als standaard met toggle naar lichte modus
  - Side navigation voor desktop, hamburger voor mobiel

- **Gebruikersbeheer**
  - Account registratie en login met e-mail validatie
  - Profielpagina met persoonlijke code feed
  - Profiel bewerken (bio, gebruikersnaam, profielfoto)
  - Wachtwoord wijzigen met huidige wachtwoordbevestiging

- **Code Sharing**
  - Ondersteuning voor 15+ programmeertalen met syntax highlighting
  - Code editor met indentatie en regelomslag functies
  - Kopieer-knop voor code met toast bevestiging
  - Caption/beschrijving toevoegen aan posts

- **Interactie**
  - Like-systeem voor posts (dynamisch bijgewerkt via AJAX)
  - Commentaarsysteem (real-time via AJAX)
  - Zoekfunctionaliteit voor gebruikers
  - Taalstatistieken op de profielpagina

- **Codeweergave**
  - Syntax highlighting via highlight.js
  - Automatische taalbadges
  - Lijnnummers voor code
  - Opmaak van code snippets

## 📋 Technische Stack

### Backend
- **PHP 8.1+**: Core programmeertaal
- **RedBeanPHP ORM**: Database abstractielaag en ORM
- **Twig**: Template engine voor views
- **dotenv**: Voor configuratiebeheer via .env bestanden

### Frontend
- **HTML5/CSS3**: Basisstructuur en styling
- **JavaScript**: Client-side functionaliteit
- **highlight.js**: Voor code syntax highlighting
- **Font Awesome**: Voor iconen
- **Google Fonts**: Voor typography (Inter, JetBrains Mono, Fira Code)

### Database
- **MySQL**: Primaire database

### Andere Tools
- **Composer**: PHP dependency management

## 🗂 Projectstructuur

Het project volgt een aangepaste MVC (Model-View-Controller) architectuur:

```
binsta/
├── app/
│   ├── controllers/              # Controllers voor de applicatie logica
│   │   ├── AuthController.php    # Authenticatie (login/register)
│   │   ├── BaseController.php    # Basis controller met gemeenschappelijke methoden
│   │   ├── CommentController.php # Commentaar functionaliteit
│   │   ├── HomeController.php    # Homepage / feed
│   │   ├── PostController.php    # Post CRUD functionaliteit
│   │   ├── ProfileController.php # Gebruikersprofielen
│   │   └── SearchController.php  # Zoekfunctionaliteit
│   │
│   ├── core/                     # Core systeembestanden
│   │   ├── database.php          # Database configuratie 
│   │   ├── router.php            # Routing systeem
│   │   └── seeder.php            # Database seeder voor testdata
│   │
│   ├── models/                   # Datamodellen
│   │   ├── Post.php              # Post model met like/comment functionaliteit
│   │   └── User.php              # User model
│   │
│   └── views/                    # Twig templates
│       ├── auth/                 # Authenticatie templates
│       │   ├── login.twig        # Login formulier
│       │   └── register.twig     # Registratie formulier
│       │
│       ├── home/                 # Homepage templates
│       │   └── index.twig        # Feed weergave
│       │
│       ├── posts/                # Post-gerelateerde templates
│       │   ├── create.twig       # Post aanmaken
│       │   └── show.twig         # Post detail weergave
│       │
│       ├── profile/              # Profiel templates
│       │   ├── edit.twig         # Profiel bewerken
│       │   └── show.twig         # Profiel weergeven
│       │
│       ├── search/               # Zoek templates
│       │   └── index.twig        # Zoekresultaten
│       │
│       ├── errors/               # Error pagina's
│       │   └── 404.twig          # 404 pagina
│       │
│       └── layout.twig           # Basis layout template
│
├── public/                       # Publiek toegankelijke bestanden
│   ├── css/                      # CSS bestanden
│   │   └── styles.css            # Hoofdstijlbestand
│   │
│   ├── js/                       # JavaScript bestanden
│   │   └── scripts.js            # Hoofd JavaScript bestand
│   │
│   ├── uploads/                  # Geüploade bestanden (profielfoto's)
│   │   └── placeholder_image.png # Standaard profielfoto
│   │
│   └── index.php                 # Entry point
│
├── vendor/                       # Composer dependencies
├── .env                          # Configuratie bestand
├── .htaccess                     # Apache rewrite rules
├── composer.json                 # Composer configuratie
├── composer.lock                 # Composer lock bestand 
└── README.md                     # Projectdocumentatie
```

## 🚀 Installatie

### Vereisten

- PHP 8.1 of hoger
- MySQL Server
- Composer
- Webserver (Apache/Nginx)

### Stap 1: Project ophalen

Clone de repository:

```bash
git clone gitrepo
cd binsta
```

### Stap 2: Dependencies installeren

```bash
composer install
```

### Stap 3: Database instellen

Maak een nieuwe MySQL database aan:

```sql
CREATE DATABASE binsta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Stap 4: Configuratie

Maak een `.env` bestand in de root van het project:

```
DB_HOST=localhost
DB_NAME=binsta
DB_USER=jouw_database_user
DB_PASS=jouw_database_password
```

### Stap 5: Upload directory instellen

```bash
mkdir -p public/uploads
chmod 775 public/uploads
```

### Stap 6: Test data inladen (optioneel)

```bash
php app/core/seeder.php
```

### Stap 7: Webserver configureren

#### Voor Apache

Zorg ervoor dat mod_rewrite is ingeschakeld. De `.htaccess` bestanden zijn al bijgevoegd in het project:

- Root `.htaccess`: Stuurt alles naar de public directory
- Public `.htaccess`: Stuurt verzoeken naar index.php

#### Voor Nginx

Voeg het volgende toe aan je server configuratie:

```nginx
server {
    listen 80;
    server_name binsta.local;
    root /path/to/binsta/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }
}
```

### Stap 8: Browser openen

Navigeer naar `http://localhost/` of je geconfigureerde domein.

## 💾 Database Schema

Binsta gebruikt RedBeanPHP als ORM, waarbij de database schema automatisch wordt aangemaakt. Hier is een overzicht van de tabellen:

### user
- `id` - Primary Key
- `username` - Gebruikersnaam
- `email` - E-mailadres
- `password` - Gehashed wachtwoord
- `bio` - Gebruikersbiografie
- `profile_image` - Bestandsnaam van profielfoto
- `created_at` - Registratiedatum

### post
- `id` - Primary Key
- `user_id` - Foreign key naar user
- `code` - De code snippet
- `language` - Programmeertaal
- `caption` - Beschrijving
- `created_at` - Aanmaakdatum

### like
- `id` - Primary Key
- `post_id` - Foreign key naar post
- `user_id` - Foreign key naar user
- `created_at` - Like datum

### comment
- `id` - Primary Key
- `post_id` - Foreign key naar post
- `user_id` - Foreign key naar user
- `content` - Inhoud van de reactie
- `created_at` - Reactiedatum

## 🛣 Controllers & Routes

### Routing Systeem

Het routing systeem bevindt zich in `app/core/router.php`. Het analyseert de URI en HTTP methode om de juiste controller en actie aan te roepen. Ondersteunde routes:

#### GET Routes
- `/` → `HomeController->index` - Toont de feed
- `/login` → `AuthController->loginForm` - Toont login formulier
- `/register` → `AuthController->registerForm` - Toont registratie formulier
- `/profile` → `ProfileController->show` - Toont gebruikersprofiel
- `/profile/edit` → `ProfileController->edit` - Toont profiel bewerken formulier
- `/search` → `SearchController->index` - Zoekfunctionaliteit
- `/posts/create` → `PostController->create` - Toont post aanmaken formulier
- `/posts/{id}` → `PostController->show` - Toont specifieke post

#### POST Routes
- `/login` → `AuthController->login` - Verwerkt login
- `/register` → `AuthController->register` - Verwerkt registratie
- `/logout` → `AuthController->logout` - Verwerkt logout
- `/profile/update` → `ProfileController->update` - Verwerkt profiel update
- `/profile/password` → `ProfileController->updatePassword` - Verwerkt wachtwoord wijziging
- `/posts/create` → `PostController->store` - Slaat nieuwe post op
- `/posts/like` → `PostController->like` - Verwerkt post likes/unlikes
- `/comments/create` → `CommentController->store` - Slaat commentaar op
- `/follow` → `ProfileController->follow` - Verwerkt user volgen/ontvolgen

### Controllers Overzicht

#### BaseController
- Basiscontroller die alle anderen erven
- Initialiseert Twig, sessies en databaseverbinding
- Biedt gemeenschappelijke functionaliteit (render, redirect, auth checks)

#### AuthController
- Beheert gebruikersauthenticatie
- Implementeert login, registratie en logout
- Valideert gebruikersinvoer

#### HomeController
- Toont de hoofdfeed met recente posts
- Laadt posts met eager loading voor betere prestaties

#### PostController
- CRUD operaties voor posts
- Like functionaliteit met AJAX ondersteuning
- Post detail weergave met commentaar

#### ProfileController
- Toont en bewerkt gebruikersprofielen
- Beheert profielfoto uploads
- Genereert taalstatistieken voor gebruikers

#### CommentController
- Beheert reacties op posts
- Ondersteunt AJAX voor real-time updates

#### SearchController
- Implementeert zoekfunctionaliteit voor gebruikers

## 📊 Models

Het project gebruikt RedBeanPHP ORM met model classes:

### User Model
`app/models/User.php`
- Beheert gebruikersgegevens
- Default profielfoto constante
- Methoden voor followers/following tellen

### Post Model
`app/models/Post.php`
- Beheert post gegevens
- Like/unlike functionaliteit
- Commentaar beheer
- Caching mechanisme voor like status

## 🖼 Views/Templates

Het project gebruikt Twig als template engine met de volgende structuur:

### Layout
`views/layout.twig` - Basistemplate met:
- HTML structuur
- CSS/JS imports
- Navigatiemenu (sidebar en mobile nav)
- Flash messages

### Authenticatie Views
- `views/auth/login.twig` - Login formulier
- `views/auth/register.twig` - Registratie formulier met wachtwoordsterkte meter

### Post Views
- `views/posts/create.twig` - Code editor met taal selector
- `views/posts/show.twig` - Post detail met code, likes, commentaar

### Profiel Views
- `views/profile/show.twig` - Gebruikersprofiel met posts en statistieken
- `views/profile/edit.twig` - Profiel bewerken formulier

### Feed View
- `views/home/index.twig` - Hoofdfeed met posts

### Zoek View
- `views/search/index.twig` - Zoekresultaten

## 🔒 Authenticatie & Beveiliging

### Authenticatie
- Wachtwoorden worden gehasht met `password_hash()` en PHP's native PASSWORD_DEFAULT algoritme
- Login status wordt bijgehouden in PHP sessies
- Niet-ingelogde gebruikers worden omgeleid naar de login pagina

### Beveiliging
- Formulierinvoer wordt gevalideerd aan server-side
- Bescherming tegen cross-site request forgery (CSRF) via sessiecontrole
- Uploaded bestanden worden gevalideerd op type en veilig hernoemd

## 💻 Frontend Functionaliteiten

### CSS Structuur
Het project gebruikt een enkel `styles.css` bestand met:
- CSS variabelen voor thema en consistentie
- Responsief grid systeem
- Donkere/lichte modus
- Modulair ontwerp voor componenten

### JavaScript Functionaliteit
Het `scripts.js` bestand bevat:
- Code syntax highlighting met highlight.js
- Like functionaliteit via AJAX
- Commentaar toevoegen via AJAX
- Code kopiëren naar klembord
- Donkere/lichte modus toggle
- Mobiele menu toggle

### Key Frontend Componenten

#### Code Editor
- Syntax highlighting
- Lijnnummering
- Taalbadge
- Kopieer knop

#### Feed Posts
- Interactieve like buttons
- Dynamische commentaarsectie
- Gebruikersprofiellinks

#### Profiel Statistieken
- Taalgebruik visualisatie
- Post telling

## 🧪 Testgebruikers

Bij gebruik van de seeder worden deze testgebruikers aangemaakt:

| Username            | Email              | Wachtwoord   |
|---------------------|-------------------|--------------|
| future-tech-leader  | tech@example.com  | password123  |
| code_wizard         | wizard@example.com | password123  |

## ⚠️ Bekende Issues & Beperkingen

1. **Ontbrekende sessie persistentie**: Likes worden niet altijd correct bijgehouden tussen paginanavigaties.
2. **Beperkte foutafhandeling**: Sommige foutmeldingen zijn generiek.
3. **Geen paginering**: De feed toont maximaal 20 posts zonder paginering.
4. **Beperkte zoekfunctionaliteit**: Alleen zoeken op gebruikersnaam, niet op code of taal.
