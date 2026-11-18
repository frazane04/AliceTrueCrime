# AliceTrueCrime

A community web platform for exploring and documenting real true crime cases. Visitors can browse and search a catalogue of historical cases. Registered users can comment and submit new cases, which go through an admin approval workflow before being published.

Built as a team project for the **Web Technologies** course of the BSc in Computer Science at the University of Padua.

> The user interface and the case content are in Italian.

## Features

**Browsing and search**
- Catalogue of 25+ real historical cases, each with its story, victims, perpetrators and linked press articles
- Search across titles, descriptions and locations, with filters by case type and year
- Most-viewed cases on the home page, based on a view counter per case
- SEO-friendly URLs based on slugs (`/esplora/<case-slug>`)

**Users and community**
- Registration and login with hashed passwords (`password_hash` / `password_verify`)
- "Remember me" sessions using hashed persistent tokens
- Editable user profile and comments on cases
- Opt-in email newsletter sent through the Brevo API when a new case is published

**Content management**
- Users can submit new cases, including any number of victims, perpetrators and articles
- Role-based approval workflow: submitted and edited cases stay hidden until an admin approves them. Admins can preview, approve, edit or delete a case.
- Image upload with server-side validation, resizing and conversion to WebP, plus automatic cleanup of orphaned images

**Quality**
- Accessibility work targeting **WCAG 2.2 AA**: semantic HTML, ARIA attributes, keyboard navigation and accessible forms
- Performance tuning with **Lighthouse**: WebP images, Gzip compression and browser caching via `.htaccess`
- Responsive layout, a light/dark theme that follows the system preference, and a dedicated print stylesheet
- Security basics: CSRF tokens on forms, prepared statements for database queries, and credentials kept out of the repository (`.env`, `src/db/config.php`)
- Custom 403, 404, 500 and 503 error pages

## Tech stack

| Layer | Technologies |
|-------|--------------|
| Backend | PHP (no framework), MySQL/MariaDB via `mysqli` |
| Frontend | HTML templates, CSS, vanilla JavaScript |
| Server | Apache with `mod_rewrite` (front controller in `index.php`) |
| External services | Brevo API (newsletter) |

## Project structure

```
├── index.php              # Front controller and router
├── .htaccess              # URL rewriting, compression, caching
├── src/
│   ├── pages/             # Page controllers (home, explore, case, submit, profile, ...)
│   ├── db/                # Database connection and data-access functions
│   ├── helpers/           # Utilities: CSRF, image handling, case-form helpers, newsletter
│   └── template/          # HTML layouts, pages, reusable components, error pages
├── css/                   # Main and print stylesheets
├── js/                    # Client-side validation and interactive features
├── assets/img/            # Static images and uploaded case pictures
└── db/AliceTrueCrimeDB.sql  # Database schema and sample data
```

## Getting started

**Requirements:** PHP 8 with the GD extension, MySQL or MariaDB, and Apache with `mod_rewrite`. XAMPP or MAMP work out of the box.

1. Clone the repository into your web server's document root:
   ```bash
   git clone https://github.com/frazane04/AliceTrueCrime.git
   ```
2. Create the database and import the schema and sample data:
   ```bash
   mysql -u root -p -e "CREATE DATABASE alicetruecrime"
   mysql -u root -p alicetruecrime < db/AliceTrueCrimeDB.sql
   ```
3. Create a `.env` file in the project root:
   ```ini
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=your_password
   DB_NAME=alicetruecrime
   ```
4. *(Optional, for the newsletter)* Create `src/db/config.php` with your Brevo API key:
   ```php
   <?php
   return ['brevo_api_key' => 'your_brevo_api_key'];
   ```
5. Open `http://localhost/AliceTrueCrime/` in your browser.

## Team

- Francesco Zanella
- Lorenzo Grolla
- Samuele Perozzo
- Alice Zago
