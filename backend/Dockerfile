FROM php:8.3-cli

# Diretório de trabalho
WORKDIR /var/www

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_mysql

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia o código
COPY . /var/www

# Ajusta permissões
RUN chown -R www-data:www-data /var/www

# Instala dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Expõe a porta padrão do Artisan
EXPOSE 8000

# Sobe o servidor embutido
CMD php artisan serve --host=0.0.0.0 --port=8000