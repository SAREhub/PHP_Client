FROM rabbitmq:3.6.12-management-alpine

RUN apk add --no-cache curl openssl python \
	&& mkdir -p /home/testca/certs \
	&& mkdir -p /home/testca/private \
	&& chmod 700 /home/testca/private \
	&& echo 01 > /home/testca/serial \
	&& touch /home/testca/index.txt

COPY openssl.cnf /home/testca
COPY prepare_server.sh /home/
COPY rabbitmq.config /etc/rabbitmq/rabbitmq.config

RUN mkdir -p /home/server \
	&& chmod +x /home/prepare_server.sh \
    && /bin/sh /home/prepare_server.sh
