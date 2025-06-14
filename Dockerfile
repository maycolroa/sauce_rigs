# Use the official PHP 7.2 image
FROM php:7.4.33

#ENV COMPOSER_ALLOW_SUPERUSER=1

# Instala dependencias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nginx

# Instala extensiones PHP
RUN docker-php-ext-install \
    pdo_mysql  \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala Node.js y npm (versión 8.10.0 y 3.5.2)
#RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
#RUN apt-get install -y nodejs npm
#RUN npm install -g npm@3.5.2

RUN apt-get install -y npm

# Instalar nvm
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 16.20.2

RUN mkdir -p $NVM_DIR \
    && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | NVM_DIR=$NVM_DIR bash

# Configurar nvm para todos los usuarios
RUN echo 'export NVM_DIR="$NVM_DIR"' > /etc/profile.d/nvm.sh \
    && echo '[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"' >> /etc/profile.d/nvm.sh

# Instalar Node.js usando nvm
RUN /bin/bash -c "source $NVM_DIR/nvm.sh && nvm install $NODE_VERSION && nvm alias default $NODE_VERSION && nvm use default"

# Verificar la instalación de Node.js
RUN /bin/bash -c "source $NVM_DIR/nvm.sh && node -v"

# Ejecutar npm install
#RUN /bin/bash -c "source $NVM_DIR/nvm.sh && npm install"

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . .

# Crear las carpetas necesarias y establecer los permisos adecuados
RUN mkdir -p /var/www/storage/framework/cache/data \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache

# Install PHP dependencies
#RUN composer install

# Install Node dependencies
#RUN npm install

# Expose port 80 and start PHP server
EXPOSE 80/tcp

CMD php artisan serve --host=0.0.0.0 --port=80

# Mantener el contenedor en ejecución
#CMD ["tail", "-f", "/dev/null"]