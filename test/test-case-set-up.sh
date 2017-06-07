pwd
whoami
mysql -e 'CREATE DATABASE LorisTest'
mysql LorisTest < SQL/0000-00-00-schema.sql
mysql LorisTest < SQL/0000-00-01-Permission.sql
mysql LorisTest < SQL/0000-00-02-Menus.sql
mysql LorisTest < SQL/0000-00-03-ConfigTables.sql
mysql LorisTest < SQL/0000-00-04-Help.sql
mysql LorisTest < docs/instruments/radiology_review.sql
mysql LorisTest -u root -e "GRANT UPDATE,INSERT,SELECT,DELETE,DROP,CREATE TEMPORARY TABLES ON LorisTest.* TO 'SQLTestUser'@'localhost' IDENTIFIED BY 'TestPassword' WITH GRANT OPTION"
cp docs/config/config.xml project/config.xml
cp docs/config/config.xml test/config.xml
sed -i -e "s/%HOSTNAME%/127.0.0.1/g" -e "s/%USERNAME%/SQLTestUser/g" -e "s/%PASSWORD%/TestPassword/g" -e "s/%DATABASE%/LorisTest/g" project/config.xml
sed -i -e "s/%HOSTNAME%/127.0.0.1/g" -e "s/%USERNAME%/SQLTestUser/g" -e "s/%PASSWORD%/TestPassword/g" -e "s/%DATABASE%/LorisTest/g" test/config.xml
# Set the admin account password to a known value for testing. This needs to be done
# after the config.xml is setup.
cd tools
echo "testpass" | php ./resetpassword.php admin
mysql LorisTest -e "UPDATE users SET Pending_approval='N', Password_expiry='2100-01-01' WHERE ID=1"
cd ..
mysql LorisTest -e "UPDATE Config SET Value='$(pwd)/' WHERE ConfigID=(SELECT ID FROM ConfigSettings WHERE Name='base')"
mysql LorisTest -e "UPDATE Config SET Value='http://localhost:8000' WHERE ConfigID=(SELECT ID FROM ConfigSettings WHERE Name='url')"

# Set up the testing instrument environment
cp test/test_instrument/NDB_BVL_Instrument_testtest.class.inc project/instruments/NDB_BVL_Instrument_testtest.class.inc
mysql LorisTest -e "INSERT INTO psc (CenterID, Name, Alias, MRI_alias, Study_site) VALUES (255, 'NOT-A-STUDY-SITE', 'D01', 'D01', 'N')"
mysql LorisTest -e "INSERT INTO psc (CenterID, Name, Alias, MRI_alias, Study_site) VALUES (254, 'A-STUDY-SITE', 'D02', 'D02', 'Y')"
mysql LorisTest -e "INSERT INTO test_names (Test_name, Full_name, Sub_group,IsDirectEntry) VALUES ('testtest', 'testtest', '1','1')"
mysql LorisTest -e "INSERT INTO test_battery (Test_name, AgeMinDays, AgeMaxDays, Active, Stage, SubprojectID, Visit_label, CenterID) VALUES ('testtest', '1', '99999', 'Y', 'V1', '1', '2', NULL)"
cd tools
find ../project/instruments/NDB_BVL_Instrument_testtest.class.inc | php lorisform_parser.php
find ../project/instruments/NDB_BVL_Instrument_testtest.class.inc | php generate_tables_sql.php
cd ..
mysql LorisTest < project/tables_sql/testtest.sql
cd test