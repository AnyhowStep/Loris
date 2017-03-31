<?php
    namespace API;
    //Convenience class, wraps a given $internal_db
    class DB {
        public static $Instance;
        public static function __init__ () {
            //If static ctor is called, $Instance wraps NDB_Factory's database
            $factory = \NDB_Factory::singleton();
            self::$Instance = new DB($factory->database());
        }
        
        private $internal_db;
        public function __construct ($internal_db) {
            $this->internal_db = $internal_db;
        }
        //Calls the internal_db's method if this class does not have the method
        public function __call ($name, $arguments) {
            return call_user_func_array(
                array(
                    $this->internal_db,
                    $name
                ),
                $arguments
            );
        }
        
        //Like Database.class.inc pselectOne()...
        //Except it returns null if there are no results
        public function getOrNull ($query, $params=array()) {
            $mixed = $this->pselectOne($query, $params);
            if (is_array($mixed)) {
                return null;
            }
            return $mixed;
        }
        //Like Database.class.inc pselectRow()...
        //Except it returns null if there are no results
        public function fetchOrNull ($query, $params=array()) {
            $row = $this->pselectRow($query, $params);
            if (empty($row)) {
                return null;
            }
            return $row;
        }
    }
?>