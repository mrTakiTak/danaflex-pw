# Template app

## Настройка

1. Необходимо переименовать файл ```.env.example``` в ```.env``` и вставить значения переменных

```dotenv
# подключение к базе приложения 
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# подключение к общей базе портала
PORTAL_DB_DATABASE=danaflexportal
PORTAL_DB_USERNAME=
PORTAL_DB_PASSWORD=

# ключи для подключения аутентификации 
KEYCLOAK_REALM_PUBLIC_KEY=
KEYCLOAK_CLIENT_SECRET=
```

2. Установка пакетов

Необходимо переименовать файлы ```.npmrc.example``` в ```.npmrc``` и ```auth.json.example``` в ```auth.json```, затем заменить значение ```TOKEN``` в этих файлах на персональный ключ доступа 

Ключь доступа можно выписать по ссылке https://git.danaflex-nano.ru/user/settings/applications

Далее необходимо выполнить команды
```shell
npm install
composer install
```

3. Генерация ключа приложения
```shell
php artisan key:generate
```

4. Разработка
```shell
php artisan serve
npm run dev
```
