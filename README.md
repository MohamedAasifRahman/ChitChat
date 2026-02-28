# ChitChat

ChitChat is a full-stack social web application built using PHP and MySQL.

## Features
- User registration and authentication
- Secure session handling
- CSRF protection
- Post creation with image uploads
- Private messaging system
- Dynamic user search (Fetch API)
- REST-style API endpoints
- Secure file handling

## Tech Stack
- PHP (PDO)
- MySQL
- JavaScript (Fetch API)
- HTML5
- CSS3

## Security Implementation
- Password hashing
- Prepared statements
- Strict session mode
- HTTP-only cookies

## Setup Instructions

1. Clone the repository:

```bash
git clone https://github.com/MohamedAasifRahman/ChitChat.git
cd ChitChat
```

2. Create a MySQL database (e.g., `socialapp_db`)

3. Import the provided `schema.sql` file into your database

4. Update database credentials in `includes/config.php` if required

5. Start Apache and MySQL using XAMPP

6. Open in your browser:

```
http://localhost/chitchat
```
## Usage

- Register a new user account
- Log in securely
- Create posts with optional image uploads
- Search for other users dynamically
- Send and receive private messages
- Update profile details and profile picture
