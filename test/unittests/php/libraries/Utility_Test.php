<?php
    require_once __DIR__ . '/../../../../vendor/autoload.php';

    class Utility_Test extends PHPUnit_Framework_TestCase {
        function testAssert () {
            $this->assertEquals(true, false);
        }
    }
?>
