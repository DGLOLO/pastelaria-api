FROM php:8.2-fpm

# PASSO 1: Instalar dependências do sistema Linux
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# PASSO 2: Instalar extensões PHP que o Laravel precisa
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        xml \
        bcmath

# PASSO 3: Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PASSO 4: Criar usuário para segurança
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# PASSO 5: Definir onde a aplicação vai ficar
WORKDIR /var/www

# PASSO 6: Copiar todo o código da aplicação
COPY --chown=www:www . .


# PASSO 7: Configurar permissões para Laravel
RUN chown -R www:www /var/www \
    && chmod -R 755 /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# PASSO 8: Mudar para usuário não-root
USER www

# PASSO 9: Expor porta do PHP-FPM
EXPOSE 9000

# PASSO 10: Comando que roda quando container iniciar
CMD ["php-fpm"]