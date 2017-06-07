#!/usr/bin/env bash
mysql -e 'CREATE DATABASE LorisTest'
cat SQL/0000-00-00-schema.sql | mysql -h localhost LorisTest
mysql -e 'SELECT * FROM LorisTest.users'