# ⚙️ Izer Portfolio - Backend API Architecture

Lightweight, secure **PHP Native REST API** designed to serve dynamic portfolio content and manage protected administrative operations.

---

## 🏗️ System & Security Architecture

```text
[ Client: izerworks.my.id ]
          │
          ▼  (OPTIONS / POST)
┌───────────────────────────────────────────┐
│ 1. cors.php (Dynamic CORS & Preflight)    │
└─────────────────────┬─────────────────────┘
                      ▼
┌───────────────────────────────────────────┐
│ 2. login.php (Rate Limiter: Max 5 req/15m)│
└─────────────────────┬─────────────────────┘
                      ▼
┌───────────────────────────────────────────┐
│ 3. setcookie (Domain: .izerworks.my.id)   │
└───────────────────────────────────────────┘

Centralized CORS Middleware (cors.php): Dynamically validates origins, sets Access-Control-Allow-Credentials, and interceptively resolves preflight OPTIONS requests before hitting business logic.

Cross-Subdomain Cookie Auth: Issues secure session tokens stored in HttpOnly, SameSite=Lax cookies scoped to .izerworks.my.id.

Database Hardening: Eliminates SQL Injection risks using MySQLi Prepared Statements ($stmt->prepare) for all read/write operations.

Brute-Force Protection: IP-based rate limiting on authentication routes returning HTTP 429 Too Many Requests upon limit violation.

🛠️ Tech Stack & Server Environment
Language: PHP 8.x Native (No framework overhead)

Database: MySQL

Web Server: Nginx + PHP-FPM
```
