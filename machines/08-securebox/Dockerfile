FROM debian:stable-slim
RUN apt-get update \
 && apt-get install -y --no-install-recommends gcc libc6-dev python3 \
 && rm -rf /var/lib/apt/lists/*
WORKDIR /opt/securebox
COPY app/ /opt/securebox/
RUN gcc -fno-stack-protector -no-pie -O0 -o vuln vuln.c
COPY entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh
EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
