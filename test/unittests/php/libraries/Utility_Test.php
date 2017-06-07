<?php
    require_once __DIR__ . '/../../../../vendor/autoload.php';

    class Utility_Test extends PHPUnit_Framework_TestCase {
        public static function setUpBeforeClass () {
            $factory = NDB_Factory::singleton();
            $factory->settings(__DIR__ . "/../../../config.xml");
            
            Database::singleton(
                $factory->settings()->dbName(),
                $factory->settings()->dbUserName(),
                $factory->settings()->dbPassword(),
                $factory->settings()->dbHost()
            );
            
            # Set up the MySQL database, install the Schema, create a MySQL user
            # for the config file, and reset the Loris user's password for testing
            exec("mysql -e 'DROP DATABASE LorisTest'");
            exec("mysql -e 'CREATE DATABASE LorisTest'");
            exec("mysql LorisTest < SQL/0000-00-00-schema.sql");
            /*exec("");
            - 
            - 
            - mysql LorisTest < SQL/0000-00-01-Permission.sql
            - mysql LorisTest < SQL/0000-00-02-Menus.sql
            - mysql LorisTest < SQL/0000-00-03-ConfigTables.sql
            - mysql LorisTest < SQL/0000-00-04-Help.sql
            - mysql LorisTest < docs/instruments/radiology_review.sql
            - mysql LorisTest -u root -e "GRANT UPDATE,INSERT,SELECT,DELETE,DROP,CREATE TEMPORARY TABLES ON LorisTest.* TO 'SQLTestUser'@'localhost' IDENTIFIED BY 'TestPassword' WITH GRANT OPTION"
            - cp docs/config/config.xml project/config.xml
            - cp docs/config/config.xml test/config.xml
            - sed -i -e "s/%HOSTNAME%/127.0.0.1/g" -e "s/%USERNAME%/SQLTestUser/g" -e "s/%PASSWORD%/TestPassword/g" -e "s/%DATABASE%/LorisTest/g" project/config.xml
            - sed -i -e "s/%HOSTNAME%/127.0.0.1/g" -e "s/%USERNAME%/SQLTestUser/g" -e "s/%PASSWORD%/TestPassword/g" -e "s/%DATABASE%/LorisTest/g" test/config.xml
            # Set the admin account password to a known value for testing. This needs to be done
            # after the config.xml is setup.
            - cd tools
            - echo "testpass" | php ./resetpassword.php admin
            - mysql LorisTest -e "UPDATE users SET Pending_approval='N', Password_expiry='2100-01-01' WHERE ID=1"
            - cd ..
            - mysql LorisTest -e "UPDATE Config SET Value='$(pwd)/' WHERE ConfigID=(SELECT ID FROM ConfigSettings WHERE Name='base')"
            - mysql LorisTest -e "UPDATE Config SET Value='http://localhost:8000' WHERE ConfigID=(SELECT ID FROM ConfigSettings WHERE Name='url')"

            # Set up the testing instrument environment
            - cp test/test_instrument/NDB_BVL_Instrument_testtest.class.inc project/instruments/NDB_BVL_Instrument_testtest.class.inc
            - mysql LorisTest -e "INSERT INTO psc (CenterID, Name, Alias, MRI_alias, Study_site) VALUES (255, 'NOT-A-STUDY-SITE', 'D01', 'D01', 'N')"
            - mysql LorisTest -e "INSERT INTO psc (CenterID, Name, Alias, MRI_alias, Study_site) VALUES (254, 'A-STUDY-SITE', 'D02', 'D02', 'Y')"
            - mysql LorisTest -e "INSERT INTO test_names (Test_name, Full_name, Sub_group,IsDirectEntry) VALUES ('testtest', 'testtest', '1','1')"
            - mysql LorisTest -e "INSERT INTO test_battery (Test_name, AgeMinDays, AgeMaxDays, Active, Stage, SubprojectID, Visit_label, CenterID) VALUES ('testtest', '1', '99999', 'Y', 'V1', '1', '2', NULL)"
            - cd tools
            - find ../project/instruments/NDB_BVL_Instrument_testtest.class.inc | php lorisform_parser.php
            - find ../project/instruments/NDB_BVL_Instrument_testtest.class.inc | php generate_tables_sql.php
            - cd ..
            - mysql LorisTest < project/tables_sql/testtest.sql*/
        }
        public function assertTableCount ($table, $expected_count) {
            $escaped_table = Database::singleton()->escape($table);
            $count = Database::singleton()->pselectOne("SELECT COUNT(*) FROM {$escaped_table}", []);
            $this->assertEquals($expected_count, $count);
        }
        public function ensureDeleteAll ($table) {
            $escaped_table = Database::singleton()->escape($table);
            Database::singleton()->prepare("DELETE FROM {$escaped_table}")->execute();
            
            $this->assertTableCount($table, 0);
        }
        
        function test_calculateAge () {
            $age = Utility::calculateAge("2017-08-01", "2017-09-01");
            $this->assertEquals(0, $age["year"]);
            $this->assertEquals(1, $age["mon"]);
            $this->assertEquals(0, $age["day"]);
            
            for ($i=2; $i<=31; ++$i) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $age = Utility::calculateAge("2017-08-{$day}", "2017-09-01");
                $this->assertEquals(0, $age["year"]);
                $this->assertEquals(0, $age["mon"]);
                $this->assertEquals(31-$i, $age["day"]);
            }
        }
        function test_getSiteList () {
            $site_list = Utility::getSiteList();
            $this->assertEquals([
                "1"=>"Data Coordinating Center",
                "254"=>"A-STUDY-SITE"
            ], $site_list);
            
            $site_list = Utility::getSiteList(true);
            $this->assertEquals([
                "1"=>"Data Coordinating Center",
                "254"=>"A-STUDY-SITE"
            ], $site_list);
            
            $site_list = Utility::getSiteList(false);
            $this->assertEquals([
                "1"=>"Data Coordinating Center",
                "255"=>"NOT-A-STUDY-SITE",
                "254"=>"A-STUDY-SITE"
            ], $site_list);
        }
        function test_getAssociativeSiteList () {
            $site_list = Utility::getAssociativeSiteList();
            $this->assertEquals([
                "1"=>"Data Coordinating Center",
                "254"=>"A-STUDY-SITE"
            ], $site_list);
            
            
            $site_list = Utility::getAssociativeSiteList(true, true);
            $this->assertEquals([
                "1"=>"Data Coordinating Center",
                "254"=>"A-STUDY-SITE"
            ], $site_list);
            
            $site_list = Utility::getAssociativeSiteList(true, false);
            $this->assertEquals([
                "254"=>"A-STUDY-SITE"
            ], $site_list);
            
            $site_list = Utility::getAssociativeSiteList(false, true);
            $this->assertEquals([
                "1"=>"Data Coordinating Center",
                "255"=>"NOT-A-STUDY-SITE",
                "254"=>"A-STUDY-SITE"
            ], $site_list);
            $site_list = Utility::getAssociativeSiteList(false, false);
            $this->assertEquals([
                "255"=>"NOT-A-STUDY-SITE",
                "254"=>"A-STUDY-SITE"
            ], $site_list);
        }
        function test_getVisitList () {
            $this->assertTableCount("Visit_Windows", 0);
            
            Database::singleton()->insert("Visit_Windows", [
                "Visit_label"=>"abc2"
            ]);
            Database::singleton()->insert("Visit_Windows", [
                "Visit_label"=>"Abc3"
            ]);
            Database::singleton()->insert("Visit_Windows", [
                "Visit_label"=>"abc0"
            ]);
            Database::singleton()->insert("Visit_Windows", [
                "Visit_label"=>"abc1"
            ]);
            Database::singleton()->insert("Visit_Windows", [
                "Visit_label"=>null
            ]);
            
            $this->assertTableCount("Visit_Windows", 5);
            
            $visit_list = Utility::getVisitList();
            $this->assertEquals([
                "abc0"=>"Abc0",
                "abc1"=>"Abc1",
                "abc2"=>"Abc2",
                "Abc3"=>"Abc3",
                null=>null
            ], $visit_list);
            
            $this->ensureDeleteAll("Visit_Windows");
        }
        function test_getProjectList () {
            $this->assertTableCount("Project", 0);
            
            Database::singleton()->insert("Project", [
                "Name"=>"THE FIRST"
            ]);
            Database::singleton()->insert("Project", [
                "Name"=>"THE SECOND"
            ]);
            Database::singleton()->insert("Project", [
                "Name"=>"DUPLICATE PROJECT NAME",
            ]);
            Database::singleton()->insert("Project", [
                "Name"=>"DUPLICATE PROJECT NAME"
            ]);
            //NULL Values! Since this is allowed in our DB schema as of
            //2017-06-07
            Database::singleton()->prepare("INSERT INTO Project () VALUES ()")->execute();
            
            $this->assertTableCount("Project", 5);
            
            $project_list = Utility::getProjectList();
            $this->assertEquals([
                1=>"THE FIRST",
                2=>"THE SECOND",
                3=>"DUPLICATE PROJECT NAME",
                4=>"DUPLICATE PROJECT NAME",
                5=>null,
            ], $project_list);
            
            $this->ensureDeleteAll("Project");
        }
        function test_getSubprojectList () {
            $subproject_list = Utility::getSubprojectList();
            $this->assertEquals([], $subproject_list);
        }
    }
?>