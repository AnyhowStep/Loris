<?php
    require_once(__DIR__ . "/../../../../vendor/autoload.php");
    use PHPUnit\Framework\TestCase;
    
    class UtilityTest extends TestCase {
        public function testHelloWorld () {
            $this->assertEquals("Hello, world!", "Hello, " . "world!");
        }
    }
?>