## API - DIVEN

APi-DIVEN es un proyecto escrito en [LARAVEL](https://laravel.com/docs/9.x#why-laravel) que implementa los servicios RESET para la aplicación web y móvil de DIVEN.

##Requisitos servidor
- PHP >= 8.0
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- COMPOSER >=2.1.6
- [Para mayor información, VISITE LARAVEL DESPLIEGUE ](https://laravel.com/docs/9.x/deployment)

## Requisitos base de datos
- MARIADB >= 10.6.5-MariaDB-2 o compatibles

##Requisitos para la configuración
- Contar con el ID de cliente para la aplicación web de [Google Cloud Platform]([https://console.cloud.google.com/apis/credentials).
- Crear una base de datos con el nombre api_diven.

##Instalación

###1. Instale las dependencias
    composer install

###2. Configurar las variables de entorno
2.1 Copie el archivo .env.example a la ruta raíz del proyecto con el nombre .env
2.2 Llene las siguientes variables con la información requerida.
* **APP_URL:** Url de la aplicación.
* **APP_LOCATION:** Ruta absoluta donde se encuentra el proyecto.
* **DB_USERNAME:** Usuario del servidor de base de datos.
* **DB_PASSWORD:** Contraseña del servidor de base de datos.
* **GOOGLE_APP_CLIENT_ID:** Client id proporcionado por google cloud.
* **GOOGLE_APP_CLIENT_SECRET:** Client secret proporcionado por google cloud.
* **GOOGLE_APP_REDIRECT:** *URL_APP*/auth/google/callback => Reemplacé *URL_APP* por la URL del APP
* **GOOGLE_APP_REDIRECT_WITH_AUTH:** *URL_DIVEN_FRONENT*/login/google => Reemplacé *URL_DIVEN_FRONENT* por la url de **DIVEN-FRONENT**

###3. Inserte las migraciones en la BD
    php artisan migrate

###4. Inserte la configuración básica en la BD
php artisan db:seed
El usuario administrador **admin@ueb.edu.ec** y la contraseña **12345** se encuentran insertados en el archivo DatabaseSeeder ubicado en  database/seeders, si deseá cambiarlos reemplacé por un nuevo correo y contraseña.

###5. Configure Passport
    php artisan passport:install

## License
The API-DIVEN is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

