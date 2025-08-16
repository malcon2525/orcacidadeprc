# Usar imagem base PHP 8.1 com Apache (versão Alpine para ser mais leve)
FROM php:8.1-apache

# Instalar extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libzip-dev \
    libxslt1-dev \
    libsqlite3-dev \
    locales \
    gettext \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    curl \
    fileinfo \
    gd \
    intl \
    gettext \
    mbstring \
    exif \
    mysqli \
    pdo_mysql \
    pdo_sqlite \
    zip \
    xsl

# Habilitar mod_rewrite do Apache para o Laravel
RUN a2enmod rewrite

# Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar o arquivo composer.json e composer.lock antes de copiar os outros arquivos para melhorar o cache
COPY composer.json composer.lock /var/www/html/

# Instalar dependências do Laravel com Composer
RUN composer install --no-scripts --no-interaction --optimize-autoloader --prefer-dist

# Agora copiar os demais arquivos do projeto para o container
COPY . /var/www/html

# Ajustar o DocumentRoot para o diretório public do Laravel
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Instalar Node.js versão 18.x
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar as dependências do npm
RUN npm install

# Gerar o link simbólico para o diretório de armazenamento
RUN php artisan storage:link

# Limpar caches
RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Corrigir permissões dos diretórios
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public /var/www/html/vendor

# Adicionar configuração para o ServerName no Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expor a porta 80
EXPOSE 80

# Comando final para rodar o Apache
CMD ["apache2-foreground"]
