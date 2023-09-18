# profile.fluid-line.ru

Шаги установок, приведенные ниже, расчитаны на то, что окружением проекта будет https://github.com/cherepushka/profile-fluid-kit

## DEV Установка
- Открываем терминал и переходим в корень проекта окружения
- `docker compose exec apache-php bash` - переходим в консоль контейнера
- `cp .env.example .env` - (только если не уже создан)
- Далее заполняем `.env` нужными переменными на месте пропусков (только если не уже создан)
- `composer install`
- `php artisan migrate:fresh --seed` - миграции БД и сидирование
- `npm install`
- `npm run dev`

## PROD Установка
- Открываем терминал и переходим в корень проекта окружения
- `docker compose exec apache-php bash` - переходим в консоль контейнера
- `cp .env.example .env` - (только если не уже создан)
- Далее заполняем `.env` нужными переменными на месте пропусков (только если не уже создан)
- `composer install`
- `npm install`
- `npm run prod`

## API
* OpenApi 3.0 документация по API методам находится в `./docs/API.yaml`.
* Коллекция Postman: https://app.getpostman.com/join-team?invite_code=c43e4a0326596172e8f8ab518129f07a&target_code=8f59124a43fe3b893c1e473af807d2ee