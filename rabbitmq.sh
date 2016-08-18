#!/bin/bash

NB_TASKS=1
SYMFONY_ENV="dev"
#SYMFONY_ENV="prod"

TEXT[0]="rabbitmq:consumer -m 2 send_mail"

while true
do
    for text in "${TEXT[@]}"
    do

    NB_LAUNCHED=$(ps ax | grep "$text" | grep -v grep | wc -l)

    TASK="php todoapp/bin/console ${text} --env=${SYMFONY_ENV}"

    for (( i=${NB_LAUNCHED}; i<${NB_TASKS}; i++ ))
    do
      echo "Start new consumer"
      echo "$TASK"
      nohup $TASK & # nohup - run task as daemon
    done

    done

    echo "Wait 2 sec"
    sleep 2
done