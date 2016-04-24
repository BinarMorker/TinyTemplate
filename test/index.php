<?php
require_once "../Engine.php";
require_once "../Rule.php";
require_once "../Template.php";
use TinyTemplate\Engine;
use TinyTemplate\Rule;
use TinyTemplate\Template;

class Application {
    public static function run() {
        $time = microtime(true);
        Engine::instance()->add_rule(new Rule(
                'translate', 
                '~\{translate:(\w+),(\w+)\}~', 
                '<?php \\Application::translate(\'$1\', \'$2\'); ?>'));
        Engine::instance()->add_data(array(
            "working" => "&#10003;",
            "varif" => true,
            "varnot" => false,
            "varlist" => array(
                array(
                    "item" => 1
                ),
                array(
                    "item" => 2
                ),
                array(
                    "item" => 3
                ),
                array(
                    "item" => 4
                ),
                array(
                    "item" => 5
                )
            )
        ));
        echo Engine::instance()->process(
            new Template("test.htm"));
        echo "Generated in " . (microtime(true) - $time) . " milliseconds.";
    }
    
    public static function translate($key, $value) {
        echo $key . "_" . $value;
    }
}

Application::run();
?>