<?php
/**
 * @author François Allard <binarmorker@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace TinyTemplate;

/**
 * A singleton Engine class to process templates and pass them data and rules.
 */
class Engine {
    
    /**
     * @var Engine The reference to the instance of this class.
     */
    private static $instance;
    
    /**
     * @var array All the rules to be applied to templates.
     */
    private $rules = array();
    
    /**
     * @var array Data to be passed to the templates.
     */
    private $data = array();
    
    /**
     * Protected constructor to prevent creating a new instance of the class.
     */
    protected function __construct() {
        
    }
    
    /**
     * Private clone method to prevent cloning of the instance.
     * @return void
     */
    protected function __clone() {
        
    }
    
    /**
     * Private unserialize method to prevent unserializing.
     * @return void
     */
    protected function __wakeup() {
        
    }
    
    /**
     * Returns the instance of this class.
     * @return Engine
     */
    public static function instance() {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }
    
    /**
     * Adds a rule to the array of rules.
     * @param $rule Rule The new rule to add.
     * @return void
     */
    public function add_rule(Rule $rule) {
        $this->rules[] = $rule;
    }
    
    /**
     * Adds an array of data to the existing data.
     * @param $data array The data array to add.
     * @return void
     */
    public function add_data(array $data) {
        $this->data = array_merge($this->data, $data);
    }
    
    /**
     * Returns the processed template from a template instance.
     * @param $template Template The template instance.
     * @return string
     */
    public function process(Template $template, Layout $layout = null) {
        $content = $template->process($this->rules, $this->data);
        
        if (!is_null($layout)) {
        	$this->data[] = $content;
        	$content = $layout->process($this->rules, $this->data);
        }
        
        return $content;
    }
    
}
