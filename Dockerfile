FROM php:8.2-cli

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .

EXPOSE 10000

# api/index.php actúa como router: todas las rutas pasan por él
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} -t api api/index.php"]
