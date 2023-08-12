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



## Профили

## <span style="color:green">**GET**</span> api/profile/my - Метод для получения данных своего профиля

### ***Пример***

Выходные данные
```json
{
  "name": "Иванов Иван Иванович",
  "position": "admin",
  "info": "Алминистратор",
  "role": "admin",
  "phone": "89990000001",
  "email": "test@test.ru",
  "profile_img": "iVBORw0KGgoAAAANSUhEUgAABJ0"
}
```



## Жилые комплексы

## <span style="color:green">**GET**</span> api/projects - Метод для получения всех доступных жилых комплексов

### ***Пример***

Выходные данные
```json
[
  {
    "id": "1",
    "project_number": "100",
    "title": "Жилой комплекс «Мытищи парк» ",
    "address": "Жилой комплекс Мытищи Парк, Мытищи, Московская область",
    "img": "mytishi.png"
  },
  {
    "id": "2",
    "project_number": "101",
    "title": "Жилой комплекс «Остафьево»",
    "address": "Жилой комплекс Остафьево, село Остафьево, поселение Рязановское, Москва",
    "img": "ostafevo.png"
  }
]
```


## <span style="color:#347FC4">**POST**</span> api/houses - Метод для получения всех домов по ЖК

### ***Пример***
Входные данные

project_id - id проекта в системе

Выходные данные
```json
{
  "project": {
    "id": "3",
    "project_number": "102",
    "title": "Рублевский Квартал",
    "address": "Рублевский Квартал",
    "img": "rublyovka.jpg"
  },
  "houses": [
    {
      "id": "6",
      "house_number": "10006",
      "title": "Жилой комплекс Рублевский Квартал к.58",
      "address": "Жилой комплекс Рублевский Квартал к.58",
      "section_img": "-"
    },
    {
      "id": "7",
      "house_number": "10007",
      "title": "Жилой комплекс Рублевский Квартал к.59",
      "address": "Жилой комплекс Рублевский Квартал к.59",
      "section_img": "-"
    }
  ]
}
```


## <span style="color:#347FC4">**POST**</span> api/house - Метод для получения информации по дому

### ***Пример***
Входные данные

house_id - id дома в системе

Выходные данные
```json
{
  "id": "1",
  "id_project": "1",
  "house_number": "10001",
  "title": "1-й Стрелковый переулок, 1, Мытищи, Московская область",
  "address": "1-й Стрелковый переулок, 1, Мытищи, Московская область",
  "section_img": "houses1-house.jpg",
  "sections": [
    {
      "id": "1",
      "section_number": "1"
    },
    {
      "id": "2",
      "section_number": "2"
    }
  ]
}
```

## <span style="color:#347FC4">**POST**</span> api/section - Метод для получения информации по секции

### ***Пример***
Входные данные

id_section - id секции в системе

Выходные данные
```json
{
  "id": "1",
  "section_number": "1",
  "floor": [
    {
      "id": "1",
      "floor_number": "2",
      "floor_plan_img": "mytishchi_house_1_section_1.png"
    },
    {
      "id": "2",
      "floor_number": "3",
      "floor_plan_img": "mytishchi_house_1_section_1.png"
    },
    {
      "id": "3",
      "floor_number": "4",
      "floor_plan_img": "mytishchi_house_1_section_1.png"
    }
  ]
}
```


## <span style="color:#347FC4">**POST**</span> api/floor - Метод для получения информации по этажу

### ***Пример***
Входные данные

id_floor - id этажа в системе

Выходные данные
```json
{
  "id": "1",
  "floor_number": "2",
  "floor_plan_img": "mytishchi_house_1_section_1.png",
  "apartments": [
    {
      "id": "1",
      "apartment_number": "1"
    },
    {
      "id": "2",
      "apartment_number": "2"
    },
    {
      "id": "3",
      "apartment_number": "3"
    }
  ]
}
```



## <span style="color:#347FC4">**POST**</span> api/apartment - Метод для получения информации по квартире

### ***Пример***
Входные данные

id_apartment - id квартиры в системе

Выходные данные
```json
{
  "id": "1",
  "apartment_number": "1",
  "sockets": "",
  "switches": "",
  "toilet": "0",
  "sink": "",
  "bath": "0",
  "floor_finishing": "",
  "draft_floor_department": "",
  "ceiling_finishing": "",
  "draft_ceiling_finish": "",
  "wall_finishing": "",
  "draft_wall_finish": "",
  "windowsill": "",
  "kitchen": "3",
  "slopes": "",
  "doors": "",
  "wall_plaster": "",
  "trash": "0",
  "radiator": "",
  "floor_plaster": "",
  "ceiling_plaster": "1",
  "windows": "10"
}
```



## <span style="color:#347FC4">**POST**</span> api/apartment/edit - Метод для изменения информации по квартире

### ***Пример***
Входные данные

```json
{
  "id_apartment": "1",
  "sockets": "",
  "switches": "",
  "toilet": "0",
  "sink": "",
  "bath": "0",
  "floor_finishing": "",
  "draft_floor_department": "",
  "ceiling_finishing": "",
  "draft_ceiling_finish": "",
  "wall_finishing": "",
  "draft_wall_finish": "",
  "windowsill": "",
  "kitchen": "3",
  "slopes": "",
  "doors": "",
  "wall_plaster": "",
  "trash": "0",
  "radiator": "",
  "floor_plaster": "",
  "ceiling_plaster": "1",
  "windows": "10"
}
```

Выходные данные
```json
{
  "status": "success"
}
```