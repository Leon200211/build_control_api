# Документация по api

## Авторизация

## <span style="color:#347FC4">**POST**</span> api/auth/login - Метод для авторизации

Входные данные

username - Логин

password - Пароль

fingerprint - хеш сгенерированный js'ом на базе неких уникальных параметров/компонентов браузера.
В случае если клиент не браузер, а мобильное приложение, в качестве fingerprint используем любую уникальную строку(тот же uuid) персистентно хранящуюся на устройстве.

### ***Пример***

Входные данные
```json
{
"username" : "Vasiliy",
"password" : "Ivanov",
"fingerprint" : "vasya@coder.com",
}
```

Выходные данные пример

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYW55LXNpdGUub3JnIiwiYXVkIjoiaHR0cDovL2FueS1zaXRlLm9yZyIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJleHAiOjE2OTA2NTUzODMsImRhdGEiOnsiaWQiOiIxIiwibmFtZSI6Ilx1MDQxOFx1MDQzMlx1MDQzMFx1MDQzZFx1MDQzZVx1MDQzMiBcdTA0MThcdTA0MzJcdTA0MzBcdTA0M2QgXHUwNDE4XHUwNDMyXHUwNDMwXHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3In19.j0MwqAID4haseb28lOW-kRgxTRku4or21tpD-6SW_hY",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYW55LXNpdGUub3JnIiwiYXVkIjoiaHR0cDovL2FueS1zaXRlLm9yZyIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJleHAiOjE2OTMyNDczODMsImRhdGEiOnsiaWQiOiIxIiwibmFtZSI6Ilx1MDQxOFx1MDQzMlx1MDQzMFx1MDQzZFx1MDQzZVx1MDQzMiBcdTA0MThcdTA0MzJcdTA0MzBcdTA0M2QgXHUwNDE4XHUwNDMyXHUwNDMwXHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3In19.TziIuPrXQRuH3yc-kjR0i1EaRmVgfr-e0hk7AjeS0B4",
    "jwt_expires_in": 1690655383,
    "rt_expires_in": 1693247383
}
```

## <span style="color:#347FC4">**POST**</span> /api/auth/refresh-tokens - Метод для обновления токенов

Входные данные

user_id - id пользователя

refresh_token - Токен

fingerprint - хеш сгенерированный js'ом на базе неких уникальных параметров/компонентов браузера.
В случае если клиент не браузер, а мобильное приложение, в качестве fingerprint используем любую уникальную строку(тот же uuid) персистентно хранящуюся на устройстве.

### ***Пример***

Входные данные
```json
{
"user_id" : 1,
"refresh_token" : "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYW55LXNpdGUub3JnIiwiYXVkIjoiaHR0cDovL2FueS1zaXRlLm9yZyIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJleHAiOjE2OTMyNDg0OTAsImRhdGEiOnsiaWQiOiIxIiwibmFtZSI6Ilx1MDQxOFx1MDQzMlx1MDQzMFx1MDQzZFx1MDQzZVx1MDQzMiBcdTA0MThcdTA0MzJcdTA0MzBcdTA0M2QgXHUwNDE4XHUwNDMyXHUwNDMwXHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3In19.wS6yHn_V3rkIjFoRBM2F18CJWez4NuWY3eVB1O8_Nk0",
"fingerprint" : "234"
}
```

Выходные данные пример

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYW55LXNpdGUub3JnIiwiYXVkIjoiaHR0cDovL2FueS1zaXRlLm9yZyIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJleHAiOjE2OTA2NTUzODMsImRhdGEiOnsiaWQiOiIxIiwibmFtZSI6Ilx1MDQxOFx1MDQzMlx1MDQzMFx1MDQzZFx1MDQzZVx1MDQzMiBcdTA0MThcdTA0MzJcdTA0MzBcdTA0M2QgXHUwNDE4XHUwNDMyXHUwNDMwXHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3In19.j0MwqAID4haseb28lOW-kRgxTRku4or21tpD-6SW_hY",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYW55LXNpdGUub3JnIiwiYXVkIjoiaHR0cDovL2FueS1zaXRlLm9yZyIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJleHAiOjE2OTMyNDczODMsImRhdGEiOnsiaWQiOiIxIiwibmFtZSI6Ilx1MDQxOFx1MDQzMlx1MDQzMFx1MDQzZFx1MDQzZVx1MDQzMiBcdTA0MThcdTA0MzJcdTA0MzBcdTA0M2QgXHUwNDE4XHUwNDMyXHUwNDMwXHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3In19.TziIuPrXQRuH3yc-kjR0i1EaRmVgfr-e0hk7AjeS0B4",
    "jwt_expires_in": 1690655383,
    "rt_expires_in": 1693247383
}
```

### 


## <span style="color:#347FC4">**POST**</span> /api/auth/logout - Метод для деавторизации
Входные данные

user_id - id пользователя

refresh_token - Токен

fingerprint - хеш сгенерированный js'ом на базе неких уникальных параметров/компонентов браузера.
В случае если клиент не браузер, а мобильное приложение, в качестве fingerprint используем любую уникальную строку(тот же uuid) персистентно хранящуюся на устройстве.

### ***Пример***

Входные данные
```json
{
"user_id" : 1,
"refresh_token" : "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYW55LXNpdGUub3JnIiwiYXVkIjoiaHR0cDovL2FueS1zaXRlLm9yZyIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJleHAiOjE2OTMyNDg0OTAsImRhdGEiOnsiaWQiOiIxIiwibmFtZSI6Ilx1MDQxOFx1MDQzMlx1MDQzMFx1MDQzZFx1MDQzZVx1MDQzMiBcdTA0MThcdTA0MzJcdTA0MzBcdTA0M2QgXHUwNDE4XHUwNDMyXHUwNDMwXHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3In19.wS6yHn_V3rkIjFoRBM2F18CJWez4NuWY3eVB1O8_Nk0",
"fingerprint" : "234"
}
```


***<span style="color:red">Во всех остальных запросах access_token передается как Bearer</span>***

### 
