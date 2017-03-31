<?php
    namespace API;
    class NDBClientWrapper {
        public static $Instance;
        
        //Using Init() instead of __init__() because I *want* index.php
        //to call this *before* anything else gets the chance to execute
        public static function Init () {
            $client = new \NDB_Client();
            $client->makeCommandLine();
            $client->initialize(__DIR__ . "/../../../project/config.xml");
            self::$Instance = $client;
        }
    }
?>