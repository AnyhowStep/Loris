mysql -e 'CREATE DATABASE LorisTest'
mysql LorisTest < SQL/0000-00-00-schema.sql
mysql -e 'SELECT * FROM LorisTest.users'
cat SQL/0000-00-00-schema.sql