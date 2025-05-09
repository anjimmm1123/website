# Bima Backend System

This is a comprehensive backend system for managing resources and user authentication.

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO PHP Extension
- JSON PHP Extension

## Installation

1. Clone the repository to your web server directory
2. Create a MySQL database named `bima_db`
3. Import the database schema from `database/schema.sql`
4. Configure your web server to point to the `backend` directory
5. Ensure the `uploads` directory is writable by the web server

## API Endpoints

### Authentication

#### Login
- **URL**: `/api/login`
- **Method**: `POST`
- **Body**:
  ```json
  {
    "username": "string",
    "password": "string"
  }
  ```

#### Register
- **URL**: `/api/register`
- **Method**: `POST`
- **Body**:
  ```json
  {
    "username": "string",
    "email": "string",
    "password": "string",
    "full_name": "string"
  }
  ```

### Resources

#### Get All Resources
- **URL**: `/api/resources`
- **Method**: `GET`
- **Query Parameters**:
  - `page`: Page number (default: 1)
  - `limit`: Items per page (default: 10)

#### Get Resource by ID
- **URL**: `/api/resources/{id}`
- **Method**: `GET`

#### Create Resource
- **URL**: `/api/resources`
- **Method**: `POST`
- **Body**:
  ```json
  {
    "title": "string",
    "description": "string",
    "type": "string",
    "file_path": "string",
    "created_by": "integer",
    "categories": ["integer"]
  }
  ```

#### Update Resource
- **URL**: `/api/resources/{id}`
- **Method**: `PUT`
- **Body**:
  ```json
  {
    "title": "string",
    "description": "string",
    "type": "string",
    "categories": ["integer"]
  }
  ```

#### Delete Resource
- **URL**: `/api/resources/{id}`
- **Method**: `DELETE`

## Security

- All endpoints except login and register require authentication
- Passwords are hashed using PHP's password_hash function
- Input validation and sanitization is implemented
- SQL injection prevention using prepared statements
- CORS headers are properly configured

## Error Handling

The API returns appropriate HTTP status codes:
- 200: Success
- 400: Bad Request
- 401: Unauthorized
- 404: Not Found
- 500: Internal Server Error

All responses are in JSON format:
```json
{
  "success": boolean,
  "message": "string",
  "data": object|array|null
}
```

## File Structure

```
backend/
├── api/
│   └── index.php
├── auth/
│   └── Auth.php
├── config/
│   └── database.php
├── database/
│   └── schema.sql
├── models/
│   └── Resource.php
└── uploads/
```

## Default Admin Account

After importing the database schema, a default admin account is created:
- Username: admin
- Password: password
- Email: admin@bima.com

Please change these credentials after first login. 