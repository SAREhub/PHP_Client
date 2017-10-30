#!/bin/bash
set -e

source bin/dockerutil.sh;

SERVICE_NAME=test_rabbit
IMAGE_TAG=localhost:5000/test_rabbit
AMQP_PORT="30000"
MANAGEMENT_PORT="30001"

if [ "$1" == "clean" ]; then
 dockerutil_print_info "test rabbitmq service" "cleaning..."
 docker service rm $SERVICE_NAME
 sleep 2
 exit
fi

service_exists=$(docker service ls -q -f "name=${SERVICE_NAME}")
if [ ! "$service_exists" ]; then

 docker build --tag $IMAGE_TAG './bin/test/rabbitmq' && docker push $IMAGE_TAG

 dockerutil_print_info "test rabbitmq service" "creating service..."
 docker service create \
    --name $SERVICE_NAME \
    --publish $AMQP_PORT:5671 \
    --publish $MANAGEMENT_PORT:15671 \
    --hostname test_rabbit \
    --limit-cpu 0.5 \
    --limit-memory 500M \
    --env 'RABBITMQ_DEFAULT_VHOST=test' \
    --env 'RABBITMQ_DEFAULT_USER=test' \
    --env 'RABBITMQ_DEFAULT_PASS=test' \
    --env 'RABBITMQ_VM_MEMORY_HIGH_WATERMARK=0.6' \
    --env 'RABBITMQ_SSL_CACERTFILE=/home/testca/cacert.pem' \
    --env 'RABBITMQ_SSL_CERTFILE=/home/server/cert.pem' \
    --env 'RABBITMQ_SSL_KEYFILE=/home/server/key.pem' \
    --env 'RABBITMQ_SSL_FAIL_IF_NO_PEER_CERT=false' \
    --env 'RABBITMQ_SSL_VERIFY=verify_none' \
    --detach \
   $IMAGE_TAG

container=$(dockerutil_get_service_container $SERVICE_NAME)
dockerutil_print_info "test rabbitmq service" "container: $container"
else
 dockerutil_print_info "test rabbitmq service" "rabbit inited before - use './bin/test/rabbit/init.sh clean' for remove"
fi
