# Use a imagem PHP CLI 8.3
FROM php:8.3-cli

# Sinalizando para usar o usuário root
USER root

# Instale dependências do sistema necessárias
RUN apt-get update && apt-get install -y \
    systemctl \
    lsof \
    libssl-dev \
    pkg-config \
    unzip \
    libcurl4-openssl-dev \
    supervisor \
    vim \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip gd pdo pdo_mysql

# Instalando a extensão sockets do php
RUN docker-php-ext-install sockets

# Instale a extensão do MongoDB com suporte a SSL
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Instalar a extensão phpredis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instale o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Defina o diretório de trabalho como o diretório raiz do aplicativo
WORKDIR /var/www/html

# Copie o arquivo composer.lock e o arquivo composer.json para o contêiner
COPY composer.lock composer.json /

# Copie o restante dos arquivos do aplicativo para o contêiner
COPY . /var/www/html

# Exponha a porta 8000 para acessar o servidor Laravel
EXPOSE 8000

# Rodando o composer install
RUN composer install

RUN php artisan key:generate

# Comando para iniciar o servidor Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]