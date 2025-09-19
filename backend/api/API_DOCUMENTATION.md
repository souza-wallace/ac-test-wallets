# API Documentation - Wallet

## Base URL
```
http://127.0.0.1:8001/api
```

## Authentication
Todas as rotas protegidas requerem Bearer Token no header:
```
Authorization: Bearer {token}
```

---

## Authentication

### Login
**POST** `/login`

**Body (form-data):**
```
email: alice@example.com
password: 123456
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 1,
    "name": "Alice",
    "email": "alice@example.com"
  }
}
```