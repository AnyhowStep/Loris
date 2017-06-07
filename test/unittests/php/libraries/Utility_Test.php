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
        }
        public function assertTableCount ($table, $desired_count) {
            $escaped_table = Database::singleton()->escape($table);
            $count = Database::singleton()->pselectOne("SELECT COUNT(*) FROM {$escaped_table}", []);
            $this->assertEquals($count, $desired_count);
        }
        public function ensureDeleteAll ($table) {
            $escaped_table = Database::singleton()->escape($table);
            Database::singleton()->prepare("DELETE FROM {$escaped_table}")->execute();
            
            $this->assertTableCount($table, 0);
        }
        
        function test_calculateAge () {
            $age = Utility::calculateAge("2017-08-01", "2017-09-01");
            $this->assertEquals($age["year"], 0);
            $this->assertEquals($age["mon"], 1);
            $this->assertEquals($age["day"], 0);
            
            for ($i=2; $i<=31; ++$i) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $age = Utility::calculateAge("2017-08-{$day}", "2017-09-01");
                $this->assertEquals($age["year"], 0);
                $this->assertEquals($age["mon"], 0);
                $this->assertEquals($age["day"], 31-$i);
            }
        }
        function test_getSiteList () {
            $site_list = Utility::getSiteList();
            $this->assertEquals($site_list, [
                "1"=>"Data Coordinating Center",
                "254"=>"A-STUDY-SITE"
            ]);
            
            $site_list = Utility::getSiteList(true);
            $this->assertEquals($site_list, [
                "1"=>"Data Coordinating Center",
                "254"=>"A-STUDY-SITE"
            ]);
            
            $site_list = Utility::getSiteList(false);
            $this->assertEquals($site_list, [
                "1"=>"Data Coordinating Center",
                "255"=>"NOT-A-STUDY-SITE",
                "254"=>"A-STUDY-SITE"
            ]);
        }
        function test_getAssociativeSiteList () {
            $site_list = Utility::getAssociativeSiteList();
            $this->assertEquals($site_list, [
                "1"=>"Data Coordinating Center",
                "254"=>"A-STUDY-SITE"
            ]);
            
            
            $site_list = Utility::getAssociativeSiteList(true, true);
            $this->assertEquals($site_list, [
                "1"=>"Data Coordinating Center",
                "254"=>"A-STUDY-SITE"
            ]);
            
            $site_list = Utility::getAssociativeSiteList(true, false);
            $this->assertEquals($site_list, [
                "254"=>"A-STUDY-SITE"
            ]);
            
            $site_list = Utility::getAssociativeSiteList(false, true);
            $this->assertEquals($site_list, [
                "1"=>"Data Coordinating Center",
                "255"=>"NOT-A-STUDY-SITE",
                "254"=>"A-STUDY-SITE"
            ]);
            $site_list = Utility::getAssociativeSiteList(false, false);
            $this->assertEquals($site_list, [
                "255"=>"NOT-A-STUDY-SITE",
                "254"=>"A-STUDY-SITE"
            ]);
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
            $this->assertEquals($visit_list, [
                "abc0"=>"Abc0",
                "abc1"=>"Abc1",
                "abc2"=>"Abc2",
                "Abc3"=>"Abc3",
                null=>null
            ]);
            
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
            Database::singleton()->prepare("INSERT INTO Project () VALUES ()");
            
            $this->assertTableCount("Project", 5);
            
            $project_list = Utility::getProjectList();
            $this->assertEquals($project_list, [
                1=>"THE FIRST",
                2=>"THE SECOND",
                3=>"DUPLICATE PROJECT NAME",
                4=>"DUPLICATE PROJECT NAME",
                5=>null,
            ]);
            
            $this->ensureDeleteAll("Project");
        }
    }
?>