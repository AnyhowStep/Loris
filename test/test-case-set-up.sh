mysql -e 'CREATE DATABASE LorisTest'
cat SQL/0000-00-00-schema.sql | mysql LorisTest
mysql -e 'SELECT * FROM LorisTest.users'