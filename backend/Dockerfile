# PHP com extensões e utilitários
FROM php:8.2-cli

# Instala dependências e utilitários
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    curl \
    git \
    libxml2-dev \
    libicu-dev \
    libsqlite3-dev \
    sqlite3 \
    nano \
    vim \
    less \
    procps \
    iputils-ping \
    net-tools \
    lsb-release \
    bash \
    htop \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip intl

# Instala Xdebug para cobertura de testes
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copia o Composer da imagem oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Xdebug para debug remoto
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Define o diretório de trabalho
WORKDIR /var/www/html/app

# Expondo a porta padrão do servidor PHP embutido
EXPOSE 8000

# Comando padrão para iniciar o servidor PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "webroot"]
