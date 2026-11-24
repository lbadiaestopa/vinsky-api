# 🎻 Vinsky API
<img width="2488" height="622" alt="image" src="https://github.com/user-attachments/assets/fce67391-37a1-4d8d-b28d-94b91d2acea1" />

A RESTful API for managing orchestras, their members, programs, events, and musical scores. It provides structured access control, allowing users to collaborate within orchestras, organize rehearsals and concerts, and manage shared sheet music through secure file uploads.

## 🛠️ Tech Stack
- **Backend:** PHP 8.5
- **Framework:** Laravel 13
- **Authentication:** Laravel Passport
- **Database:** MySQL
- **Email Delivery:** Gmail API with OAuth 2.0
- **Testing:** Pest
- **Documentation:** Scribe

## 🪄 Features
- Orchestra and membership management
- Project organization
- Rehearsal and concert scheduling
- Secure PDF score storage and access control
- OAuth 2.0 authentication with Laravel Passport
- Email verification with signed and expiring URLs
- Role-based authorization using Laravel Policies
- RESTful JSON API
- Automated API documentation with Scribe
- Automated testing with Pest

## ✉️ Email Verification & Delivery

Vinsky implements an email verification system to ensure that users
confirm their email address before accessing the application.

### Email Verification

- Password confirmation during registration.
- Automatic delivery of verification emails upon registration.
- Secure, signed verification URLs with configurable expiration.
- Restricted authentication and API access for unverified users.
- Rate-limited verification email resend with protection against
  account enumeration.
- Configurable frontend redirection after successful verification.

### Email Delivery

Emails are delivered through the Gmail REST API using OAuth 2.0,
rather than traditional SMTP.

The integration includes:

- Custom Laravel mail transport built on Symfony Mailer.
- Automatic OAuth 2.0 access token generation using a refresh token.
- HTTPS-based email delivery without relying on SMTP ports.
- Base64URL encoding of MIME email messages.
- Error handling for authentication and email delivery failures.
- Reusable Blade email templates with Vinsky branding.
- Automated tests using mocked HTTP responses.

The custom transport integrates with Laravel's native notification
system, allowing future transactional emails to reuse the same
delivery infrastructure.

## 🔌 Endpoints
### Auth

| Method | Endpoint | Description |
|---------|----------|-------------|
| POST | `/register` | Register a new user and send a verification email |
| POST | `/login` | Log in and get an access token (verified users only) |
| POST | `/logout` | Log out and revoke current access token |
| GET | `/email/verify/{id}/{hash}` | Verify an email address using a signed URL |
| POST | `/email/verification-notification` | Resend the verification email |

### Profile

| Method | Endpoint | Description |
|---------|----------|-------------|
| GET | `/me` | View profile |
| PUT | `/me` | Edit profile details |
| PUT | `/me/password` | Edit password |
| DELETE | `/me` | Delete your account |

### Orchestras

| Method | Endpoint | Description |
|---------|----------|-------------|
| POST | `/orchestras` | Create new orchestra |
| GET | `/orchestras` | List all user’s orchestras |
| GET | `/orchestras/{orchestra}` | View orchestra details |
| PUT | `/orchestras/{orchestra}` | Edit orchestra details |
| DELETE | `/orchestras/{orchestra}` | Delete orchestra |

### Memberships

| Method | Endpoint | Description |
|---------|----------|-------------|
| POST | `/orchestras/{orchestra}/memberships` | Add membership to an orchestra |
| GET | `/memberships` | List all orchestra members |
| GET | `/memberships/{membership}` | List specific member |
| PUT | `/memberships/{membership}` | Edit membership details |
| DELETE | `/memberships/{membership}` | Delete membership |

### Programs

| Method | Endpoint | Description |
|---------|----------|-------------|
| POST | `/orchestras/{orchestra}/programs` | Create new program |
| GET | `/orchestras/{orchestra}/programs` | List all programs |
| GET | `/programs/{program}` | View program details |
| PUT | `/programs/{program}` | Edit program details |
| DELETE | `/programs/{program}` | Delete program |

### Events

| Method | Endpoint | Description |
|---------|----------|-------------|
| POST | `/programs/{program}/events` | Create new event |
| GET | `/programs/{program}/events` | List all events |
| GET | `/events/{event}` | View event details |
| PUT | `/events/{event}` | Edit event details |
| DELETE | `/events/{event}` | Delete event |

### Scores

