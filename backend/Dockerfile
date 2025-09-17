FROM php:8.3-fpm-alpine

WORKDIR /var/www

# dependências essenciais
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    mysql-dev \
    $PHPIZE_DEPS

# extensões PHP necessárias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

#  Nginx
RUN mkdir -p /run/nginx && \
    rm -rf /etc/nginx/conf.d/default.conf

#  Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar arquivos de configuração
COPY ./docker/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./docker/nginx/sites.conf /etc/nginx/conf.d/default.conf

# Copiar código da aplicação
COPY --chown=www-data:www-data . .

# Instalar dependências do Composer (apenas produção)
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Limpar dependências de compilação e cache
RUN apk del $PHPIZE_DEPS && \
    rm -rf /var/cache/apk/* /tmp/*

# Expor porta
EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]