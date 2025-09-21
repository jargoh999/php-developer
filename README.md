# Laravel Blog API

A robust RESTful API for a blog application built with Laravel. This API provides endpoints for user authentication and blog post management.

## Features

- **User Authentication**
  - User registration and login with JWT
  - Protected routes for authenticated users
  - User profile management

- **Blog Posts**
  - Create, read, update, and delete blog posts
  - View all posts with pagination
  - Search posts by title or content
  - View single post by slug

- **Security**
  - JWT Authentication
  - Input validation
  - Rate limiting
  - CORS support

## Tech Stack

- **Backend**: Laravel 10.x
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/PostgreSQL/SQLite
- **API Documentation**: OpenAPI (Swagger)

## Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (for frontend assets)

## Installation

1. **Clone the repository**
   ```bash
   git clone [your-repository-url]
   cd laravel-blog-api
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Update your `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_blog
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Generate JWT secret key**
   ```bash
   php artisan jwt:secret
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Build assets (for development)**
   ```bash
   npm run dev
   ```

The API will be available at `http://localhost:8000/api`

## API Documentation

### Authentication

#### Register a new user
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password"
}
```

#### Get authenticated user
```http
GET /api/me
Authorization: Bearer your_jwt_token
```

#### Logout
```http
POST /api/logout
Authorization: Bearer your_jwt_token
```

### Posts

#### Get all posts
```http
GET /api/posts
```

#### Get a single post
```http
GET /api/posts/{slug}
```

#### Create a new post
```http
POST /api/posts
Authorization: Bearer your_jwt_token
Content-Type: application/json

{
    "title": "My First Post",
    "body": "This is the content of my first post."
}
```

#### Update a post
```http
PUT /api/posts/{slug}
Authorization: Bearer your_jwt_token
Content-Type: application/json

{
    "title": "Updated Post Title",
    "body": "Updated content of the post."
}
```

#### Delete a post
```http
DELETE /api/posts/{slug}
Authorization: Bearer your_jwt_token
```

#### Search posts
```http
GET /api/posts/search?q=search+term
```

## Testing

Run the test suite:

```bash
php artisan test
```

## Environment Variables

The following environment variables need to be set in your `.env` file:

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_blog
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## Security Vulnerabilities

If you discover a security vulnerability within this application, please send an e-mail to the maintainer. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contributing
0. git clone https://github.com/jargoh999/php-developer 
1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
