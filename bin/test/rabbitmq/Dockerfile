FROM rabbitmq:3.6.12-management-alpine

RUN apk add --no-cache curl openssl python \
	&& mkdir -p /home/testca/certs \
	&& mkdir -p /home/testca/private \
	&& chmod 700 /home/testca/private \
	&& echo 01 > /home/testca/serial \
	&& touch /home/testca/index.txt

COPY openssl.cnf /home/testca
COPY prepare_server.sh /home/
COPY local_admin.sh /usr/bin/

RUN mkdir -p /home/server \
	&& chmod +x /home/prepare_server.sh \
	&& curl https://raw.githubusercontent.com/rabbitmq/rabbitmq-management/rabbitmq_v3_6_12/bin/rabbitmqadmin -o /usr/bin/rabbitmqadmin \
    && chmod +x /usr/bin/rabbitmqadmin \
    && chmod +x /usr/bin/local_admin.sh \
    && /bin/sh /home/prepare_server.sh
