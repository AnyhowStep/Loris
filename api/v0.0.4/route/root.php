<?php
    namespace API;
    App::$Instance->get("/", function ($request, $response, $args) {
        return $response->withJson(array(
            "success"=>true,
            "test"=>"hello"
        ), 200, JSON_PRETTY_PRINT);
    });
?>