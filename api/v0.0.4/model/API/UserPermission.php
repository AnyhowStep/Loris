<?php
    namespace API;
    
    class UserPermission {
        public static function FetchAll ($user_id) {
            return DB::$Instance->pselect("
                SELECT
                    p.*
                FROM
                    user_perm_rel up
                JOIN
                    permissions p
                ON
                    p.permID = up.permID
                WHERE
                    userID = :user_id
            ", array(
                "user_id"=>$user_id
            ));
        }
    }
?>