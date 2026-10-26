# Inventario TI – Sistema web de inventario de equipos de cómputo

Aplicación en **Laravel + MariaDB** (Proyecto Integrador, UNIAJC).
Incluye: inicio de sesión con roles, CRUD de equipos, mantenimientos, ubicaciones,
tipos de equipo, responsables y usuarios, código QR por equipo, alertas de obsolescencia
y reportes.

> Esta carpeta **no es un proyecto Laravel completo**: trae solo los archivos propios
> (`app/`, `routes/`, `resources/`, `lang/`, `tests/`, `database/`). Se copian encima de un proyecto
> Laravel nuevo (paso 2).

## Requisitos
- XAMPP con **PHP 8.2 o superior** (Apache + MySQL/MariaDB + phpMyAdmin)
- Composer (https://getcomposer.org)
- Git

## Instalación paso a paso

1. **Crear el proyecto Laravel** (en una carpeta de tu elección, por ejemplo `C:\proyectos`):
   ```
   composer create-project laravel/laravel inventario-ti
   cd inventario-ti
   ```
2. **Copiar los archivos de esta carpeta** dentro de `inventario-ti`, aceptando
   reemplazar los existentes (`routes/web.php`, `app/Models/User.php`, `tests/Feature/ExampleTest.php`).
   Copia las carpetas `app`, `routes`, `resources`, `lang`, `tests` y `database`.
3. **Importar la base de datos**: en phpMyAdmin → *Importar* → archivo `database/inventario_ti.sql`.
4. **Configurar `.env`**: aplica los cambios de `LEEME-env.txt`.
5. **Limpiar la configuración y arrancar**:
   ```
   php artisan config:clear
   php artisan serve
   ```
6. Abre http://127.0.0.1:8000 e inicia sesión.

## Usuarios de prueba (contraseña de todos: `Admin123*`)
| Correo | Rol |
|---|---|
| admin@inventario.test | Administrador |
| tecnico@inventario.test | Técnico |
| consulta@inventario.test | Consulta |

## Permisos por rol
| Acción | Administrador | Técnico | Consulta |
|---|:--:|:--:|:--:|
| Ver equipos, mantenimientos, alertas, reportes | ✔ | ✔ | ✔ |
| Registrar / editar equipos y mantenimientos | ✔ | ✔ | ✖ |
| Dar de baja un equipo | ✔ | ✔ | ✖ |
| Gestionar responsables | ✔ | ✔ | ✖ |
| Eliminar equipos y mantenimientos | ✔ | ✖ | ✖ |
| Gestionar tipos, ubicaciones, usuarios y parámetros | ✔ | ✖ | ✖ |

## Regla de obsolescencia
Un equipo (no dado de baja) genera alerta si su **antigüedad ≥ vida útil** (5 años por defecto)
**o** si tiene **≥ 3 fallas en los últimos 12 meses**. Los tres valores se cambian en
*Administración → Parámetros de obsolescencia*.

## Probar el QR desde el celular
El QR guarda la dirección de la ficha del equipo. Para que el celular la abra, el servidor debe
verse desde la red:
```
php artisan serve --host=0.0.0.0 --port=8000
```
Abre el sistema en el computador con `http://IP-DE-TU-PC:8000` (por ejemplo `http://192.168.1.15:8000`;
la IP se ve con `ipconfig`), genera el QR y escanéalo con el celular conectado al mismo WiFi.
Si no abre, permite el puerto 8000 en el Firewall de Windows.

## Mapa del código (para la sustentación)
| Carpeta / archivo | Qué hace |
|---|---|
| `routes/web.php` | Todas las rutas y qué roles pueden usarlas |
| `app/Http/Middleware/RolMiddleware.php` | Bloquea el acceso según el rol |
| `app/Models/` | Un modelo por tabla (Eloquent) y sus relaciones |
| `app/Models/Equipo.php` | Incluye la regla de obsolescencia |
| `app/Http/Controllers/` | La lógica de cada módulo (validar, guardar, consultar) |
| `resources/views/` | Pantallas (Blade + Bootstrap, responsive) |
| `lang/` | Mensajes de validación y paginación en español |
| `database/inventario_ti.sql` | Script de la base de datos con datos de prueba |
| `tests/` | Pruebas automáticas (`php artisan test`) |

## Subir a Git
```
git init
git add .
git commit -m "Sistema web de inventario de equipos de cómputo"
git branch -M main
git remote add origin https://github.com/TU-USUARIO/inventario-ti.git
git push -u origin main
```
(El archivo `.env` no se sube; por eso se incluye `LEEME-env.txt`.)

## Problemas frecuentes
- **`Unknown database 'inventario_ti'`**: falta importar el `.sql` o el nombre en `.env` es distinto.
- **`could not find driver`**: activa `extension=pdo_mysql` en `php.ini` de XAMPP y reinicia Apache.
- **`Access denied for user 'root'`**: revisa `DB_USERNAME` y `DB_PASSWORD` en `.env`.
- **`419 Page Expired`**: borra las cookies del navegador y verifica `SESSION_DRIVER=file`.
- **Pantalla en blanco o error 500**: abre `storage/logs/laravel.log` y revisa la última línea.
- Después de cambiar el `.env`, ejecuta siempre `php artisan config:clear`.
