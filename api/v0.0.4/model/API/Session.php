<?php
    namespace API;
    class Session {
        public static function FetchAllVisitLabel ($candidate_id) {
            //This implementation is only because of legacy. This is not efficient, by any means.
            //This is here to make the code *outside* of this class more readable.
            //I apologize.
            //I just really think each database table should have their own model class.
            //It's cleaner this way -anyhowstep
            $item = Candidate::Fetch($candidate_id);
            if (is_null($item)) {
                return null;
            }
            //Jesus, this returns a kvp kinda' thing
            //Key is session id
            //Value is visit label
            $arr = $item->getListOfVisitLabels();
            //Convert it to an array of rows,
            //Each row has the "ID" and "Visit_label" columns
            //Honestly, I hate how the database does not have a naming convention for *anything*.
            //Tables, columns, values, etc.
            //But such is life. I'm sorry the names suck =(
            //-anyhowstep
            $result = array();
            foreach ($arr as $key=>$val) {
                $result[] = (object)array(
                    "ID"=>$key,
                    "Visit_label"=>$val
                );
            }
            return $result;
        }
        public static function Fetch ($session_id) {
            //Again, using legacy
            return \NDB_Factory::singleton()->timepoint($session_id);
        }
        public static function GetIDFromCandidateVisitLabel ($candidate_id, $visit_label) {
            return DB::$Instance->getOrNull("
                SELECT
                    ID
                FROM
                    session
                WHERE
                    CandID = :candidate_id AND
                    Visit_label = :visit_label
            ", array(
                "candidate_id"=>$candidate_id,
                "visit_label"=>$visit_label
            ));
        }
    }
?>