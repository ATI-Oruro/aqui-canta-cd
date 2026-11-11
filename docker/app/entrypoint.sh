#!/usr/bin/env bash
set -e

ROLE="${CONTAINER_ROLE:-app}"

cd /var/www/html

if [ "$ROLE" = "app" ]; then
    echo "============================================================"
    echo " [ACB] Iniciando aprovisionamiento del contenedor principal "
    echo "============================================================"

    # 1. Preparar archivo .env si no existe
    if [ ! -f ".env" ]; then
        echo "-> Creando archivo .env a partir de .env.example..."
        cp .env.example .env
    fi

    # 2. Instalar dependencias PHP con Composer
    echo "-> Instalando dependencias de PHP (Composer)..."
    composer install --no-interaction --prefer-dist --optimize-autoloader

    # 3. Instalar dependencias Node.js y compilar assets Vite (con puertos/keys del entorno)
    echo "-> Instalando dependencias Frontend (NPM) y compilando assets (Vite)..."
    npm install --no-audit --no-fund
    npm run build

    # 4. Generar APP_KEY si no está definida en .env
    if ! grep -q "^APP_KEY=base64:" .env; then
        echo "-> Generando clave de aplicación (APP_KEY)..."
        php artisan key:generate --force --ansi
    fi

    # 5. Crear enlace simbólico de almacenamiento público
    echo "-> Configurando enlace simbólico de storage..."
    php artisan storage:link --force --ansi || true

    # 6. Esperar a MySQL y asegurar que la base de datos exista
    echo "-> Verificando conexión a MySQL (${DB_HOST:-mysql}:${DB_PORT:-3306})..."
    MAX_RETRIES=30
    COUNT=0
    until mysqladmin ping -h "${DB_HOST:-mysql}" -P "${DB_PORT:-3306}" -u root -p"${MYSQL_ROOT_PASSWORD:-root_acb_2026}" --silent; do
        COUNT=$((COUNT + 1))
        if [ "$COUNT" -ge "$MAX_RETRIES" ]; then
            echo "ERROR: No se pudo conectar a MySQL después de ${MAX_RETRIES} intentos."
            exit 1
        fi
        echo "   Esperando a MySQL... (${COUNT}/${MAX_RETRIES})"
        sleep 2
    done

    echo "-> Creando/verificando base de datos '${DB_DATABASE:-festival_acb}'..."
    mysql -h "${DB_HOST:-mysql}" -P "${DB_PORT:-3306}" -u root -p"${MYSQL_ROOT_PASSWORD:-root_acb_2026}" \
        -e "CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE:-festival_acb}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON \`${DB_DATABASE:-festival_acb}\`.* TO '${DB_USERNAME:-acb_app}'@'%'; FLUSH PRIVILEGES;"

    # 7. Ejecutar migraciones y seeders oficiales
    echo "-> Ejecutando migraciones de base de datos..."
    php artisan migrate --force --ansi

    echo "-> Ejecutando seeders oficiales de la Convocatoria 2026..."
    php artisan db:seed --force --ansi

    # 8. Limpiar cachés para aplicar variables de entorno actuales
    php artisan config:clear --ansi
    php artisan route:clear --ansi
    php artisan view:clear --ansi

    # Marcar aprovisionamiento listo para contenedores dependientes (Reverb)
    touch /tmp/acb_app_ready

    echo "============================================================"
    echo " [ACB] Servidor Web Laravel escuchando en 0.0.0.0:8000      "
    echo "============================================================"
    exec php artisan serve --host=0.0.0.0 --port=8000

elif [ "$ROLE" = "reverb" ]; then
    echo "============================================================"
    echo " [ACB] Esperando dependencias para iniciar Laravel Reverb   "
    echo "============================================================"
    while [ ! -f "vendor/autoload.php" ]; do
        echo "   Esperando a que el contenedor 'app' instale vendor/..."
        sleep 2
    done

    echo "============================================================"
    echo " [ACB] Servidor WebSocket Reverb escuchando en 0.0.0.0:6080 "
    echo "============================================================"
    exec php artisan reverb:start --host=0.0.0.0 --port=6080
else
    exec "$@"
fi

