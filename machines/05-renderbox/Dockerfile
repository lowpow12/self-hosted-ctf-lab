FROM php:8.2-apache
COPY app/ /var/www/html/
RUN chown -R root:root /var/www/html && chmod -R 755 /var/www/html
COPY entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh
EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
