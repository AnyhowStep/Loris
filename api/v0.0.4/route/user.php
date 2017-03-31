<?php
    namespace API;
    App::$Instance->post("/users/log_in", function ($request, $response, $args) {
        $body = $request->getParsedBody();
        $username = $body["username"];
        $password = $body["password"];
        
        if (Me::PasswordAuthenticate($username, $password)) {
            return $response->withJson(array(
                "token"=>Me::GetEncodedToken()
            ));
        } else {
            return $response->withJson(array(
                "error"=>empty(Me::$LogInHelper->_lastError) ?
                    "Unknown error" :
                    Me::$LogInHelper->_lastError
            ), 401);
        }
    })->add(new RequiredBodyRegex(
        "username:.+",
        "password:.+"
    ));
?>