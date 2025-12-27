# 🎓 YIHAA - Student Collaboration & Socialization Platform

YIHAA is a modern web platform designed to facilitate student collaboration, socialization, and academic engagement. Students can exchange posts, discover events such as seminars, competitions, and scholarships, share learning materials, collaborate through team features with real-time chat, and build meaningful connections with peers. Built with modern technologies including Laravel 12, Tailwind CSS, and Supabase for secure authentication.

## 📋 Table of Contents

-   [Key Features](#key-features)
-   [Tech Stack](#tech-stack)
-   [System Requirements](#system-requirements)
-   [Installation & Setup](#installation--setup)
-   [Database Configuration](#database-configuration)
-   [Project Structure](#project-structure)
-   [API Endpoints](#api-endpoints)
-   [User Guide](#user-guide)
-   [Admin Guide](#admin-guide)
-   [Troubleshooting](#troubleshooting)

---

## ✨ Key Features

### 🔐 Authentication & User Management

-   Register and login with Supabase Auth
-   Role-based access control (Admin, User)
-   User profiles with avatar support
-   Auto-sync users from Supabase to local database
-   Secure JWT token-based authentication

### 📝 Post & Feed Features

-   Create and share posts with peers
-   Comment on posts
-   Like functionality
-   Rich media support (images, attachments)
-   Feed discovery and browsing

### 🎯 Events & Opportunities

-   **Seminars**: Browse and register for seminars
-   **Competitions**: Discover competitions and contests
-   **Scholarships**: Find and explore scholarship programs
-   Event registration tracking
-   Event notifications

### 👥 Team Collaboration

-   Create new teams for projects and groups
-   Join existing teams
-   Manage team members with different roles
-   Real-time team chat messaging
-   Team notifications
-   Share resources within teams
-   Team logo and branding

### 📚 Learning Materials

-   Upload and share learning materials
-   Organize materials by categories
-   Browse and download educational content
-   Material management system
-   Content organization and search

### 💬 Communication & Engagement

-   Real-time notifications system
-   Team chat messaging
-   Direct interactions between students
-   Comment threads on posts
-   Activity notifications

### 📊 Admin Dashboard

-   Manage events (seminars, competitions, scholarships)
-   Control learning materials
-   Monitor user activities
-   Manage user roles and permissions
-   Platform analytics and insights

---

## 🛠️ Tech Stack

### Backend

| Technology      | Version | Purpose                    |
| --------------- | ------- | -------------------------- |
| **Laravel**     | 12.0    | PHP Web Framework          |
| **PHP**         | 8.2+    | Server-side Language       |
| **PostgreSQL**  | Latest  | Primary Database           |
| **Supabase**    | 2.80+   | Authentication & Real-time |
| **JWT**         | 6.11    | Token-based Authentication |
| **Guzzle HTTP** | 7.10+   | HTTP Client Library        |

### Frontend

| Technology       | Version | Purpose                          |
| ---------------- | ------- | -------------------------------- |
| **Tailwind CSS** | 4.1+    | Utility-first CSS Framework      |
| **Vite**         | 7.0+    | Modern Build Tool & Dev Server   |
| **Alpine.js**    | 3.4+    | Lightweight JavaScript Framework |
| **Preline**      | 3.2+    | UI Component Library             |
| **Axios**        | 1.11+   | HTTP Client Library              |

### Development Tools

| Tool            | Purpose                    |
| --------------- | -------------------------- |
| **Composer**    | PHP Dependency Manager     |
| **npm/yarn**    | JavaScript Package Manager |
| **Artisan CLI** | Laravel Command Line Tool  |
| **PHPUnit**     | Testing Framework          |

---

## 💻 System Requirements

### Minimum Requirements

-   **PHP**: 8.2 or higher
-   **Node.js**: 18+ (for frontend build)
-   **Composer**: 2.0+
-   **Database**: PostgreSQL 12+
-   **OS**: Windows, macOS, or Linux

### Required Tools

-   Text Editor / IDE (VS Code, PHPStorm, etc.)
-   Git for version control
-   Terminal/Command Prompt
-   Modern web browser (Chrome, Firefox, Safari, Edge)

---

## 🚀 Installation & Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd yihaaa-app
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install
```

### 4. Setup Environment

```bash
# Copy example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure `.env` File

Edit `.env` and set the following values:

```env
# Application
APP_NAME="YIHAA"
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:xxxxx  # Auto-generated from php artisan key:generate

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=yihaaa_db
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Supabase Configuration
SUPABASE_URL=https://qdfotopajdiuailyeprh.supabase.co
SUPABASE_KEY=your_supabase_anon_key
SUPABASE_JWT_SECRET=your_jwt_secret

# Mail Configuration (Optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=no-reply@yihaa.com
MAIL_FROM_NAME="YIHAA"
```

### 6. Setup Database

```bash
# Run database migrations
php artisan migrate

# (Optional) Seed the database with sample data
php artisan db:seed
```

### 7. Build Frontend Assets

```bash
# Development mode
npm run dev

# Production build
npm run build
```

### 8. Start Development Server

```bash
# Terminal 1: Start Laravel development server
php artisan serve

# Terminal 2: Start Vite development server (for hot reload)
npm run dev
```

The application will be available at: `http://localhost:8000`

---

## 🗄️ Database Configuration

### Setup Database Triggers (Supabase)

YIHAA uses Supabase for authentication. Database triggers are required to auto-sync new users from Supabase Auth to the `public.users` table.

#### Steps:

1. **Open Supabase Dashboard**

    - URL: https://supabase.com/dashboard/project/qdfotopajdiuailyeprh
    - Click **SQL Editor** in the left menu

2. **Run Setup Script**

    - Create a **New Query**
    - Copy-paste the contents from file: `database/supabase_trigger_auto_create_user.sql`
    - Click **Run** or press Ctrl+Enter

3. **Verify Trigger Successfully Created**
    ```sql
    SELECT * FROM pg_trigger WHERE tgname = 'on_auth_user_created';
    SELECT proname FROM pg_proc WHERE proname = 'handle_new_user';
    ```
    Should display 2 rows.

### Database Schema

#### `users` Table

```sql
- id (BIGINT PRIMARY KEY)
- name (VARCHAR)
- email (VARCHAR UNIQUE)
- email_verified_at (TIMESTAMP)
- password (VARCHAR)
- remember_token (VARCHAR)
- supabase_id (UUID UNIQUE, NULLABLE)
- avatar_url (TEXT, NULLABLE)
- role (VARCHAR DEFAULT 'user') -- 'admin' or 'user'
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### Other Main Tables

-   **posts**: User posts and articles
-   **teams**: Team collaboration management
-   **team_members**: Team members
-   **materials**: Learning materials
-   **seminars**: Seminar data
-   **scholarships**: Scholarship programs
-   **competitions**: Competition/contest data
-   **chat_teams**: Team chat messages
-   **notifications**: System notifications

### Setup Admin Role

#### Method 1: Via Supabase SQL Editor

```sql
UPDATE public.users
SET role = 'admin'
WHERE email = 'your-email@example.com';
```

#### Method 2: Via Supabase Table Editor

1. Open **Table Editor** → Select `users` table
2. Find the user you want to make admin
3. Double-click the `role` column
4. Change value from `user` to `admin`

#### Method 3: Via Laravel Tinker

```bash
php artisan tinker
User::where('email', 'admin@example.com')->update(['role' => 'admin']);
exit
```

---

## 📁 Project Structure

```
yihaaa-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Business logic & request handling
│   │   │   ├── AdminEventController.php
│   │   │   ├── AdminMaterialController.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── EventController.php
│   │   │   ├── HomeController.php
│   │   │   ├── PostController.php
│   │   │   ├── TeamController.php
│   │   │   └── ... (other controllers)
│   │   ├── Middleware/          # Request middleware
│   │   └── Requests/            # Form request validation
│   ├── Models/                  # Database models (Eloquent ORM)
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── Team.php
│   │   └── ... (other models)
│   ├── Services/                # Business logic & helper services
│   ├── Helpers/                 # Helper functions
│   ├── Providers/               # Service providers
│   └── View/                    # View composers & logic
├── routes/
│   ├── api.php                  # API routes (prefix: /api)
│   ├── home.php                 # Home/public routes
│   ├── admin.php                # Admin routes (protected)
│   ├── auth.php                 # Auth routes
│   ├── materi.php               # Learning material routes
│   └── ... (other route files)
├── resources/
│   ├── views/
│   │   ├── components/          # Reusable Blade components
│   │   ├── emails/              # Email templates
│   │   ├── pages/               # Page templates
│   │   └── layouts/             # Layout templates
│   ├── css/
│   │   └── app.css             # Global Tailwind styles
│   └── js/
│       ├── app.js              # Main JavaScript entry
│       └── ... (JS modules)
├── database/
│   ├── migrations/              # Database migration files
│   ├── seeders/                 # Database seeders
│   ├── factories/               # Model factories for testing
│   ├── supabase_trigger_auto_create_user.sql
│   └── supabase_profiles_schema.sql
├── config/
│   ├── app.php                 # Application configuration
│   ├── auth.php                # Authentication configuration
│   ├── database.php            # Database configuration
│   ├── supabase.php            # Supabase configuration
│   └── ... (other config files)
├── public/
│   ├── index.php               # Entry point
│   ├── build/                  # Compiled assets (auto-generated)
│   └── robots.txt
├── storage/                     # Logs, cache, file uploads
├── bootstrap/
│   ├── app.php                 # Bootstrap application
│   ├── cache/                  # Cached bootstrap files
│   └── providers.php
├── tests/                       # PHPUnit test files
├── vendor/                      # Composer dependencies
├── node_modules/               # npm dependencies
├── .env                        # Environment variables
├── .env.example                # Example environment
├── composer.json               # PHP dependencies
├── package.json                # JavaScript dependencies
├── vite.config.js             # Vite configuration
├── tailwind.config.js         # Tailwind CSS configuration
├── phpunit.xml                # PHPUnit configuration
└── README.md                  # Documentation (this file)
```

---

## 🔌 API Endpoints

### Authentication Routes

```
POST   /api/auth/register        # Register new user
POST   /api/auth/login           # Login user
POST   /api/auth/logout          # Logout user
POST   /api/auth/refresh         # Refresh JWT token
GET    /api/auth/me              # Get current user info
```

### User Routes

```
GET    /api/users                # Get list of users
GET    /api/users/{id}           # Get user detail
PUT    /api/users/{id}           # Update user profile
DELETE /api/users/{id}           # Delete user
```

### Posts Routes

```
GET    /api/posts                # Get all posts
POST   /api/posts                # Create new post
GET    /api/posts/{id}           # Get post detail
PUT    /api/posts/{id}           # Update post
DELETE /api/posts/{id}           # Delete post
POST   /api/posts/{id}/comments  # Add comment
POST   /api/posts/{id}/like      # Like post
```

### Teams Routes

```
GET    /api/teams                # Get user's teams
POST   /api/teams                # Create new team
GET    /api/teams/{id}           # Get team detail
PUT    /api/teams/{id}           # Update team
DELETE /api/teams/{id}           # Delete team
POST   /api/teams/{id}/members   # Add team member
DELETE /api/teams/{id}/members/{memberId}  # Remove member
GET    /api/teams/{id}/chat      # Get team chat
POST   /api/teams/{id}/chat      # Send chat message
```

### Events Routes

```
GET    /api/events               # Get all events
GET    /api/events/seminars      # Get seminars
GET    /api/events/competitions  # Get competitions
GET    /api/events/scholarships  # Get scholarships
POST   /api/events/{id}/register # Register for event
```

### Materials Routes

```
GET    /api/materials            # Get all materials
GET    /api/materials/{id}       # Get material detail
POST   /api/materials            # Upload material (admin)
```

### Admin Routes

```
GET    /admin                           # Dashboard
GET    /admin/events                    # Manage events
POST   /admin/events/seminar            # Create seminar
PUT    /admin/events/seminar/{id}       # Update seminar
DELETE /admin/events/seminar/{id}       # Delete seminar
GET    /admin/materials                 # Manage materials
POST   /admin/materials                 # Upload material
```

---

## 📖 User Guide

### For Regular Students

#### 1. Registration & Login

-   Click the **Register** button on the home page
-   Enter your email and password
-   Verify your email (if required)
-   Login with your credentials

#### 2. Creating Posts

1. Go to **Home** or **Feed** page
2. Click the **Create Post** button
3. Enter your post content
4. Attach images/files if needed
5. Click **Publish**

#### 3. Creating a Team

1. Click **Teams** menu in the navbar
2. Click **Create New Team** button
3. Enter team name and description
4. Upload team logo (optional)
5. Click **Create**

#### 4. Managing Your Team

-   Add members using invite link or email
-   Chat with team members
-   Share resources within the team
-   Manage member roles (Owner, Admin, Member)

#### 5. Accessing Learning Materials

1. Click **Materials** menu in the navbar
2. Browse materials by category
3. Download or preview materials
4. Share materials with your team

#### 6. Registering for Events

1. Click **Events** menu (Seminars, Competitions, Scholarships)
2. Select the event you want to join
3. Click **Register Now**
4. Fill in the registration form
5. Submit and wait for confirmation

---

## 📊 Admin Guide

#### 1. Accessing Admin Dashboard

-   Login with an account that has `admin` role
-   Access via URL: `http://localhost:8000/login-admin`

#### 2. Event Management

```
Admin Dashboard → Events Management
```

-   **View**: See all events (seminars, competitions, scholarships)
-   **Create**: Create new events
-   **Edit**: Update event information
-   **Delete**: Remove events
-   **Registrations**: View list of registered participants

#### 3. Materials Management

```
Admin Dashboard → Materials Management
```

-   Upload new learning materials
-   Categorize materials
-   Edit titles and descriptions
-   Delete materials if needed
-   Monitor download/access statistics

#### 4. User Management

-   View all registered users
-   Manage user roles
-   Suspend/reactivate user accounts
-   Monitor user activity

---

## 🐛 Troubleshooting

### Error: "SQLSTATE[08006]: Connection refused"

**Problem**: PostgreSQL database is not connected

**Solution**:

1. Make sure PostgreSQL service is running
2. Check configuration in `.env` (host, port, username, password)
3. Ensure the database has been created
4. Test connection with command:
    ```bash
    php artisan db:show
    ```

### Error: "Supabase connection failed"

**Problem**: Cannot connect to Supabase

**Solution**:

1. Make sure URL and KEY in `.env` are correct
2. Check internet connection
3. Verify Supabase project status in dashboard
4. Clear cache:
    ```bash
    php artisan config:clear
    php artisan cache:clear
    ```

### Error: "Vite manifest not found"

**Problem**: Assets are not built properly

**Solution**:

1. Rebuild assets:
    ```bash
    npm run build
    ```
2. Or run the dev server:
    ```bash
    npm run dev
    ```
3. Clear Laravel cache:
    ```bash
    php artisan view:clear
    ```

### Error: "Migration table not found"

**Problem**: Migrations have not been run

**Solution**:

```bash
php artisan migrate
```

### Error: "Class 'App\Models\User' not found"

**Problem**: Autoloading not refreshed

**Solution**:

```bash
composer dump-autoload
php artisan optimize
```

### Application Running Slowly

**Solution**:

1. Clear cache:
    ```bash
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    php artisan config:clear
    ```
2. Run optimization:
    ```bash
    php artisan optimize
    ```
3. Check database queries:
    ```bash
    php artisan tinker
    DB::enableQueryLog();
    // run queries
    dd(DB::getQueryLog());
    ```

---

## 📝 Development Workflow

### Creating New Features

#### 1. Backend (API/Business Logic)

```bash
# 1. Create controller
php artisan make:controller MyFeatureController

# 2. Create model & migration
php artisan make:model MyModel -m

# 3. Create service class (for business logic)
php artisan make:class Services/MyFeatureService

# 4. Add route in routes/api.php or routes/home.php

# 5. Run migration
php artisan migrate

# 6. Test with Tinker or API client (Postman, Insomnia)
```

#### 2. Frontend (UI/Components)

```bash
# 1. Create Blade component
php artisan make:component MyComponent

# 2. Create Vue/Alpine component (if needed)
# Add file to resources/js/components/

# 3. Import in template
# In Blade: <x-my-component />

# 4. Build with Vite
npm run dev
```

### Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/AuthTest.php

# Run with coverage report
php artisan test --coverage

# Run test with verbose output
php artisan test --verbose
```

---

## 🔒 Security Best Practices

### Environment Variables

-   Never hardcode sensitive data
-   Use `.env` for configuration
-   Don't commit `.env` to git (use `.env.example`)

### Authentication

-   Always validate users with `auth.check` middleware
-   Use role-based middleware for admin access: `middleware(['auth.check', 'admin'])`
-   Validate JWT tokens from Supabase

### Database

-   Use parameterized queries (Eloquent ORM)
-   Avoid raw SQL queries when possible
-   Protect against SQL injection

### File Upload

-   Validate file type & size
-   Store uploaded files in `storage/` not `public/`
-   Use asset URLs to serve files

### CSRF Protection

-   CSRF tokens are automatically included in Blade forms
-   Include `@csrf` directive in forms

---

## 📚 Additional Documentation

For more detailed documentation:

-   [Laravel Documentation](https://laravel.com/docs)
-   [Supabase Documentation](https://supabase.com/docs)
-   [Tailwind CSS Documentation](https://tailwindcss.com/docs)
-   [Vite Documentation](https://vitejs.dev/)

---

## 🤝 Contributing

Contributions are greatly appreciated! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 👥 Development Team

**YIHAA Development Team**

-   Contributors: Development Team
-   Last Updated: December 2025

---

**Happy Coding! 🚀**
