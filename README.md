# MdlBol: Boletines de Moodle CEEH

## 1 - Requerimiento lógico necesario
- PHP 7.3+.
- MariaDB 10.4.13 ó MySQL 5.7.
- SQlite3 3.30+.
- Composer 2.0.9+. 

### 1.1 - Extensiones de PHP habilitadas
- Extensión pdo_mysql de PHP.
- Extensión pdo_sqlite de PHP.
- Extensión gd2 (GD) (para procesamiento de imágenes).

#### 1.1.1 - Requerimientos de Dompdf
- Extensión xml de PHP.
- Extensión mbstring de PHP.
 
#### 1.1.2 - Recomendaciones 
- Tener habilitado OPcache (OPcache, XCache, APC, etc.): mejora el rendimiento considerablemente.

## 2 - Acciones en la BD

### 2.1 - Crear los usuarios administradores en la BD SQlite3

#### 2.1.1 - Acceder al directorio de la aplicación ejemplo
```bash
user@box$ cd /var/www/html/mdlbol
```
#### 2.1.2 - Crear la BD SQlite3 "internal.sqlite3"
```bash
user@box$ cd /var/www/html/mdlbol

user@box$ sqlite3 database/internal.sqlite3
```
#### 2.1.3 - Crear los usuarios administradores de MdlBol
```sqlite3
sqlite> .read database/CreateAdminsUsers.sqlite3.sql
sqlite> .exit
```
### 2.2 - Crear la tabla "mdlbol_feedback_cidead" en la BD de Moodle (Actualizar el nombre de la BD, usuario de la DB con los valores reales)
```bash
user@box$ mysql -u dbuser -p moodledb < database/CreateTableMdlbolFeedbackCidead.mariadb.sql
user@box$ Enter password: 
```
### 2.3 - Crear una tarea de cron para limpiar la cache de la aplicación, puede ser semanal (Actualizar con la ruta real de la aplicación)
```bash
user@box$ sudo crontab -e

0 0 * * 0 php -f /var/www/html/mdlbol/script/clearcache
```
### 2.4 - Crear el archivo ".env" en la raíz de la aplicación
```bash
user@box$ cp .env.example .env
```
#### 2.4.1 - Editar el archivo ".env" ajustar (DB_NAME, DB_USER, DB_PASSWORD, BASE_URL) con los valores reales y respetando las demás opciones ya establecidas.

* Prestar especial atención a la variable de entrono BASE_URL, si no se establece correctamente esta variable con la URL de la aplicación se producirán errores. En el entorno de desarrollo local debe tener este valor "http://localhost:8000"


```bash
user@box$ nano .env
```
```bash
DB_ADAPTER=pdo
DB_NAME=yourdbname
DB_USER=yourdbuser
DB_PASSWORD=yourdbpasswd
DB_HOST=localhost
DB_TYPE=mysql
DB_OPTIONS="PDO::ATTR_EMULATE_PREPARES => true"
BASE_URL="http://application.url"
```

## 3 - Instalación de dependencias de composer (En la raíz de la aplicación ejecutar lo siguiente)
```bash
composer install
```
## 4 - Puesta en producción
- Finalmente crear un virtual host con el servidor web de producción, de esta forma la aplicación quedará lista para funcionar.

## 5 - Observaciones importantes
- El usuario que corre el servidor web, por ejemplo en Ubuntu es www-data, debe tener permisos de escritura sobre el directorio de la caché, se recomienda ponerlo en 640 (www-data puede leer y escribir, el grupo www-data solo leer).
```bash
user@box$ sudo chown -R www-data:www-data /var/www/html/mdlbol/cache
user@box$ sudo chmod -R 640 /var/www/html/mdlbol/cache
```