<?php
    require_once __DIR__ . '/../../../../vendor/autoload.php';

    class Utility_Test extends PHPUnit_Framework_TestCase {
    
        function test_calculateAge () {
            $age = Utility::calculateAge("2017-02-28", "2017-03-01");
            $this->assertEquals($age, [
                "year"=>0,
                "mon"=>0,
                "day"=>1
            ]);
        }
    }
?>