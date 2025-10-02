# <image src="./public/img/favicon.png" style="float: left; height: 32px;"/>&nbsp;Тестовое задание для Онли

### Структура проекта
```
project/
├─ app/								- Приложение
│  ├─ auth/							- Авторизация
│  │  ├─ data/						- Хранилище данных авторизации
│  │  │  ├─ active_sessions.json
│  │  │  ├─ token_blacklist.json
│  │  │	 └─ users.json
│  │  ├─ auth.php
│  │  ├─ captcha.php
│  │  ├─ config.php
│  │  ├─ jwt.php
│  │  ├─ session_helper.php
│  │  └─ storage.php
│  ├─ components/    - компоненты
│  │  └─ header.php  - верхняя панель
│  ├─ router.php     - роутер
│  ├─ routes.php
│  └─ template.php   - шаблон
├─ pages/            - страницы
│  ├─ auth/          - страницы авторизации
│  │  ├─ login.php
│  │  └─ register.php
│  ├─ 404.php
│  ├─ index.php      - начальная страница
│  └─ profile.php
├─ public/           - статичные файлы
│  ├─ css/
│  │  └─ style.css   - стиль сайта
│  └─ img/
│     ├─ favicon.png
│     └─ landing_background.png
├─ launch.bat        - запуск веб-сервера
├─ main.php          - главный файл приложения
└─ README.md
```

### Реализованный функционал
- 🖥️ Главная страница 
- 🔐 Авторизация
- ☑️ Яндекс Капча
- 👤 Изменение данных пользователя
- 🔃 Сохранение сессии
- ⚙️ REST API
- 📂 Статичные файлы

### Точки API
- ``GET /api/routes`` - Список путей к страницам
- ``GET /api/api_routes`` - Список точек API
- ``POST /api/auth/register`` - Запрос регистрации пользователя
- ``POST /api/auth/login`` - Запрос входа
- ``GET /api/auth/logout`` - Деавторизация
- ``PUT /api/auth/change`` - Запрос изменения даных

