# <image src="./public/img/favicon.png" style="float: left; height: 32px;"/>&nbsp;Тестовое задание для Онли

### Структура проекта
```
project/ 
├── app/								- Директория приложения
│   ├── auth/							- Авторизация
│   │   ├── data/ 						- Хранилище
│   │   │   ├── active_sessions.json	- Активные сессии
│   │   │   ├── token_blacklist.json	- Чёрный список токенов
│   │   │   └── users.json 				- Пользователи
│   │   ├── auth.php 					- Авторизация
│   │   ├── captcha.php 				- Проверка Яндекс Капчи
│   │   ├── config.php 					- Конфигурация авторизации
│   │   ├── jwt.php 					- Работа с токенами
│   │   ├── session_helper.php 			- Управление сессией
│   │   └── storage.php 				- Работа с хранилищем
│   ├── components/ 					- Компоненты страниц
│   │   └── header.php					- Верхняя панель сайта
│   ├── router.php 						- Роутер
│   ├── routes.php 						- Определение путей страниц и API
│   └── template.php 					- Шаблон для отрисовки страницы
├── pages/ 								- Страницы 
│   ├── auth/ 							- Страницы авторизации
│   │   ├── login.php 					- Страница входа
│   │   └── register.php 				- Страница регистрации
│   ├── 404.php 						- Страница ошибки 404
│   ├── index.php 						- Главная страница
│   └── profile.php 					- Страница пользователя
├── public/ 							- Статичные файлы
│   ├── css/ 							- Стили
│   │   └── style.css 					- Главный файл стиля
│   └── img/ 							- Изображения
│       ├── favicon.png 				- Иконка
│       └── landing_background.png		- Фон главной страницы
├── launch.bat 							- Запустить веб сервер
├── main.php 							- Главный файл
└── README.md 							- README
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
- ``GET /api/user`` - Данные о текущем пользователе
- ``POST /api/auth/register`` - Запрос регистрации пользователя
- ``POST /api/auth/login`` - Запрос авторизации
- ``POST /api/auth/logout`` - Запрос деавторизации
- ``POST /api/auth/change`` - Запрос изменения даных

### Скриншоты
| ![Главная страница](/public/img/screenshots/main_page.jpeg) | ![Страница регистрации](/public/img/screenshots/register_page.jpeg) |
| --- | --- |
| ![Страница входа](/public/img/screenshots/login_page.jpeg) | ![Страница пользователя](/public/img/screenshots/profile_page.jpeg)|