<?php
require_once "../Engine.php";
require_once "../Rule.php";
require_once "../Template.php";
require_once "../Layout.php";
use TinyTemplate\Engine;
use TinyTemplate\Rule;
use TinyTemplate\Template;
use TinyTemplate\Layout;

class Application {
    public static function run() {
        $time = microtime(true);
        
        Engine::instance()->add_data(array(
            "working" => "&#10003;"
        ));
        
        echo Engine::instance()->process(
            new Template("layout2.htm"), new Layout("layout.htm"));
        
        echo "Generated in " . (microtime(true) - $time) . " milliseconds.";
    }
}

Application::run();
?>