| Method | Endpoint | Description |
|---------|----------|-------------|
| POST | `/programs/{program}/scores` | Add score |
| GET | `/programs/{program}/scores` | View all scores |
| GET | `/programs/{program}/scores/{score}/download` | Download score |
| DELETE | `/programs/{program}/scores/{score}` | Delete score |

## 🚧 Setup & Installation
### Requirements
- PHP 8.5+
- Composer
- MySQL 8.4+

### Installation 
1. Clone the repository
```bash
git clone https://github.com/lbadiaestopa/vinsky-api
```
```bash
cd vinsky-api
```
2. Install dependencies
```bash
composer install
```
3. Configure environment variables
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```
Edit the `.env` file and configure your local environment, especially your database credentials (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`).

4. Email Configuration

To enable email delivery through Gmail API, configure the following
environment variables:

```dotenv
MAIL_MAILER=gmail
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Vinsky"

GMAIL_CLIENT_ID=your-client-id
GMAIL_CLIENT_SECRET=your-client-secret
GMAIL_REFRESH_TOKEN=your-refresh-token

APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
```

A Google Cloud project with the Gmail API enabled and OAuth 2.0
credentials is required. The refresh token must be authorized with
the following scope:

```
https://www.googleapis.com/auth/gmail.send
```

For local development without sending real emails, Laravel's log
mailer can be used instead:

```dotenv
MAIL_MAILER=log
```

In production, `APP_URL` and `FRONTEND_URL` must point to their
respective deployed applications.

Never commit OAuth credentials or environment secrets to the repository.

5. Run migrations and seeders
```bash
php artisan migrate:fresh --seed
```
6. Create a Passport personal access client

```bash
php artisan passport:client --personal
```

Select `users` as the user provider if prompted. This client is
required by Laravel Passport to generate personal access tokens
during login.

> **Note:** The personal access client must be recreated whenever
> the database is reset using `migrate:fresh`.

7. Start the development server
```bash
php artisan serve
```
8. Demo accounts

| Role | Email | Password |
|------|--------|----------|
| Admin | beethoven@admin.com | `password` |
| Member | mozart@member.com | `password` |
| Member | bach@member.com | `password` |

### Base URL
The API will be available at:
```
http://localhost:8000/api/v1/ 
```

## 🐳 Docker

The project is fully dockerized using Docker Compose, providing the Laravel API, MySQL database, and React frontend as separate services.

### Requirements

- Docker Desktop

### Setup

Clone both repositories into the same parent directory:

- [Vinsky API](https://github.com/lbadiaestopa/vinsky-api)
- [Vinsky Frontend](https://github.com/lbadiaestopa/vinsky-front)

```text
parent-directory/
├── vinsky-api/
└── vinsky-front/
```

Navigate to the backend directory:

```bash
cd vinsky-api
```

Build and start the containers:

```bash
docker compose up -d --build
```

This will start the following services:

- **Backend:** Laravel API at `http://localhost:8000`
- **Frontend:** React application at `http://localhost:5173`
- **Database:** MySQL running inside Docker

To stop the containers:

```bash
docker compose down
```

To start them again:

```bash
docker compose up -d
```

### Database

The MySQL data is persisted in a Docker volume, so restarting or recreating the containers does not remove the database data.

If the database needs to be initialized from scratch, the volume can be removed and recreated:

```bash
docker compose down -v
```
```bash
docker compose up -d --build
```

> **Note:** Removing the volume deletes all database data, including seeded data and Passport credentials.

After resetting the database, recreate the Passport personal
access client if it has not been created automatically:

```bash
docker compose exec backend php artisan passport:client --personal
```

## 🧪 Testing

Run the test suite using Pest:
```bash
php artisan test tests/Feature
```

## 📖 Documentation
To generate the documentation with Scribe run:
```
php artisan scribe:generate
```
```
php artisan postman:fix
```

The interactive documentation will be available locally at:
```
http://localhost:8000/docs
```

### 📮 Postman collection and environment

The collection and environment are available in the `/public/docs`
directory.

1. Import both files into Postman.
2. Log in using a verified account or register a new account and
   complete the email verification process before logging in.
3. Copy the access token returned by the login endpoint.
4. Paste the token into the `bearer_token` environment variable.

The collection is now ready to use.

### OpenAPI
An openAPI file is also available at the /public/docs directory. Follow the same steps as for the Postman collection and environment, but use the OpenAPI file instead. (You may have to change the ```bearer_token``` variable name from the environment to ```bearerToken```)

##

👨🏻‍💻 Project developed by **Lluc Badia Estopà** - [LinkedIn](https://www.linkedin.com/in/lbadiaestopa) · [Github](https://github.com/lbadiaestopa)
