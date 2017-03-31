<?php
    namespace API;
    class App {
        public static $Instance;
        public static function __init__ () {
            $container = new \Slim\Container(array(
                "settings"=>array(
                    "debug"=>true,
                    "displayErrorDetails"=>true,
                ),
                "notFoundHandler"=>function ($c) {
                    return function ($request, $response) {
                        return $response
                                ->withJson(array(
                                    "error"=>"not found"
                                ), 404);
                    };
                },
                "errorHandler"=>function ($c) {
                    return function ($request, $response, $ex) use($c) {
                        error_log($ex->getMessage());
                        $status = $ex->getCode();
                        if ($status == 0) {
                            //Assume it's an error with the server
                            $status = 500;
                        }
                        return $c["response"]
                            ->withJson(array(
                                "error"=>$ex->getMessage()
                            ), $status);
                    };
                },
                "notAllowedHandler"=>function ($c) {
                    return function ($request, $response, $methods) use($c) {
                        return $c["response"]
                            ->withHeader("Allow", implode(",", $methods))
                            ->withJson(array(
                                "error"=>"Method not allowed. Allowed are " . implode(", ", $methods)
                            ), 405);
                    };
                }
            ));
            $app = new \Slim\App($container);
            $app->add(function ($request, $response, $next) {
                //I don't quite agree with simply allowing anything to access the API on a browser but
                //your will be done?
                //Feel free to change this to be less/more restrictive as needed.
                $response = $response
                    ->withHeader("Access-Control-Allow-Origin", "*")
                    ->withHeader("Access-Control-Allow-Headers", "Content-Type");
                return $next($request, $response);
            });
            $app->add(function ($request, $response, $next) use ($app) {
                $response = $next($request, $response);
                if ($response->getStatusCode() == 404 && $response->getBody()->getSize() == 0) {
                    //We've received a 404 response with no error message, go to the default implementation.
                    $handler = $app->getContainer()["notFoundHandler"];
                    return $handler($request, $response);
                }
                return $response;
            });
            self::$Instance = $app;
        }
    }
?>