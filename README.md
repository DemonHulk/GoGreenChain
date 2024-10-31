GoGreenChain
Este proyecto es una plataforma de gestión de tareas enfocada en acciones medioambientales y comunitarias. Los usuarios pueden registrarse, realizar tareas como reciclaje o limpieza, y recibir recompensas en tokens. Está diseñado para empresas que desean asignar tareas a usuarios y para usuarios que buscan obtener recompensas por su contribución.

Características
Autenticación de Usuarios: Los usuarios pueden registrarse e iniciar sesión en la plataforma.

Roles de Usuarios: El sistema tiene diferentes roles:

Administrador: Gestiona las tareas y los usuarios.
Empresa: Crea tareas y asigna recompensas.
Usuario: Completa tareas y gana recompensas.
Usuarios sin rol: Pueden registrarse y ver algunas opciones antes de recibir su rol.
Gestión de Tareas:

Crear, editar y eliminar tareas.
Asignar recompensas (en tokens) por completar las tareas.
Filtrar y visualizar tareas por estado (pendiente o completada).
Ver tareas activas y completadas con detalles de fechas.
Tareas Geolocalizadas:

Los usuarios pueden ver la ubicación de la tarea y recibir instrucciones sobre dónde realizarla.
Panel de Control:

Información resumida sobre el número de tareas activas, completadas y totales.
Lista interactiva de tareas con opción de marcar como completadas.
Tecnologías Usadas
Backend: Laravel (PHP)
Frontend: Blade Templates, AdminLTE
Base de Datos: MySQL
Autenticación y Seguridad: Laravel Auth, Gates y Policies para control de acceso.
Tokens: Implementación de sistema de recompensas con tokens a través de integración con la API de Near Wallet.
Requisitos
PHP >= 8.2.4
Composer
MySQL
Node.js y npm
Near Wallet (Para manejar la API de recompensas)
Instalación
Clona este repositorio:

bash
git clone https://github.com/DemonHulk/GoGreenChain
Accede al directorio del proyecto:

bash
cd proyecto-tareas
Instala las dependencias de PHP:

bash
composer install
Instala las dependencias de Node.js:

bash
npm install
Crea un archivo .env y configura las variables de entorno (copia el .env.example):

bash
cp .env.example .env
Asegúrate de configurar tu base de datos y los detalles de Near Wallet en el archivo .env.

Genera la clave de la aplicación:

bash
php artisan key:generate

--------------------CONFIGURACIONES IMPORTANTES--------------------

Para poder inicializar el proyecto debemos generar unos permisos en postgres
-- Crear el usuario con su contraseña
CREATE USER gogreenchain_user WITH PASSWORD 'jkjrytuf*53sd';

-- Conceder permisos de conexión a la base de datos
GRANT CONNECT ON DATABASE gogreenchain TO gogreenchain_user;

-- Conceder todos los privilegios en la base de datos
GRANT ALL PRIVILEGES ON DATABASE gogreenchain TO gogreenchain_user;

-- Conceder permisos para usar el esquema public
GRANT USAGE ON SCHEMA public TO gogreenchain_user;

-- Conceder permisos de SELECT, INSERT, UPDATE, DELETE en todas las tablas del esquema public
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO gogreenchain_user;

-- Conceder permisos de USAGE y UPDATE en todas las secuencias del esquema public
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO gogreenchain_user;
GRANT UPDATE ON ALL SEQUENCES IN SCHEMA public TO gogreenchain_user;

-- Conceder permisos para crear tablas en el esquema public
GRANT CREATE ON SCHEMA public TO gogreenchain_user;

-- También puedes conceder todos los privilegios en el esquema public
GRANT ALL PRIVILEGES ON SCHEMA public TO gogreenchain_user;

Dentro de nuestro .env debemos ingresar las siguientes credenciales:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gogreenchain
DB_USERNAME=gogreenchain_user
DB_PASSWORD=jkjrytuf*53sd

bash
Copiar código
php artisan serve
(Opcional) Ejecuta los assets del frontend:

bash
Copiar código
npm run dev
Uso
Registro e Inicio de Sesión
Los usuarios pueden registrarse en la plataforma y, una vez autenticados, se les asignará un rol.
Los administradores pueden gestionar usuarios y tareas desde el panel de control.
Gestión de Tareas
Las empresas pueden crear nuevas tareas desde su perfil, asignar descripciones, fechas de inicio y fin, ubicación y recompensas en tokens.
Los usuarios pueden completar tareas, recibir recompensas, y ver el estado de sus actividades en el panel.
Near Wallet
Este proyecto utiliza la integración de Near Wallet para manejar los tokens de recompensa. Asegúrate de tener configurado el NEAR_ACCOUNT_ID y NEAR_NETWORK en el archivo .env.

Contribuciones
Las contribuciones son bienvenidas. Por favor, sigue los pasos de pull request y abre un issue si encuentras algún error.

Licencia
Este proyecto está bajo la Licencia MIT. Consulta el archivo LICENSE para más detalles.
