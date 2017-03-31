<?php
    namespace API;
    /*  RequiredBodyRegex
        For when you just want to make sure user input matches some regular expression.
        
        Usage:
        new RequiredBodyRegex(
            "param1:\d+", //For numbers
            "param2:[a-zA-Z0-9]{1,32}" //For alphanumeric, 1-32 characters long
        )
        
        The format for each argument is:
            <parameter-name>:<regular expression without anchors (they're automatically added)>
    */
    class RequiredBodyRegex {
        private $args;
        public function __construct (...$args) {
            $this->args = $args;
        }
        public function __invoke ($request, $response, $next) {
            $body = $request->getParsedBody();
            foreach ($this->args as $raw) {
                $tokens = explode(":", $raw);
                $key = $tokens[0];
                $regex = "/^" . $tokens[1] . "$/AD";
                $val = isset($body[$key]) ? $body[$key] : null;
                if (is_null($val)) {
                    throw new \Exception("$key is required", 400);
                }
                if (!is_string($val) || !preg_match($regex, $val)) {
                    throw new \Exception("The value for $key is incorrect", 400);
                }
            }
            return $next($request, $response);
        }
    }
?>