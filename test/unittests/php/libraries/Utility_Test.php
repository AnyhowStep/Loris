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
        public static function tearDownAfterClass () {
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

        private function setUpDummySites () {
            //Should only have DCC
            $this->assertTableCount("psc", 1);

            Database::singleton()->insert("psc", [
                "CenterID"=>254,
                "Name"=>"A-STUDY-SITE",
                "Study_site"=>"Y"
            ]);
            Database::singleton()->insert("psc", [
                "CenterID"=>255,
                "Name"=>"NOT-A-STUDY-SITE",
                "Study_site"=>"N"
            ]);
            //As of this writing, NULL for Study_site is valid...
            Database::singleton()->insert("psc", [
                "CenterID"=>253,
                "Name"=>"NULL-STUDY-SITE",
                "Study_site"=>null
            ]);

            $this->assertTableCount("psc", 4);
        }
        private function tearDownDummySites () {
            $this->assertTableCount("psc", 4);

            Database::singleton()->delete("psc", [
                "CenterID"=>253
            ]);
            Database::singleton()->delete("psc", [
                "CenterID"=>254
            ]);
            Database::singleton()->delete("psc", [
                "CenterID"=>255
            ]);

            $this->assertTableCount("psc", 1);
        }
        function test_getSiteList () {
            $this->setUpDummySites();

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
                "254"=>"A-STUDY-SITE",
                "253"=>"NULL-STUDY-SITE"
            ], $site_list);

            $this->tearDownDummySites();
        }
        function test_getAssociativeSiteList () {
            $this->setUpDummySites();

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
                "254"=>"A-STUDY-SITE",
                "253"=>"NULL-STUDY-SITE"
            ], $site_list);
            $site_list = Utility::getAssociativeSiteList(false, false);
            $this->assertEquals([
                "255"=>"NOT-A-STUDY-SITE",
                "254"=>"A-STUDY-SITE",
                "253"=>"NULL-STUDY-SITE"
            ], $site_list);

            $this->tearDownDummySites();
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
            $this->assertTableCount("subproject", 2);

            $subproject_list = Utility::getSubprojectList();
            $this->assertEquals([
                1=>"Control",
                2=>"Experimental"
            ], $subproject_list);

            $this->assertTableCount("Project", 0);

            Database::singleton()->insert("Project", [
                "ProjectID"=>9001,
                "Name"=>"PROJECT 9001"
            ]);

            $this->assertTableCount("Project", 1);

            $this->assertTableCount("Project_rel", 0);

            Database::singleton()->insert("project_rel", [
                "ProjectID"=>9001,
                "SubprojectID"=>2
            ]);

            $this->assertTableCount("project_rel", 1);

            $subproject_list = Utility::getSubprojectList(9001);
            $this->assertEquals([
                2=>"Experimental"
            ], $subproject_list);

            $this->ensureDeleteAll("Project");
            $this->ensureDeleteAll("project_rel");
        }
        //As of this writing, getSubprojectsForProject() is a wrapper for getSubprojectList()
        //Therefore, the tests are duplicated with getSubprojectsForProject() being called instead
        function test_getSubprojectsForProject () {
            $this->assertTableCount("subproject", 2);

            $subproject_list = Utility::getSubprojectsForProject();
            $this->assertEquals([
                1=>"Control",
                2=>"Experimental"
            ], $subproject_list);

            $this->assertTableCount("Project", 0);

            Database::singleton()->insert("Project", [
                "ProjectID"=>9001,
                "Name"=>"PROJECT 9001"
            ]);

            $this->assertTableCount("Project", 1);

            $this->assertTableCount("Project_rel", 0);

            Database::singleton()->insert("Project_rel", [
                "ProjectID"=>9001,
                "SubprojectID"=>2
            ]);

            $this->assertTableCount("Project_rel", 1);

            $subproject_list = Utility::getSubprojectsForProject(9001);
            $this->assertEquals([
                2=>"Experimental"
            ], $subproject_list);

            $this->ensureDeleteAll("Project");
            $this->ensureDeleteAll("Project_rel");
        }
        function test_getTestNameByCommentID () {
            $this->assertTableCount("flag", 0);
        }
    }
?>
