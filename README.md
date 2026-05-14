# 🎮 GameSocial

> Red social y catálogo de gestión de videojuegos — Proyecto de Fin de Ciclo DAW

**GameSocial** es una aplicación web dinámica que combina las funcionalidades de una red social con un catálogo completo de videojuegos. Permite a los usuarios registrar su historial de juegos, publicar en un muro social compartido, seguir a otros jugadores, interactuar mediante respuestas anidadas y likes, y organizar su colección en listas temáticas.

Desarrollado por **Alexis Sevilla Serrano** — 2º DAW · IES Francisco Ayala

---

## 📋 Índice

- [Características](#-características)
- [Tecnologías](#-tecnologías)
- [Arquitectura](#-arquitectura)
- [Base de datos](#-base-de-datos)
- [Requisitos previos](#-requisitos-previos)
- [Instalación local](#-instalación-local)
- [Despliegue en producción](#-despliegue-en-producción)
- [Estructura de directorios](#-estructura-de-directorios)
- [Seguridad](#-seguridad)
- [Capturas de pantalla](#-capturas-de-pantalla)
- [Mejoras futuras](#-mejoras-futuras)
- [Autor](#-autor)

---

## ✨ Características

### Para usuarios
- **Registro e inicio de sesión** con opción "Recordarme" (cookie persistente de 30 días)
- **Catálogo de videojuegos** navegable con filtros simultáneos por título, género, plataforma, desarrolladora y tipo
- **Biblioteca personal** con cinco estados por juego: *Sin iniciar, En curso, Terminado, Completado (100%) y Abandonado*
- **Valoraciones** del 1 al 5 por juego, con media de la comunidad en tiempo real
- **Feed social** con publicaciones de texto, respuestas anidadas multinivel y likes dinámicos sin recarga (AJAX)
- **Seguimiento de usuarios** con búsqueda por nombre y contadores actualizados
- **Listas temáticas** públicas o privadas con portada automática
- **Notificaciones** automáticas de actividad
- **Perfil personalizable** con avatar y biografía
- **URLs amigables** del tipo `/juego/elden-ring`, `/usuario/alexis`, `/lista/3-mis-favoritos`

### Para administradores
- **Panel de administración** con acceso restringido por rol
- **CRUD completo** del catálogo de videojuegos con subida de imagen de portada
- **Gestión de usuarios**: cambio de rol y eliminación de cuentas
- **Moderación** de publicaciones del feed

---

## 🛠 Tecnologías

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.1 (MVC nativo, sin frameworks) |
| Base de datos | MySQL 8 con PDO y prepared statements |
| Frontend | Bootstrap 5 + JavaScript ES6 (Fetch API) |
| Tipografía | Google Fonts — Orbitron + Roboto |
| Iconos | Font Awesome 6.5.1 |
| Servidor local | XAMPP (Apache + MySQL + PHP) |
| Servidor producción | AWS EC2 — Ubuntu Server 22.04 LTS |
| Dominio | IONOS |
| DNS | Cloudflare |
| HTTPS | Let's Encrypt + Certbot |
| Control de versiones | Git + GitHub |
| Wireframes | Moqups |
| Diagramas | Draw.io |

---

## 🏗 Arquitectura

El proyecto implementa el patrón **MVC nativo en PHP** sin frameworks externos.

```
Petición HTTP
     │
     ▼
 .htaccess  ──────────────►  index.php (Router central)
                                    │
                    ┌───────────────┼───────────────┐
                    ▼               ▼               ▼
              Controlador      Controlador      Controlador
              (feed.php)     (catalogo.php)   (perfil.php)
                    │               │               │
                    ▼               ▼               ▼
                 Modelo          Modelo           Modelo
               (Post.php)  (Videojuego.php)  (Usuario.php)
                    │               │               │
                    └───────────────┼───────────────┘
                                    ▼
                                 MySQL
                                    │
                    ┌───────────────┼───────────────┐
                    ▼               ▼               ▼
                  Vista           Vista           Vista
              (feed.php)      (catalogo.php)   (perfil.php)
```

- **Modelos** (`backend/modelos/`): acceso a la base de datos con PDO. Sin lógica HTTP ni HTML.
- **Vistas** (`frontend/vistas/`): generan el HTML con `htmlspecialchars()` en cada salida dinámica. Sin lógica de negocio.
- **Controladores** (`backend/controladores/`): verifican sesión → procesan POST → redirigen → cargan datos → incluyen vista.
- **Helpers** (`backend/helpers/`): `auth.php` (sesión/autorización), `imagen.php` (rutas), `slug.php` (URLs amigables), `videojuego_admin.php` (validación de formularios).

---

## 🗄 Base de datos

13 tablas con relaciones N:M, claves foráneas, restricciones de unicidad y operaciones UPSERT / INSERT IGNORE.

| Tabla | Descripción |
|---|---|
| `usuarios` | Cuentas de usuario con bcrypt, rol y token de sesión persistente |
| `videojuegos` | Catálogo completo con portada, género, plataforma y desarrolladora |
| `usuario_videojuego` | Biblioteca personal: estado (ENUM 5 valores) y valoración — UPSERT |
| `posts` | Publicaciones del feed social |
| `post_respuestas` | Respuestas anidadas multinivel (`id_respuesta_padre` nullable) |
| `likes` | Tabla polimórfica para likes de posts y respuestas |
| `seguidores` | Relación N:M reflexiva entre usuarios — INSERT IGNORE |
| `notificaciones` | Alertas de actividad por usuario |
| `listas` | Listas temáticas públicas o privadas |
| `lista_videojuego` | Videojuegos dentro de cada lista — INSERT IGNORE |
| `logros` | Definición de logros disponibles |
| `usuario_logro` | Logros desbloqueados por cada usuario |
| `comentarios` | Comentarios en fichas de videojuegos con anidamiento |

---

## ⚙️ Requisitos previos

- **XAMPP** (Apache 2.4 + MySQL 8 + PHP 8.1) o entorno equivalente
- **PHP 8.1** con extensiones: `pdo_mysql`, `mbstring`, `fileinfo`
- **Git**
- Navegador moderno (Chrome 90+, Firefox 88+, Edge 90+, Safari 14+)

---

## 🚀 Instalación local

### 1. Clonar el repositorio

```bash
git clone https://github.com/Alexis15014/gamesocial.git
```

Coloca la carpeta clonada en `htdocs/` de tu instalación de XAMPP:

```
C:\xampp\htdocs\gamesocial\
```

### 2. Crear la base de datos

1. Inicia XAMPP y arranca los servicios **Apache** y **MySQL**.
2. Abre **phpMyAdmin** en `http://localhost/phpmyadmin`.
3. Crea una base de datos llamada `gamesocial`.
4. Importa el archivo `gamesocial.sql` incluido en la raíz del proyecto.

### 3. Configurar la conexión

Edita `backend/config/conexion.php` con tus credenciales locales:

```php
<?php
$host     = 'localhost';
$dbname   = 'gamesocial';
$usuario  = 'root';      // usuario de MySQL en local
$password = '';           // contraseña vacía por defecto en XAMPP
```

### 4. Acceder a la aplicación

Abre el navegador y ve a:

```
http://localhost/gamesocial/
```

> **Nota:** El router central (`index.php`) y el `.htaccess` gestionan todas las rutas automáticamente. Asegúrate de que `mod_rewrite` está activado en Apache.

### Credenciales de prueba

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | `admin@gamesocial.com` | `admin123` |
| Usuario | `zelda@gamesocial.com` | `usuario123` |

*(Ajusta según los datos del `.sql` incluido en el repositorio.)*

---

## ☁️ Despliegue en producción

### Infraestructura utilizada

- **AWS EC2** — Ubuntu Server 22.04 LTS (t2.micro, 1 vCPU, 1 GB RAM)
- **IONOS** — Registro del dominio
- **Cloudflare** — Gestión de DNS y proxy HTTPS
- **Let's Encrypt + Certbot** — Certificado SSL gratuito con renovación automática

### Proceso paso a paso

```bash
# 1. Conectarse al servidor por SSH (usando PuTTY en Windows o ssh en Linux/macOS)
ssh -i clave.pem ubuntu@<IP_PUBLICA_EC2>

# 2. Actualizar el sistema e instalar dependencias
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 php libapache2-mod-php php-mysql php-mbstring php-xml mysql-server git -y

# 3. Activar mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# 4. Clonar el repositorio en el servidor
sudo git clone https://github.com/Alexis15014/gamesocial.git /var/www/html/gamesocial/

# 5. Importar la base de datos
mysql -u root -p -e "CREATE DATABASE gamesocial;"
mysql -u root -p gamesocial < /var/www/html/gamesocial/gamesocial.sql

# 6. Ajustar permisos de la carpeta de avatares
sudo chown -R www-data:www-data /var/www/html/gamesocial/frontend/assets/avatars/
sudo chmod -R 755 /var/www/html/gamesocial/frontend/assets/avatars/

# 7. Configurar el VirtualHost de Apache
sudo nano /etc/apache2/sites-available/gamesocial.conf
```

Contenido del VirtualHost:

```apacheconf
<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias www.tudominio.com
    DocumentRoot /var/www/html/gamesocial

    <Directory /var/www/html/gamesocial>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/gamesocial_error.log
    CustomLog ${APACHE_LOG_DIR}/gamesocial_access.log combined
</VirtualHost>
```

```bash
# 8. Activar el sitio y reiniciar Apache
sudo a2ensite gamesocial.conf
sudo systemctl reload apache2

# 9. Instalar Certbot y obtener certificado HTTPS
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d tudominio.com -d www.tudominio.com

# 10. Actualizar credenciales en producción
sudo nano /var/www/html/gamesocial/backend/config/conexion.php
# → Cambiar usuario, contraseña y nombre de BD de producción
```

### DNS en Cloudflare

Añade dos registros A en el panel de Cloudflare:

| Tipo | Nombre | Valor |
|---|---|---|
| A | `@` | IP pública de EC2 |
| A | `www` | IP pública de EC2 |

### Mantenimiento

Para actualizar la aplicación en producción:

```bash
cd /var/www/html/gamesocial
sudo git pull origin main
```

---

## 📁 Estructura de directorios

```
gamesocial/
├── backend/
│   ├── config/
│   │   └── conexion.php          # Credenciales de la base de datos
│   ├── controladores/            # Controladores MVC (un archivo por módulo)
│   │   ├── login.php
│   │   ├── registro.php
│   │   ├── feed.php
│   │   ├── catalogo.php
│   │   ├── detalle.php
│   │   ├── perfil.php
│   │   ├── biblioteca.php
│   │   ├── listas.php
│   │   ├── like.php
│   │   ├── seguir.php
│   │   ├── notificaciones.php
│   │   └── admin/
│   │       ├── panel.php
│   │       ├── videojuegos.php
│   │       └── usuarios.php
│   ├── helpers/
│   │   ├── auth.php              # Verificación de sesión y autorización
│   │   ├── imagen.php            # Resolución de rutas de imágenes
│   │   ├── slug.php              # Generación de URLs amigables
│   │   └── videojuego_admin.php  # Validación de formularios admin
│   └── modelos/                  # Clases de acceso a datos con PDO
│       ├── Usuario.php
│       ├── Videojuego.php
│       ├── Post.php
│       ├── PostRespuesta.php
│       ├── Like.php
│       ├── Follow.php
│       ├── EstadoJuego.php
│       ├── Lista.php
│       ├── Notificacion.php
│       ├── Logro.php
│       └── Comentario.php
├── frontend/
│   ├── assets/
│   │   ├── avatars/              # Avatares de usuario (escritura por www-data)
│   │   ├── css/
│   │   │   └── custom.css        # Estilos personalizados sobre Bootstrap 5
│   │   ├── img/                  # Imágenes de portada de videojuegos
│   │   └── js/
│   │       └── feed.js           # Lógica AJAX para likes dinámicos
│   ├── partials/                 # Fragmentos reutilizables (header, footer)
│   └── vistas/                  # Plantillas HTML generadas por los controladores
├── .htaccess                     # Rewrite rules → redirige todo a index.php
├── index.php                     # Router central
├── gamesocial.sql                # Volcado completo de la base de datos
└── README.md
```

---

## 🔒 Seguridad

| Vector | Solución implementada |
|---|---|
| SQL Injection | PDO con prepared statements en todas las consultas, sin excepción |
| XSS | `htmlspecialchars()` en todas las salidas dinámicas de las vistas |
| Contraseñas | `password_hash()` con bcrypt y salting automático |
| Cookies | Flags `HttpOnly` + `Secure` + token de 256 bits (`random_bytes(32)`) |
| Comparación de tokens | `hash_equals()` para evitar ataques de temporización |
| Control de acceso | `requiereLogin()` en controladores privados, `requiereAdmin()` en el panel |
| Subida de archivos | Validación de extensión contra lista blanca (jpg, png, webp) |
| Duplicados | `INSERT IGNORE` en follows y lista_videojuego; captura de `PDOException 23000` en likes concurrentes |

---

## 📸 Capturas de pantalla

| Vista | Descripción |
|---|---|
| Login | Formulario centrado con "Recordarme", tema oscuro gamer |
| Catálogo | Grid responsive con filtros dinámicos por género, plataforma y desarrolladora |
| Mi Colección | Tarjetas con estado en badge y valoración en estrellas |
| Feed | Muro de publicaciones con likes AJAX y respuestas anidadas |
| Perfil | Avatar, estadísticas de colección, logros y listas públicas |
| Panel Admin | CRUD de videojuegos, gestión de roles y moderación de posts |

*(Las capturas completas están disponibles en la documentación del proyecto.)*

---

## 🔮 Mejoras futuras

**Corto plazo**
- [ ] Mensajería privada entre usuarios
- [ ] Paginación del feed y el catálogo (LIMIT + OFFSET)
- [ ] Sistema de logros completo con asignación automática por actividad

**Medio plazo**
- [ ] API REST — exposición de datos en JSON para integraciones externas
- [ ] Integración con IGDB o RAWG para importar juegos automáticamente
- [ ] Modo claro/oscuro con persistencia en cookie

**Largo plazo**
- [ ] Aplicación móvil nativa (Android / iOS) consumiendo la API REST

---

## 📚 Documentación

La documentación técnica completa del proyecto (análisis, diseño, diagramas, casos de prueba y proceso de despliegue) está disponible en el archivo `GameSocial-Documentacion-Alexis-Sevilla.pdf` incluido en este repositorio.

---

## 👤 Autor

**Alexis Sevilla Serrano**  
2º DAW — Desarrollo de Aplicaciones Web  
IES Francisco Ayala

- GitHub: [@Alexis15014](https://github.com/Alexis15014)

---

## 📄 Licencia

Este proyecto es de código abierto con fines educativos. Puedes revisarlo, estudiar su código y reutilizarlo libremente con atribución al autor.
