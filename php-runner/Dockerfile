FROM php:8.2-cli

WORKDIR /src

# Habilitar extensão do Postgres
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY . /src