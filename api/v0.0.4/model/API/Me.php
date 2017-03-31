<?php
    namespace API;
    class Me {
        public static $LogInHelper;
        //Nicer wrapper. Just call Me::IsLoggedIn()!
        public static function IsLoggedIn () {
            return !is_null(self::$LogInHelper) && self::$LogInHelper->isLoggedIn();
        }
        public static function __init__ () {
            //Attempt to authenticate user
            if (self::IsLoggedIn()) {
                return;
            }
            
            $log_in_helper = new \SinglePointLogin();
            self::$LogInHelper = $log_in_helper; //Set the static variable, used in IsLoggedIn()
            
            //It looks like authenticate() always returns a bool
            //but I see code in NDB_Client that does strict checking so I'm paranoid
            //and will also do a strict check...
            //If I need the result of authenticate() in future
            $log_in_helper->authenticate();
            
            //Do I need more processing here? Maybe in future?
        }
        
        public static function GetEncodedToken () {
            if (!self::IsLoggedIn()) {
                throw new \Exception("Log in before creating tokens");
            }
            
            return \SinglePointLogin::CreateJWT(array(
                // JWT related tokens to for the JWT library to validate
                "iss"  => $baseURL,
                "aud"  => $baseURL,
                // Issued at
                "iat"  => time(),
                "nbf"  => time(),
                // Expire in 1 day
                "exp"  => time() + 86400,
                // Additional payload data
                "id"   => self::$LogInHelper->getId(),
                "username" => self::$LogInHelper->getUsername(),
            ));
        }
        public static function PasswordAuthenticate ($username, $password) {
            return self::$LogInHelper->passwordAuthenticate($username, $password, false);
        }
        public static function GetID () {
            return self::$LogInHelper->getId();
        }
        public static function GetUsername () {
            return self::$LogInHelper->getUsername();
        }
        
        //Permissions
        private static $PermissionArr = null;
        public static function GetOrFetchPermissions () {
            if (!self::IsLoggedIn()) {
                return null;
            }
            if (is_null(self::$PermissionArr)) {
                self::$PermissionArr = UserPermission::FetchAll(self::GetID());
            }
            return self::$PermissionArr;
        }
        public static function HasPermission ($permission_code) {
            if (!self::IsLoggedIn()) {
                return false;
            }
            $permission_arr = self::GetOrFetchPermissions();
            //Inefficient but works. Blah.
            foreach ($permission_arr as $row) {
                if ($row["code"] === $permission_code) {
                    return true;
                }
            }
            return false;
        }
    }
?>