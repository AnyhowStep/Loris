<?php
    $api_version = $_GET["api-version"];
    //We'll verify that $api_version actually confirms to the regex, can't risk a directory traversal attack...
    if (!preg_match("/^v[0-9.]+$/AD", $api_version)) {
        //Why are you trying to hack us?
        //We're just an innocent loris =(
        http_response_code(404);
        header("Content-Type:application/json");
        die(json_encode(array(
            "error"=>"Invalid version $api_version"
        )));
    }
    //Modify the request URI path to remove the "/api/vXX.XX.XX" bits, they'll interfere with routing
    $request_uri_paths = explode("/", $_SERVER["REQUEST_URI"]);
    $request_uri_paths = array_slice($request_uri_paths, 3);
    $request_uri_paths = "/" . implode("/", $request_uri_paths);
    
    $_SERVER["REQUEST_URI"] = $request_uri_paths; //Feed it back into $_SERVER
    
    include_once(__DIR__ . "/../api/$api_version/index.php");
?>