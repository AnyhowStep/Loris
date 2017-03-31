<?php
    namespace API;
    /*  CheckETag
        Enables auto etag checking.
        
        Example Usage:
            //SomeModel must implement the public static function "CalculateETag".
            App::$Instance->get(
                //code
            )->add(new CheckEtag(SomeModel::class, "id"));
    */
    class CheckETag {
        private $class_name;
        private $args;
        public function __construct ($class_name, ...$args) {
            $this->class_name = $class_name;
            $this->args = $args;
        }
        
        public function __invoke ($request, $response, $next) {
            $route = $request->getAttribute("route");
            
            $etag_args = array();
            foreach ($this->args as $a) {
                $val = $route->getArgument($a);
                if (is_null($val)) {
                    throw new \Exception("Missing route argument '$val'");
                }
                $etag_args[] = $val;
            }
            
            if (!method_exists($this->class_name, "CalculateETag")) {
                throw new \Exception("{$this->class_name} does not implement CalculateETag()");
            }
            $etag = call_user_func_array(array($this->class_name, "CalculateETag"), $etag_args);
            $if_none_match_arr = $request->getHeader("If-None-Match");
            if (count($if_none_match_arr) == 0 || $etag !== $if_none_match_arr[0]) {
                //Client did not cache data or cached data is outdated
                $response = $next($request, $response);
                return $response->withHeader("ETag", $etag);
            } else {
                //Client cached data and is up-to-date
                return $response->withStatus(304); //Not modified
            }
        }
    }
?>