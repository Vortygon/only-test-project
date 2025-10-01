# Тестовое задание для Онли

### Структура проекта
```
project/
├─ app/              - приложение
│  ├─ components/    - компоненты
│  │  └─ header.php  - верхняя панель
│  ├─ router.php     - роутер
│  └─ template.php   - шаблон
├─ pages/            - страницы
│  ├─ auth/          - страницы авторизации
│  │  ├─ login.php
│  │  └─ register.php
│  ├─ 404.php
│  └─ index.php      - начальная страница
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

### Реализованный функционал:
- 🖥️ Главная страница 
- 🔐 Авторизация
- ⚙️ REST API
- 📂 Статичные файлы



### Точки API
- ``GET /api/routes`` - Список путей к страницам
- ``GET /api/api_routes`` - Список точек API
- ``POST /api/register`` - Запрос регистрации пользователя
- ``POST /api/login`` - Запрос входа
- ``PATCH /api/change`` - Запрос изменения даных

