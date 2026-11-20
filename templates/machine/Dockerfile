# Template Dockerfile. Sesuaikan base image dengan kategori mesin.
FROM php:8.2-apache
# RUN apt-get update && apt-get install -y --no-install-recommends <paket> && rm -rf /var/lib/apt/lists/*
COPY app/ /var/www/html/
COPY entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh
EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
