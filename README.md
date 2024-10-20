# Sobrus-symfony-api-test


## Installation

Install with composer

```bash
  composer install
  php bin/console make:migration
  php bin/console doctrine:migrations:migrate
  php bin/console lexik:jwt:generate-keypai
  php bin/console security:create-user
```
## Infos
the response can have:
- "data": as an object
- "errors": as an array of objects
- "message": as a string
