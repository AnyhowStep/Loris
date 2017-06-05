<?php
    require_once __DIR__ . '/../../../../vendor/autoload.php';

    class Utility_Test extends PHPUnit_Framework_TestCase {
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
            NDB_Factory::singleton()->settings("../../../config.xml");
            
            Database::singleton(
                $this->factory->settings()->dbName(),
                $this->factory->settings()->dbUserName(),
                $this->factory->settings()->dbPassword(),
                $this->factory->settings()->dbHost()
            );
            
            $site_list = Utility::getSiteList();
            $this->assertEquals($site_list, []);
            $site_list = Utility::getSiteList(true);
            $this->assertEquals($site_list, []);
            $site_list = Utility::getSiteList(false);
            $this->assertEquals($site_list, []);
        }
    }
?>