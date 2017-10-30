#!/usr/bin/env sh

/usr/bin/rabbitmqadmin -k --ssl --ssl-disable-hostname-verification  -V sareHub -P 15671 -u sarehub -p test $@