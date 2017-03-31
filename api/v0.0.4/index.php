<?php
    namespace API;
    //*Try not to enable these in a production environment, I guess?
    error_reporting(-1);
    ini_set("display_errors", TRUE);
    ini_set("display_startup_errors", TRUE);
    //*/
    //Composer autoload
    require_once(__DIR__ . "/../../vendor/autoload.php");
    
    function include_once_if_exists ($file) {
        if (file_exists($file)) {
            include_once($file);
        }
    }
    spl_autoload_register(function ($class) {
        //This turns namespaces into folder names..
        //Example, $class = "MyNamespace\\SubNamespace\\SomeClass"
        //To load the directory, $class2 = "MyNamespace/SubNamespace/SomeClass"
        //Some day, I will not write hack-y code =(
        //Then again, the nature of PHP is hack-y, right?
        $class2 = str_replace("\\", "/", $class);
        
        include_once_if_exists(__DIR__ . "/model/{$class2}.php");
        include_once_if_exists(__DIR__ . "/middleware/{$class2}.php");
        include_once_if_exists(__DIR__ . "/{$class2}.php");
        
        //This is a hack to implement static constructors in PHP!
        //Please forgive me for the sin I have committed. -anyhowstep
        if (method_exists($class, "__init__")) {
            $class::__init__();
        }
    });
    
    //The API should really be a token-only thing, honestly.
    //RESTful APIs should *never* use COOKIES!
    
    //Honestly, I don't like the idea of instantiating the client and database here like this
    //but with so much legacy, and with me not being experienced enough with the LORIS code base,
    //I'm just going to do it. Forgive me.
    NDBClientWrapper::Init();
    
    
    //Add routes
    function include_once_whole_directory ($dir) {
        $arr = scandir($dir);
        for ($i=2; $i<count($arr); ++$i) {
            $a = $arr[$i];
            include_once_if_exists("$dir/$a");
        }
    }
    include_once_whole_directory(__DIR__ . "/route");
    
    App::$Instance->run();
?>