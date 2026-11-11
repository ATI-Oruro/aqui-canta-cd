FROM php:8.2-cli-bookworm

# Evitar prompts interactivos en Debian
ENV DEBIAN_FRONTEND=noninteractive

# Instalar dependencias del sistema, extensiones de PHP, cliente MySQL y Node.js 20
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    ca-certificates \
    gnupg \
    git \
    unzip \
    zip \
    default-mysql-client \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer 2 oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar script de arranque y asegurar saltos de línea LF y permisos de ejecución
COPY docker/app/entrypoint.sh /usr/local/bin/acb-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/acb-entrypoint.sh && chmod +x /usr/local/bin/acb-entrypoint.sh

EXPOSE 8000 6080

ENTRYPOINT ["/usr/local/bin/acb-entrypoint.sh"]

