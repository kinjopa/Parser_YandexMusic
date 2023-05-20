# Parser

Cоберите образы контейнеров и запустите их с помощью следующей команды:

`docker-compose up --build -d`

 Убедитесь, что контейнеры успешно запущены, выполнив команду:

`docker ps`

 Теперь вы можете зайти в контейнер app и установить зависимости Laravel и выполнить миграции с помощью команд:
 
`docker exec -it my_laravel_app bash
composer install
php artisan migrate`

Ваше Laravel приложение теперь должно быть доступно по адресу http://localhost:8081/. 
