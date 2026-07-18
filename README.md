# 🎻 Vinsky API
<img width="2488" height="622" alt="image" src="https://github.com/user-attachments/assets/fce67391-37a1-4d8d-b28d-94b91d2acea1" />

A RESTful API for managing orchestras, their members, programs, events, and musical scores. It provides structured access control, allowing users to collaborate within orchestras, organize rehearsals and concerts, and manage shared sheet music through secure file uploads.

## 🛠️ Tech Stack
- **Backend:** PHP 8.5
- **Framework:** Laravel 13
- **Authentication:** Laravel Passport
- **Database:** MySQL
- **Testing:** Pest
- **Documentation:** Scribe

## 🪄 Features
- Orchestra and membership management
- Project organization
- Rehearsal and concert scheduling
- Secure PDF score storage and access control
- OAuth 2.0 authentication with Laravel Passport
- Role-based authorization using Laravel Policies
- RESTful JSON API
- Automated API documentation with Scribe
- Automated testing with Pest

## 🔌 Endpoints
### Auth

| Method | Endpoint | Description |
|---------|----------|-------------|
| POST | `/register` | Register a new user |
| POST | `/login` | Log in and get access token |
| POST | `/logout` | Log out and revoke current access token |

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
### Prerequisites
- PHP 8.5+
- Composer
- MySQL 9.6+

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

4. Run migrations and seeders
```bash
php artisan migrate:fresh --seed
```

5. Start the development server
```bash
php artisan serve
```
7. Demo accounts

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
The collection and environment are available at the /public/docs directory.

1. Import both files into Postman
2. Use ```login``` (with the demo accounts credentials) or ```register```to obtain a token
3. Paste the token on the bearer_token varaible in the value field
Now it's ready to use

### OpenAPI
An openAPI file is also available at the /public/docs directory. Follow the same steps as for the Postman collection and environment, but use the OpenAPI file instead. (You may have to change the ```bearer_token``` variable name from the environment to ```bearerToken```)

##

👨🏻‍💻 Project developed by **Lluc Badia Estopà** - [LinkedIn](https://www.linkedin.com/in/lbadiaestopa) · [Github](https://github.com/lbadiaestopa)
