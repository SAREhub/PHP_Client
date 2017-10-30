#!/usr/bin/env bash

TEXT_BOLD='\e[1m'
TEXT_BLACK='\e[30m'
TEXT_RED='\e[31m'
TEXT_BLUE='\e[34m'
TEXT_YELLOW='\e[33m'
TEXT_RESET='\e[0m'
TEXT_BG_YELLOW='\e[43m'

function dockerutil_print_info {
    local header=$1
    local message=$2
    echo -e "${TEXT_BLUE}${TEXT_BOLD}$header: $message ${TEXT_RESET}" 1>&2
}

function dockerutil_getpwd {
    # for windows users you must sets shared folder on projects
    # help: https://gist.github.com/matthiasg/76dd03926d095db08745
    local DOCKER_SHARED_PROJECT_DIR='//home/docker/projects'
    ret=$PWD

    # check for cygwin users(on windows)
    if [[ `uname` == *"CYGWIN"* ]]; then
        dockerutil_print_info "getpwd" "cygwin env - must use shared dir"
        # replaced projects root dir with shared dir
        ret="${DOCKER_SHARED_PROJECT_DIR}/$(basename $PWD)"
    fi
    echo $ret
}

function dockerutil_setup_dev_app_sources_volume {
    echo $(dockerutil_getpwd)
}

function dockerutil_service_exists {
    local exists=$(docker service ls -q -f "name=${1}")
    [ ! -z "$exists" ]
}

function dockerutil_network_exists {
    local exists=$(docker network ls -q -f "name=${1}\$")
    [ ! -z "$exists" ]
}

function dockerutil_secret_exists {
    local exists=$(docker secret ls -q -f "name=${1}")
    [ "$exists" != '' ]
}

function dockerutil_create_secret {
    local name=$1
    local value=$2
    if ! $(dockerutil_secret_exists $name); then
        dockerutil_print_info "creating secret" $name
        echo $value | docker secret create $name -
    else
        dockerutil_print_info "creating secret" "secret $name exists"
    fi
}

function dockerutil_exec_command {
    local container=$1
    local command=$2
    echo $(docker exec $container /bin/sh -c "$command")
}

function dockerutil_container_get_file_contents {
    local container=$1
    local filename=$2
    if [ ! $(dockerutil_exec_command $container "[ -f $filename ] && echo "1" || echo ''") ]; then
        echo "ERROR FILE NOT EXISTS: $filename"
        exit
    fi
    echo $(dockerutil_exec_command $container "cat $filename")
}

function dockerutil_get_service_container {
    local service_name=$1
    local service_filter="label=com.docker.swarm.service.name=$service_name"
    while [ ! "$(docker ps -f "$service_filter" -f "status=running" -q)" ];
    do
        dockerutil_print_info "get_service_container" "waiting for container of $service_name service"
        sleep 2
    done
    echo $(docker ps -f "$service_filter" -q)
}

function dockerutil_composer_install {
    local install_dir=$1
    docker run \
      --rm \
      --interactive --tty \
      --volume "$install_dir":/app \
      --user $(id -u sarehub):$(id -g sarehub) \
      composer install --no-dev --optimize-autoloader --prefer-dist --no-suggest --ignore-platform-reqs

}