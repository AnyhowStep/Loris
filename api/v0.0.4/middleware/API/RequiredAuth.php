<?php
    namespace API;
    class RequiredAuth {
        public function __construct () {}
        /*  I think this is ugly:
                App::$Instance->get(
                    //blah
                )->add((new RequiredAuth())
                    ->check("perm1")
                    ->check("perm2")
                    ->check("perm3")
                );
                
            I think this is better and more readable:
                App::$Instance->get(
                    //blah
                )->add(RequiredAuth::Create()
                    ->check("perm1")
                    ->check("perm2")
                    ->check("perm3")
                );
        */
        public static function Create () {
            return new RequiredAuth();
        }
        private $check_permission_arr = array();
        public function check ($permission_code) {
            $this->check_permission_arr[] = $permission_code;
            return $this;
        }
        public function __invoke ($request, $response, $next) {
            if (Me::IsLoggedIn()) {
                foreach ($this->check_permission_arr as $permission_code) {
                    if (!Me::HasPermission($permission_code)) {
                        throw new \Exception("You do not have the permission '$permission_code'", 401);
                    }
                }
                
                return $next($request, $response);
            } else {
                throw new \Exception("Please log in", 401);
            }
        }
    }
?>