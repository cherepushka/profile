## profile.fluid-line.ru

>Если окружением проекта является <a href="https://github.com/cherepushka/profile-fluid-kit">profile-fluid-kit</a> все действия выполняются через консоль контейнера
+ + `docker compose exec apache-php bash` - переходим в консоль контейнера

### Базовая установка
- `cp .env.example .env` - Копирование .env
- Далее заполняем `.env` нужными переменными на месте пропусков 
- - Обязательные параметры: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `composer install`
- `npm install`

### DEV Установка
- `php artisan migrate:fresh --seed` - миграции БД и сидирование
- `npm run dev`

### PROD Установка
- `npm run prod`

### API
* OpenApi 3.0 документация по API методам находится в `./docs/API.yaml`.

### Обслуживание кода
Перед коммитами запускайте комманды (внутри контейнера):
* `make csFix` - код-стайл к стилю PSR
* `make phpStan` - статический анализ
