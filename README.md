Inventario TI – Sistema web de inventario de equipos de cómputo
Aplicación en Laravel + MariaDB (Proyecto Integrador, UNIAJC).

Integrantes
Ingrid Vanessa Mosquera Mosquera
Andersson Sinisterra Angulo
Descripción
Sistema web para la gestión de inventarios de equipos de cómputo.

Incluye:

Inicio de sesión con roles.
CRUD de equipos.
CRUD de mantenimientos.
CRUD de ubicaciones.
CRUD de tipos de equipo.
CRUD de responsables.
CRUD de usuarios.
Generación de código QR por equipo.
Alertas de obsolescencia.
Reportes del sistema.
Estado actual del proyecto
Versión correspondiente a la entrega de implementación inicial.

Actualmente el sistema permite:

Autenticación de usuarios.
Gestión de equipos.
Gestión de mantenimientos.
Gestión de responsables.
Gestión de ubicaciones.
Gestión de tipos de equipo.
Gestión de usuarios.
Alertas de obsolescencia.
Generación de códigos QR.
Reportes básicos.
Requisitos
XAMPP con PHP 8.2 o superior
Composer
Git
MariaDB o MySQL
Instalación paso a paso
1. Clonar repositorio
git clone URL_DEL_REPOSITORIO
cd inventario-equipos-qr
2. Instalar dependencias
composer install
3. Crear archivo de entorno
copy .env.example .env
4. Generar clave de aplicación
php artisan key:generate
5. Crear la base de datos
Crear una base de datos llamada:

inventario_ti
6. Importar la base de datos
Importar el archivo:

database/inventario_ti.sql
mediante phpMyAdmin.

7. Configurar .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario_ti
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
8. Limpiar la configuración
php artisan config:clear
9. Ejecutar el proyecto
php artisan serve
Abrir:

http://127.0.0.1:8000
Usuarios de prueba
Contraseña para todos:

Admin123*
Correo	Rol
admin@inventario.test	Administrador
tecnico@inventario.test	Técnico
consulta@inventario.test	Consulta
Permisos por rol
Acción	Administrador	Técnico	Consulta
Ver equipos, mantenimientos, alertas y reportes	✔	✔	✔
Registrar y editar equipos	✔	✔	✖
Registrar mantenimientos	✔	✔	✖
Gestionar responsables	✔	✔	✖
Gestionar usuarios	✔	✖	✖
Gestionar parámetros	✔	✖	✖
Eliminar registros	✔	✖	✖
Regla de obsolescencia
Un equipo genera alerta cuando:

Tiene una antigüedad igual o superior a 5 años.
Presenta 3 o más fallas durante los últimos 12 meses.
Estos parámetros pueden configurarse desde el sistema.

Base de datos
El proyecto incluye el archivo:

database/inventario_ti.sql
El script contiene:

Creación de tablas.
Llaves primarias.
Llaves foráneas.
Restricciones.
Relaciones.
Datos de prueba.
Tablas principales
users
roles
equipos
mantenimientos
responsables
ubicaciones
tipos_equipo
parametros
Estructura del proyecto
Carpeta / archivo	Descripción
routes/web.php	Rutas del sistema
app/Models	Modelos Eloquent
app/Http/Controllers	Controladores
resources/views	Interfaces del sistema
database/inventario_ti.sql	Script de base de datos
tests	Pruebas automáticas
Probar el QR desde el celular
Ejecutar:

php artisan serve --host=0.0.0.0 --port=8000
Obtener la IP del computador con:

ipconfig
Abrir:

http://IP_DEL_PC:8000
y escanear el QR desde un celular conectado a la misma red.

Problemas frecuentes
Unknown database 'inventario_ti'
Verificar que la base de datos fue importada correctamente.

could not find driver
Activar:

extension=pdo_mysql
en el php.ini de XAMPP.

Access denied for user 'root'
Revisar:

DB_USERNAME=root
DB_PASSWORD=
Error 419
Ejecutar:

php artisan config:clear
y borrar las cookies del navegador.

Error 500
Revisar:

storage/logs/laravel.log
Repositorio
Proyecto desarrollado para el Proyecto Integrador de Tecnología en Sistemas de Información - UNIAJC.