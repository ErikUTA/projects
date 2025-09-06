Pasos para correr el proyecto:

Clonar el proyecto: git clone https://github.com/ErikUTA/projects.git

1-. Instalar dependencias con el comando: composer install
2-. Generar clave de aplicación con comando: php artisan key:generate
3-. Ejecutar migraciones con comando: php artisan migrate
4-. Ejecutar seeders con comando: php artisan db:seed
5-. Correr proyecto con comando: php artisan serve
6-. Navegar a la interfaz de swagger para probar los endpoints: http://localhost:8000/api/documentation