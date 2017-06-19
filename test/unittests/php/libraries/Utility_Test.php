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

            $this->assertTableCount("project_rel", 0);

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

            $this->assertTableCount("project_rel", 0);

            Database::singleton()->insert("project_rel", [
                "ProjectID"=>9001,
                "SubprojectID"=>2
            ]);

            $this->assertTableCount("project_rel", 1);

            $subproject_list = Utility::getSubprojectsForProject(9001);
            $this->assertEquals([
                2=>"Experimental"
            ], $subproject_list);

            $this->ensureDeleteAll("Project");
            $this->ensureDeleteAll("project_rel");
        }
        function test_getTestNameByCommentID () {
            $this->assertTableCount("users", 1);
            $this->assertTableCount("test_names", 1);
            $this->assertTableCount("psc", 1);
            $this->assertTableCount("candidate", 0);
            $this->assertTableCount("session", 0);
            $this->assertTableCount("flag", 0);

            Database::singleton()->insert("candidate", [
                "ID"=>1337,
                "CandID"=>69,
                "PSCID"=>"TEMPORARY-PSCID",
                "UserID"=>1,
                "CenterID"=>1
            ]);
            Database::singleton()->insert("session", [
                "ID"=>9999,
                "CandID"=>69,
                "CenterID"=>1,
                "MRIQCStatus"=>""
            ]);
            Database::singleton()->insert("flag", [
                "ID"=>80085,
                "SessionID"=>9999,
                "Test_name"=>"testtest",
                "CommentID"=>"TEST-COMMENT-ID"
            ]);
            $this->assertTableCount("users", 1);
            $this->assertTableCount("test_names", 1);
            $this->assertTableCount("psc", 1);
            $this->assertTableCount("candidate", 1);
            $this->assertTableCount("session", 1);
            $this->assertTableCount("flag", 1);

            $str = Utility::getTestNameByCommentID("TEST-COMMENT-ID");
            $this->assertEquals("testtest", $str);

            //Yes, I understand that the above is a string...
            //And that the below is an empty array...
            //But, such is the behaviour of the Loris code base
            $empty_array = Utility::getTestNameByCommentID("DOES-NOT-EXIST");
            $this->assertEquals([], $empty_array);

            Database::singleton()->delete("flag", [
                "ID"=>80085
            ]);
            Database::singleton()->delete("session", [
                "ID"=>9999
            ]);
            Database::singleton()->delete("candidate", [
                "ID"=>1337
            ]);

            $this->assertTableCount("users", 1);
            $this->assertTableCount("test_names", 1);
            $this->assertTableCount("psc", 1);
            $this->assertTableCount("candidate", 0);
            $this->assertTableCount("session", 0);
            $this->assertTableCount("flag", 0);
        }
        function test_toArray () {
            //The method is really badly named.
            //And I have no idea what would be a good name for it.
            //Its existence is a code smell

            //This is not an array, so, it will not be modified
            $arr = Utility::toArray(1);
            $this->assertEquals(1, $arr);

            $arr = Utility::toArray(null);
            $this->assertEquals(null, $arr);

            //This is an empty array, so, [0] does not exist
            //It will be "wrapped" into a singleton-array
            //Using singleton here to mean "a set of one element"
            $arr = Utility::toArray([]);
            $this->assertEquals([[]], $arr);

            //This is an associative array
            //It will become a singleton
            $arr = Utility::toArray([
                "Test" => 1337
            ]);
            $this->assertEquals([
                [
                    "Test" => 1337
                ]
            ], $arr);

            //This is, technically, associative, because it doesn't start from
            //zero
            $arr = Utility::toArray([
                1 => 1337
            ]);
            $this->assertEquals([
                [
                    1 => 1337
                ]
            ], $arr);

            //This is fine and will come out unmodified
            $arr = Utility::toArray([1337]);
            $this->assertEquals([1337], $arr);

            //This will come out unmodified simply because [0] is set
            //I know, unintuitive. But, such is the way it works right now
            $arr = Utility::toArray([
                0 => 1337,
                3 => 99
            ]);
            $this->assertEquals([
                0 => 1337,
                3 => 99
            ], $arr);
        }
        function test_asArray () {
            //The method is really badly named.
            //And I have no idea what would be a good name for it.
            //Its existence is a code smell
            //Maybe "singletonIfNotArray" or something like that.
            //I'm using the word singleton here to mean "a set of one element"

            $arr = Utility::asArray(1);
            $this->assertEquals([1], $arr);

            $arr = Utility::asArray(null);
            $this->assertEquals([null], $arr);

            $arr = Utility::asArray([]);
            $this->assertEquals([], $arr);

            $arr = Utility::asArray([
                "Test" => 1337
            ]);
            $this->assertEquals([
                "Test" => 1337
            ], $arr);

            $arr = Utility::asArray([
                1 => 1337
            ]);
            $this->assertEquals([
                1 => 1337
            ], $arr);

            $arr = Utility::asArray([1337]);
            $this->assertEquals([1337], $arr);

            $arr = Utility::asArray([
                0 => 1337,
                3 => 99
            ]);
            $this->assertEquals([
                0 => 1337,
                3 => 99
            ], $arr);
        }
        function test_nullifyEmpty () {
            $arr = [];
            $field = "";
            Utility::nullifyEmpty($arr, $field);
            $this->assertEquals([], $arr);

            $arr = [
                "test"=>999
            ];
            $field = "test";
            Utility::nullifyEmpty($arr, $field);
            $this->assertEquals([
                "test"=>999
            ], $arr);

            $arr = [
                "test"=>''
            ];
            $field = "test";
            Utility::nullifyEmpty($arr, $field);
            $this->assertEquals([
                "test"=>null
            ], $arr);

            $arr = [
                "test"=>[]
            ];
            $field = "test";
            Utility::nullifyEmpty($arr, $field);
            $this->assertEquals([
                "test"=>[]
            ], $arr);

            $arr = [
                "test"=>''
            ];
            $field = "qwerty";
            Utility::nullifyEmpty($arr, $field);
            $this->assertEquals([
                "test"=>''
            ], $arr);
        }
        function test_getAllInstruments () {
            $this->assertEquals([], Utility::getAllInstruments());
        }
    }
?>
