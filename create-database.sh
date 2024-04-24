#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS gohighlevel;
    GRANT ALL PRIVILEGES ON \`gohighlevel%\`.* TO '$MYSQL_USER'@'%';
EOSQL
