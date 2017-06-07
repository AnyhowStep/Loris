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
            $visit_list = Utility::getVisitList();
            $this->assertEquals($visit_list, []);
        }
        function test_getProjectList () {
            $project_list = Utility::getProjectList();
            $this->assertEquals($project_list, []);
        }
    }
?>