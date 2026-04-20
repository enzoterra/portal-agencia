# CASO USE DOCKER

FROM php:8.2-cli

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos do projeto
COPY . .

# Instala dependências (sem dev opcional, pode ajustar)
RUN composer install --no-interaction --prefer-dist

# Expor porta
EXPOSE 8000

# Subir servidor Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]