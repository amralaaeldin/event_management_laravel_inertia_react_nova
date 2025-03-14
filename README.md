# Laravel Event Management App

This is a Laravel-based application designed for event management with user roles, event scheduling, and capacity constraints. The application includes an admin panel using Laravel Nova and a separate front-end built with InertiaJS (React.JS).

## Features

-   **User Roles**:
    -   Admin: Can create, edit, delete, and view all events (both draft and published).
    -   User: Can only view published events and join them.
-   **Admin Panel**:
    -   Manage events (CRUD operations) using Laravel Nova.
-   **User Registration & Event Participation**:

    -   Users can register and join events with capacity and overlapping constraints:
    -   -   Users cannot join the same event twice.
    -   -   Users cannot join overlapping events on the same day.
    -   Users receive confirmation emails upon registration.
    -   Reminder notifications are sent on the day of the event.
    -   Users see only published events in a calendar view
    -   Fetch events depending on the view (Day / Week / Month) which keeps queries performant.
    -   Highlighted UI for events the user has joined or been waitlisted for.

## Requirements

-   PHP 8.2 or higher
-   Laravel 12.0
-   Inertiajs 2.0
-   Laravel Nova 5.0
-   Laravel Sanctum 4.0
-   Composer
-   MySQL (or another database)
-   Node.js

## Installation

#### Step 1: Clone the Repository & Install Dependencies

```bash
git clone https://github.com/amralaaeldin/event_management_laravel_inertia_react_nova
cd event_management_laravel_inertia_react_nova
composer install
npm install
cp .env.example .env
```

#### Step 2: Define Environment Variables

Open the `.env` file and configure your environment variables, including database credentials & email configurations.

#### Step 3: Generate an Application Key, Set Up the database tables & Seed the Roles

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
```

#### Step 4: Start the Development Server

```bash
php artisan serve
npm run dev
```

-   Admin routes:

-   -   `/nova/login` - Admin login page
-   -   `/nova/resources/events` - Admin panel for managing events
-   -   `/nova/resources/users` - Admin panel for managing users
-   -   `/nova/resources/roles` - Admin panel for managing roles

-   Frontend routes:

-   -   `/register` - User registration page
-   -   `/login` - User login page
-   -   `/dashboard` - User dashboard
-   -   `/calendar` - User calendar view

```bash
You can access the application at http://localhost:8000
User Credentials:
Email: user@example.com
Password: 12345678

And the admin panel at http://localhost:8000/nova/login
Admin Credentials:
Email: admin@example.com
Password: 12345678
```
