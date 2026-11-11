# Sistema de Calificaciones — XXXIII Festival Nacional de la Canción Boliviana “Aquí… Canta Bolivia”

Sistema web en tiempo real desarrollado en **Laravel 12** para la gestión integral de calificaciones, control de escenario, jurado secreto y ranking público de gala del **XXXIII Festival Nacional de la Canción Boliviana “Aquí… Canta Bolivia”** (Oruro, Teatro Internacional Oruro — 2026).

---

## Tabla de Contenidos

1. [Descripción del Proyecto](#1-descripción-del-proyecto)
2. [Funcionalidades Principales](#2-funcionalidades-principales)
3. [Reglas Oficiales de Evaluación y Categorías](#3-reglas-oficiales-de-evaluación-y-categorías)
4. [Especificaciones Tecnológicas y Arquitectura](#4-especificaciones-tecnológicas-y-arquitectura)
5. [Estrategia de Despliegue 1: Local con XAMPP](#5-estrategia-de-despliegue-1-local-con-xampp)
6. [Estrategia de Despliegue 2: Despliegue Completo con Docker Compose en Servidor Debian](#6-estrategia-de-despliegue-2-despliegue-completo-con-docker-compose-en-servidor-debian)
7. [Ejecución en Red Local (LAN del Evento / Escenario)](#7-ejecución-en-red-local-lan-del-evento--escenario)
8. [Credenciales Iniciales y Comandos Útiles](#8-credenciales-iniciales-y-comandos-útiles)
9. [Pruebas Automatizadas](#9-pruebas-automatizadas)

---

## 1. Descripción del Proyecto

El sistema automatiza el proceso de evaluación artística del festival conforme a la **Convocatoria Oficial 2026**, garantizando transparencia, trazabilidad notarial y actualización instantánea en pantallas gigantes de escenario y dispositivos de jurados mediante **WebSockets (Laravel Reverb)**.

El alcance funcional está enfocado exclusivamente en la operación oficial del festival:

- Configuración oficial de las **13 categorías** y sus **parámetros de evaluación**.
- Gestión de **participantes, inscripciones y presentaciones** (individuales o conjuntos con nombre artístico/pseudónimo y fotografías).
- Carga masiva de datos operativos desde archivos **XLSX, CSV y JSON**, además de CRUD manual administrativo.
- Asignación de **3 jurados** en etapa de **Preselección** (departamental) y **5 jurados** en la **Etapa Final** (nacional).
- Control de estados de presentación (`POR_PARTICIPAR` → `EN_ESCENARIO` → `PARTICIPANDO` → `FINALIZADO`) y **cronómetro oficial sincronizado** desde el servidor.
- **Jurado Secreto** en la Etapa Final: su calificación forma parte del promedio interno desde el momento de emisión, pero permanece oculta al público hasta la revelación oficial de la categoría.
- **Ranking público de gala en tiempo real** con animaciones fluidas, desglose por mesa evaluadora y **Podio de Ganadores con revelación progresiva** (3° lugar, 2° lugar, 1° lugar, o Ganador Único para *Ñusta* y *Especial*).
- **Reportes oficiales y auditoría forense**: Acta Notarial lista para impresión (*print-ready*), sábana detallada por parámetro, exportación a CSV e historial inmutable de correcciones con motivo obligatorio.

---

## 2. Funcionalidades Principales

### Panel de Administración (`/admin/*`)
- **Gestión de Usuarios**: Alta y administración de usuarios con roles `ADMIN` y `JURADO`, y habilitación de permiso para control de cronómetro (`puede_controlar_tiempo`).
- **Configuración Oficial**: Consulta de las 13 categorías, tiempos máximos de presentación y parámetros con sus puntajes máximos.
- **Participantes e Inscripciones**:
  - Registro manual con validación de duplicados (`nombre + paterno + materno`), límite de integrantes por categoría y carga de fotografía oficial (`foto`) e imagen recortada sin fondo en PNG para jurados (`foto_sin_fondo`).
  - Importación masiva desde **XLSX, CSV y JSON** con descarga de plantilla oficial y reporte detallado de errores por fila.
- **Control de Presentaciones y Escenario**: Transición controlada de estados, monitoreo del cronómetro en vivo y seguimiento de votos emitidos por presentación.
- **Asignación de Jurados y Jurado Secreto**:
  - Asignación manual o automática de jurados por presentación/categoría.
  - Designación del **Jurado Secreto** en la Etapa Final, preparación de podio en modo misterio, revelación escalonada de ganadores y cierre definitivo de categoría.
- **Centro de Reportes y Auditoría Forense**:
  - **Acta Notarial Oficial** formateada para impresión y firma de notario de fe pública.
  - **Sábana de Calificaciones** desglosada parámetro por parámetro.
  - **Exportación CSV** de resultados oficiales y sábana completa.
  - **Auditoría Forense (`CalificacionHistorial`)**: Registro inmutable de cualquier corrección de nota con valores anteriores, valores nuevos, usuario, marca de tiempo y motivo obligatorio.

### Panel de Jurado y Delegado de Mesa (`/jurado/*` y `/mesa/*`)
- **Evaluación en Tiempo Real**: Visualización de la presentación activa en escenario, fotografía del participante, departamento y cronómetro sincronizado.
- **Emisión de Calificaciones por Parámetro**: Controles validados dentro del rango `[valor_minimo, valor_maximo]` de cada parámetro con suma automática en backend sobre 100 puntos.
- **Corrección Auditada**: Posibilidad de rectificar una calificación cuando la presentación está en estado `FINALIZADO` (antes del cierre de la categoría), exigiendo motivo de corrección para auditoría.
- **Controles de Mesa / Cronómetro**: El jurado controlador o delegado de mesa puede iniciar/detener el cronómetro oficial, gestionar el avance de presentaciones y activar la revelación del podio.

### Vista Pública de Gala (`/`)
- **Modo Evaluación en Vivo**: Tarjetas de gala con retrato del participante, bandera departamental, posición y promedio parcial actualizado al instante vía WebSockets (excluyendo visualmente el voto secreto mientras no se revele).
- **Modal de Mesa Evaluadora**: Al seleccionar un participante se muestra el estado de los 5 jurados y la reserva del jurado secreto (`🔒 Reservado`).
- **Modo Podio de Gala**: Revelación animada de tarjetas 3D (*flip cards*) para el 3° Lugar, 2° Lugar y 1° Lugar (o proclamación de Ganador Único en las categorías **Ñusta** y **Especial**), acompañada de la tabla de clasificación general.

---

## 3. Reglas Oficiales de Evaluación y Categorías

### Concepto de Puntaje (Base 100 Puntos)
El porcentaje de cada parámetro **no actúa como multiplicador**, sino que representa directamente el **puntaje máximo obtenible** en ese criterio. La suma de los valores máximos de todos los parámetros de una categoría es siempre **100 puntos**:

$$\text{Nota del Jurado} = \sum_{i=1}^{n} \text{Valor Parámetro}_i \quad \in [n, 100]$$

- **Preselección (3 jurados)**: $\text{Nota Final} = \frac{J_1 + J_2 + J_3}{3}$
- **Etapa Final (5 jurados, incluido el Jurado Secreto)**: $\text{Nota Final} = \frac{J_1 + J_2 + J_3 + J_4 + J_5}{5}$

### Las 13 Categorías Oficiales (Convocatoria 2026)

| # | Categoría | Parámetros y Puntajes Máximos | Premiación |
|---:|---|---|---|
| 1 | **Ñusta** | Interpretación vocal (30), Interpretación instrumental (40), Carisma, dominio de escenario y conocimientos culturales (15), Indumentaria representativa (15) | **Ganadora Única** (Ñusta del Festival) |
| 2 | **Solista vocal** | Interpretación (40), Técnica vocal (30), Expresión y dominio de escenario (20), Indumentaria (10) | Podio (1°, 2° y 3° lugar) |
| 3 | **Solista instrumental – Armónico** | Dominio instrumental (50), Interpretación y expresión (30), Indumentaria (20) | Podio (1°, 2° y 3° lugar) |
| 4 | **Solista instrumental – Melódico** | Dominio instrumental (50), Interpretación y expresión (30), Indumentaria (20) | Podio (1°, 2° y 3° lugar) |
| 5 | **Dúo vocal instrumental** | Interpretación vocal y armonía (30), Interpretación instrumental (30), Expresión y dominio de escenario (25), Indumentaria (15) | Podio (1°, 2° y 3° lugar) |
| 6 | **Trío vocal instrumental** | Interpretación vocal y armonía (30), Interpretación instrumental (30), Expresión y dominio de escenario (25), Indumentaria (15) | Podio (1°, 2° y 3° lugar) |
| 7 | **Grupo vocal instrumental** | Interpretación vocal y armonía (35), Interpretación y arreglos instrumentales (35), Expresión y dominio de escenario (15), Indumentaria (15) | Podio (1°, 2° y 3° lugar) |
| 8 | **Composición** | Estructura musical (40), Letra (40), Originalidad (20) | Podio (1°, 2° y 3° lugar) |
| 9 | **Estudiantina** | Interpretación y ejecución instrumental (40), Arreglos musicales propios / Armonía (40), Indumentaria (20) | Podio (1°, 2° y 3° lugar) |
| 10 | **Autóctono** | Dominio instrumental (30), Armonía instrumental (30), Dominio escénico y originalidad (20), Indumentaria (20) | Podio (1°, 2° y 3° lugar) |
| 11 | **Solista vocal infantil** | Interpretación (50), Expresión y dominio de escenario (20), Técnica vocal (15), Indumentaria (15) | Podio (1°, 2° y 3° lugar) |
| 12 | **Solista instrumental infantil** | Interpretación instrumental (60), Expresión y dominio de escenario (30), Indumentaria (10) | Podio (1°, 2° y 3° lugar) |
| 13 | **Especial** | Técnica vocal (35), Técnica instrumental (35), Dominio escénico y originalidad (15), Indumentaria (15) | **Ganador Único** (1° Lugar) |

---

## 4. Especificaciones Tecnológicas y Arquitectura

### Stack Tecnológico

| Capa | Tecnología | Versión / Detalle |
|---|---|---|
| **Backend Framework** | Laravel | `^12.0` |
| **Lenguaje** | PHP | `^8.2` (extensiones: `pdo_mysql`, `mbstring`, `zip`, `xml`, `gd`, `fileinfo`, `pcntl`/`sockets`) |
| **Tiempo Real (WebSockets)** | Laravel Reverb + Laravel Echo + Pusher JS | `@laravel/reverb ^1.11`, `laravel-echo ^2.5`, `pusher-js ^8.6` |
| **Base de Datos** | MySQL / MariaDB (Producción/Local) y SQLite (Tests) | MySQL `8.0+` / MariaDB `10.6+` (`utf8mb4_unicode_ci`) |
| **Frontend & Estilos** | Blade + Tailwind CSS + Alpine.js + Vite | Tailwind `^3.1`, Alpine.js `^3.4`, Vite `^7.0` |
| **Testing** | PHPUnit | `^11.5` (Suite unitaria y de integración en memoria) |

### Arquitectura Modular (`app/Modules`)

El código está organizado por dominios funcionales dentro de `app/Modules/` y utilidades transversales en `app/Support/`:

```text
app/
├── Modules/
│   ├── Auditoria/        # Consultas forenses de historial de cambios
│   ├── Calificaciones/   # Modelos, controladores, eventos y servicios de cálculo/corrección
│   ├── Categorias/       # Categorías oficiales, estados de revelación y cierre
│   ├── Departamentos/    # Los 9 departamentos de Bolivia y resolución de banderas
│   ├── Fases/            # Etapas PRESELECCION y FINAL
│   ├── Importaciones/    # Lectores XLSX, CSV y JSON y validación de lotes
│   ├── Inscripciones/    # Alta/edición de inscripciones y servicio compartido RegistrarInscripcionService
│   ├── Jurados/          # Asignación de jurados, mesa evaluadora y gestión de jurado secreto
│   ├── Parametros/       # Catálogo de parámetros de evaluación
│   ├── Participantes/    # Gestión de participantes y fotografías
│   ├── Presentaciones/   # Estados de escenario y cronómetro oficial
│   ├── Rankings/         # Cálculo de posiciones en vivo y eventos de difusión
│   ├── Reportes/         # Acta notarial, sábana de notas y exportadores CSV
│   └── Usuarios/         # Autenticación, roles (ADMIN / JURADO) y dashboards
└── Support/
    └── Enums/            # Enums tipados de estados, fases y roles
```

---

## 5. Estrategia de Despliegue 1: Local con XAMPP

Esta estrategia utiliza **PHP y MySQL (MariaDB) incluidos en XAMPP** en Windows, ideal para estaciones locales sin contenedores.

### 5.1. Requisitos Previos
1. **XAMPP** con **PHP 8.2+** y **MySQL/MariaDB**.
2. **Composer** instalado globalmente.
3. **Node.js 20+** y **NPM**.
4. En el archivo `C:\xampp\php\php.ini`, verificar que estén habilitadas (sin `;` al inicio) las extensiones:
   ```ini
   extension=pdo_mysql
   extension=mbstring
   extension=zip
   extension=gd
   extension=fileinfo
   ```

### 5.2. Crear la Base de Datos en XAMPP
1. Abrir el **XAMPP Control Panel** e iniciar los módulos **Apache** y **MySQL** (puerto por defecto `3306`).
2. Ingresar a `http://localhost/phpmyadmin` o usar la consola MySQL de XAMPP y ejecutar:
   ```sql
   CREATE DATABASE IF NOT EXISTS festival_acb
     CHARACTER SET utf8mb4
     COLLATE utf8mb4_unicode_ci;
   ```

### 5.3. Instalación y Configuración del Proyecto
1. Clonar o ubicar el proyecto y abrir una terminal en la raíz del repositorio:
   ```powershell
   composer install
   npm install
   ```
2. Copiar el archivo de variables de entorno:
   ```powershell
   copy .env.example .env
   ```
3. Editar el archivo `.env` ajustando la conexión a MySQL de XAMPP (puerto `3306`, usuario `root` sin contraseña por defecto en XAMPP) y las credenciales de Reverb:
   ```dotenv
   APP_NAME="Aquí Canta Bolivia"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=festival_acb
   DB_USERNAME=root
   DB_PASSWORD=

   BROADCAST_CONNECTION=reverb
   FILESYSTEM_DISK=local
   QUEUE_CONNECTION=database
   CACHE_STORE=database
   SESSION_DRIVER=database

   REVERB_APP_ID=935767
   REVERB_APP_KEY=rlml9nwlxkswhus5t6ca
   REVERB_APP_SECRET=xzx65mxlpwktbspb0bpp
   REVERB_HOST="127.0.0.1"
   REVERB_PORT=6080
   REVERB_SCHEME=http
   REVERB_SERVER_HOST=0.0.0.0
   REVERB_SERVER_PORT=6080
   ```
4. Generar la clave de aplicación, crear el enlace simbólico de almacenamiento de imágenes y ejecutar migraciones con los seeders oficiales:
   ```powershell
   php artisan key:generate
   php artisan storage:link
   php artisan migrate --seed
   ```
5. Compilar los assets de frontend (Tailwind CSS + JS + Laravel Echo):
   ```powershell
   npm run build
   ```

### 5.4. Iniciar los Servicios en XAMPP
Abrir **dos terminales** en la raíz del proyecto:

- **Terminal 1 — Servidor Web Laravel**:
  ```powershell
  php artisan serve --host=0.0.0.0 --port=8000
  ```
- **Terminal 2 — Servidor WebSocket (Laravel Reverb)**:
  ```powershell
  php artisan reverb:start --host=0.0.0.0 --port=6080
  ```

Acceder en el navegador a: `http://127.0.0.1:8000`

---

## 6. Estrategia de Despliegue 2: Despliegue Completo con Docker Compose en Servidor Debian

El proyecto incluye una arquitectura completa en contenedores definida en [`docker-compose.yml`](docker-compose.yml), [`Dockerfile`](Dockerfile) y [`docker/app/entrypoint.sh`](docker/app/entrypoint.sh), diseñada para desplegarse en un **servidor Debian (Debian 11 Bullseye / Debian 12 Bookworm)** donde ya puedan existir otros servicios en ejecución (Apache, Nginx, MySQL/MariaDB local, aplicaciones en puertos `80`, `443`, `3306`, `8000` u `8080`).

### 6.1. Arquitectura de Red Aislada y Enrutamiento de Puertos (Sin Colisiones)

Para evitar cualquier conflicto con servicios preexistentes en el servidor Debian:

1. **Subred Interna Aislada (`acb_festival_network` — `172.28.10.0/24`)**: Todos los contenedores se comunican internamente mediante IPs fijas y nombres DNS de Docker (`mysql:3306`, `reverb:6080`, `app:8000`) sin pasar por los puertos del sistema anfitrión.
2. **Puertos Externos No Estándar y Configurables**: Los puertos publicados en el host Debian utilizan rangos altos libres de colisión y las bases de datos se enlazan únicamente al loopback local (`127.0.0.1`).

| Servicio | Contenedor | IP Interna (`acb_net`) | Puerto Interno | Enlace en Host Debian (Por defecto) | Propósito y Prevención de Conflicto |
|---|---|---|---:|---|---|
| `mysql` | `acb_mysql` | `172.28.10.10` | `3306` | `127.0.0.1:3307` | Evita chocar con un MySQL/MariaDB nativo del servidor (`3306`) y bloquea el acceso directo desde internet. |
| `app` | `acb_app` | `172.28.10.20` | `8000` | `0.0.0.0:8085` | Evita chocar con Apache/Nginx (`80`/`443`), otras apps PHP/Python (`8000`) o Tomcat/Proxies (`8080`). |
| `reverb` | `acb_reverb` | `172.28.10.30` | `6080` | `0.0.0.0:6085` | Servidor WebSocket en tiempo real; evita colisiones en `6080`/`8080`. |
| `phpmyadmin` | `acb_phpmyadmin` | `172.28.10.40` | `80` | `127.0.0.1:8086` | Gestión de BD enlazada solo a localhost en el puerto `8086` (acceso seguro vía túnel SSH o proxy). |

---

### 6.2. Preparación del Servidor Debian e Instalación de Docker

Ejecutar los siguientes comandos en el servidor Debian:

```bash
# 1. Actualizar índices de paquetes e instalar utilidades requeridas
sudo apt-get update
sudo apt-get install -y ca-certificates curl gnupg git ufw

# 2. Agregar clave GPG oficial de Docker y repositorio para Debian
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# 3. Instalar Docker Engine y Docker Compose Plugin
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# 4. Habilitar servicio Docker y permitir uso al usuario actual
sudo systemctl enable --now docker
sudo usermod -aG docker $USER
```

#### Verificación previa de puertos en uso en el servidor Debian
Antes de levantar el stack, puedes comprobar qué puertos están ocupados en el servidor Debian con:

```bash
sudo ss -tulnp | grep -E ':(3307|8085|6085|8086)\b'
```
Si la salida está vacía, los cuatro puertos por defecto están completamente libres. Si alguno estuviera ocupado, basta con cambiar su variable en el archivo `.env` (`APP_EXTERNAL_PORT`, `REVERB_EXTERNAL_PORT`, `DB_EXTERNAL_PORT`, `PMA_EXTERNAL_PORT`).

---

### 6.3. Clonación y Configuración de Variables de Entorno (`.env`)

```bash
# 1. Clonar el repositorio en /opt o /var/www
sudo mkdir -p /var/www/aqui_canta_bolivia
sudo chown -R $USER:$USER /var/www/aqui_canta_bolivia
git clone <URL_DEL_REPOSITORIO> /var/www/aqui_canta_bolivia
cd /var/www/aqui_canta_bolivia

# 2. Crear el archivo .env a partir de la plantilla
cp .env.example .env
```

Editar el archivo `.env` (`nano .env`) configurando la IP o dominio del servidor Debian y los puertos enrutados:

```dotenv
APP_NAME="Aquí Canta Bolivia"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
# Reemplazar 192.168.1.100 por la IP o dominio real del servidor Debian:
APP_URL=http://192.168.1.100:8085

# --- Enrutamiento de Puertos en el Host Debian (Sin colisión) ---
APP_BIND_IP=0.0.0.0
APP_EXTERNAL_PORT=8085

REVERB_BIND_IP=0.0.0.0
REVERB_EXTERNAL_PORT=6085
REVERB_EXTERNAL_SCHEME=http

DB_BIND_IP=127.0.0.1
DB_EXTERNAL_PORT=3307

PMA_BIND_IP=127.0.0.1
PMA_EXTERNAL_PORT=8086

# --- Base de Datos (Comunicación interna Docker hacia el servicio 'mysql') ---
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=festival_acb
DB_USERNAME=acb_app
DB_PASSWORD=acb_local_2026
MYSQL_ROOT_PASSWORD=root_acb_2026

# --- Sesiones, Caché y Colas ---
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

# --- WebSockets (Laravel Reverb) ---
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=935767
REVERB_APP_KEY=rlml9nwlxkswhus5t6ca
REVERB_APP_SECRET=xzx65mxlpwktbspb0bpp
REVERB_HOST=reverb
REVERB_PORT=6080
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=6080

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_PORT="${REVERB_EXTERNAL_PORT}"
VITE_REVERB_SCHEME="${REVERB_EXTERNAL_SCHEME}"
```

---

### 6.4. Construcción y Despliegue Automatizado con Docker Compose

Al ejecutar `docker compose up -d --build`, el contenedor `acb_app` ejecuta automáticamente el script [`docker/app/entrypoint.sh`](docker/app/entrypoint.sh) que realiza todo el aprovisionamiento en orden:

```bash
# Construir la imagen Debian/PHP 8.2/Node 20 y levantar todos los servicios en segundo plano
docker compose up -d --build
```

#### ¿Qué comandos ejecuta automáticamente el contenedor al iniciar?
El script de entrada (`docker/app/entrypoint.sh`) y la inicialización de MySQL (`docker/mysql/init/001_database.sql`) ejecutan automáticamente la siguiente secuencia de comandos dentro del contenedor:

```bash
# 1. Instalar dependencias de PHP con Composer
composer install --no-interaction --prefer-dist --optimize-autoloader

# 2. Instalar dependencias de Node.js y compilar los assets de Vite (Tailwind CSS + JS + Echo)
npm install --no-audit --no-fund
npm run build

# 3. Generar la clave criptográfica de Laravel (si no existe en .env) y enlazar storage
php artisan key:generate --force --ansi
php artisan storage:link --force --ansi

# 4. Crear la base de datos 'festival_acb' con codificación utf8mb4 y otorgar privilegios al usuario
mysql -h mysql -P 3306 -u root -p"$MYSQL_ROOT_PASSWORD" \
  -e "CREATE DATABASE IF NOT EXISTS \`festival_acb\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; \
      GRANT ALL PRIVILEGES ON \`festival_acb\`.* TO 'acb_app'@'%'; FLUSH PRIVILEGES;"

# 5. Ejecutar todas las migraciones de la base de datos
php artisan migrate --force --ansi

# 6. Ejecutar los seeders oficiales de la Convocatoria 2026 (9 departamentos, fases, 13 categorías, parámetros y usuario admin)
php artisan db:seed --force --ansi

# 7. Limpiar cachés y levantar el servidor web (en acb_app) y el servidor WebSocket (en acb_reverb)
php artisan config:clear --ansi
php artisan serve --host=0.0.0.0 --port=8000
# Y en el contenedor acb_reverb:
php artisan reverb:start --host=0.0.0.0 --port=6080
```

---

### 6.5. Comandos de Verificación y Ejecución Manual en Debian

Si necesitas verificar el despliegue o volver a ejecutar cualquiera de los pasos manualmente desde la terminal del servidor Debian:

```bash
# Ver el estado de salud (healthy/running) y puertos asignados de los 4 contenedores
docker compose ps

# Seguir en vivo los logs de instalación, migraciones y seeders del contenedor principal
docker compose logs -f app

# Seguir los logs del servidor WebSocket Reverb
docker compose logs -f reverb

# Ejecutar manualmente la instalación de dependencias PHP y Node dentro del contenedor
docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader
docker compose exec app npm install
docker compose exec app npm run build

# Crear/verificar manualmente la base de datos desde el contenedor MySQL
docker compose exec mysql mysql -u root -proot_acb_2026 \
  -e "CREATE DATABASE IF NOT EXISTS festival_acb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar manualmente las migraciones y los seeders oficiales
docker compose exec app php artisan migrate --seed --force

# Reiniciar las presentaciones a estado POR_PARTICIPAR después de un simulacro
docker compose exec app php artisan festival:reset-presentaciones
```

---

### 6.6. (Opcional) Enrutamiento con Nginx Reverse Proxy en el Servidor Debian

Si el servidor Debian ya cuenta con **Nginx** corriendo en los puertos `80`/`443` para otros sistemas y deseas publicar **Aquí Canta Bolivia** bajo un dominio o subdominio propio (unificando HTTP y WebSockets sin exponer puertos adicionales en el firewall), cambia en `.env`:

```dotenv
APP_BIND_IP=127.0.0.1
REVERB_BIND_IP=127.0.0.1
VITE_REVERB_PORT=80
```

Y crea el bloque de virtual host en `/etc/nginx/sites-available/aqui_canta_bolivia.conf`:

```nginx
server {
    listen 80;
    server_name festival.tudominio.gob.bo;

    client_max_body_size 64M;

    # 1. Enrutar tráfico Web hacia el contenedor acb_app (127.0.0.1:8085)
    location / {
        proxy_pass http://127.0.0.1:8085;
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # 2. Enrutar WebSockets de Laravel Reverb (/app y /apps) hacia acb_reverb (127.0.0.1:6085)
    location ~ ^/(app|apps) {
        proxy_pass http://127.0.0.1:6085;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
        proxy_send_timeout 60s;
    }
}
```

Habilitar el sitio y recargar Nginx sin afectar los demás servicios del servidor Debian:

```bash
sudo ln -s /etc/nginx/sites-available/aqui_canta_bolivia.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 7. Ejecución en Red Local (LAN del Evento / Escenario)

Cuando el sistema se despliega en una red local para conectar múltiples tablets/laptops de jurados y la pantalla gigante del escenario:

1. Obtener la dirección IP local del servidor anfitrión (por ejemplo, `192.168.20.154`).
2. Actualizar en `.env`:
   ```dotenv
   APP_URL=http://192.168.20.154:8000
   REVERB_HOST="192.168.20.154"
   REVERB_PORT=6080
   REVERB_SERVER_HOST=0.0.0.0
   REVERB_SERVER_PORT=6080
   ```
3. Recompilar el bundle frontend para que `Laravel Echo` en los navegadores clientes se conecte a la IP del servidor Reverb:
   ```powershell
   php artisan config:clear
   npm run build
   ```
4. Ejecutar ambos servicios escuchando en todas las interfaces (`0.0.0.0`):
   ```powershell
   php artisan serve --host=0.0.0.0 --port=8000
   php artisan reverb:start --host=0.0.0.0 --port=6080
   ```
5. Asegurarse de permitir los puertos TCP `8000` y `6080` en el Firewall del sistema operativo anfitrión.

---

## 8. Credenciales Iniciales y Comandos Útiles

### Usuario Administrador por Defecto (Creado por `DatabaseSeeder`)
- **URL de acceso**: `/login`
- **Correo electrónico**: `admin@acb.local`
- **Usuario**: `admin`
- **Contraseña**: `admin123`

### Comandos Artisan del Proyecto

| Comando | Descripción |
|---|---|
| `php artisan migrate --seed` | Ejecuta las migraciones y carga los 9 departamentos, fases, las 13 categorías con sus parámetros oficiales y el usuario administrador. |
| `php artisan storage:link` | Conecta `public/storage` con `storage/app/public` para servir las fotografías de los participantes. |
| `php artisan festival:reset-presentaciones` | Reinicia todas las presentaciones al estado `POR_PARTICIPAR`, limpia calificaciones/historial, reinicia cronómetros y oculta podios (ideal tras pruebas o simulacros previos al evento). |
| `php artisan reverb:start --host=0.0.0.0 --port=6080` | Inicia el servidor WebSocket en tiempo real para cronómetros, calificaciones, ranking y revelación de podio. |

---

## 9. Pruebas Automatizadas

El proyecto cuenta con una suite completa de pruebas unitarias y de integración (`tests/Unit` y `tests/Feature`) que se ejecutan sobre **SQLite en memoria** sin afectar la base de datos operativa:

```powershell
php artisan test
```
