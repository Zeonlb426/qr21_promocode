
## Общая информация

Проект основан на фреймворке Laravel, с использованием модуля для административной части Laravel-admin v1.8.9:

- Версия PHP 7.4^
- Версия Composer v2.2^ https://getcomposer.org/download/
- Версия Laravel Framework 8.83^
- Все зависимости указаны в файле composer.json, в корне проекта. 


## Установка

- Открыть консоль в папке проекта или перейти в консоле в PROJECT_DIR
```
cd /var/www/PROJECT_DIR
```

- Копировать код из репозитория в папку с проектом PROJECT_DIR
```
git clone 'Путь к Git-репозиторию'
```

- Переименовать файл конфигурации .env.example  в .env
- Создать базу данных и заполнить данные о подключении к БД в файл конфигурации .env
```  
  DB_CONNECTION=pgsql
  DB_DATABASE=Имя базы данных
  DB_FOREIGN_KEYS=null
  DB_HOST=Хост
  DB_PASSWORD=Пароль для подключения к БД
  DB_PORT=Порт БД
  DB_USERNAME=Имя пользователя
  
# Ниже указываются данные для подключения к текущей базе проекта
# Это необходимо для осуществления переноса информации из текущей БД в новую
  
  DB_DATABASE_MYSQL=regploom
  DB_HOST_MYSQL=portainer.famdev.ru
  DB_PASSWORD_MYSQL=
  DB_PORT_MYSQL=
  DB_USERNAME_MYSQL=
```
- Запуск composer(предварительно должен быть установлен). В корневой папке проекта должен быть файл composer.json. Запустить в консоле команду
```
composer install
```
- После завершения установки всех пакетов, запуск миграций
```
php artisan migrate
```

## Перенос данных из старой базы в новую 

В конфигурационном файле .env должны быть указаны все необходимые данные для подключения к обоим источникам.

Для успешного выполнения процедуры переноса необходимо установить php_memory_limit = -1 в файле настоек php.ini, либо поднять лимит до 1Gb.

Выполнить в консоли по очереди следующие команды:

```
php artisan transfer:tradeNetwork

php artisan transfer:promocodes

php artisan transfer:promocode-log

php artisan transfer:idx-log
```


## Административная панель

Путь к админ-панели проекта https://*Домен проекта*/*ПРЕФИКС*/
Префикс указывается в файле .env
```
ADMIN_ROUTE_PREFIX=adminx24
```

Для обновления пунктов меню административной панели проекта, необходимо запустить в консоли:
```
php artisan admin:import-menu-config
```

## Рассылка писем
Для работы почтовых рассылок указть в .env файле следующие данные:
```
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=""

```
Установить задания Cron каждые 5 минут на запуск:
```
php artisan schedule:run
```
## Подключение к MindBox
В конфигурационном файле .env указать следующие данные:
```
MINDBOX_KEY=
MINDBOX_ENDPOINT_ID=
MINDBOX_URL=https://api.mindbox.ru/v3/operations/
POINT_OF_CONTACT=
MINDBOX_DEBUG=false
```
