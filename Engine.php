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
        
        // PHP code exclusion
        // TODO: Add exclusions for CDATA, dhtml and other dynamic server-side scripting.
        $this->rules[] = new Rule(
            'php_exclude', 
            '~(<\?)~', 
            '<?php echo \'<?\'; ?>');
        
        // If conditionning
        $this->rules[] = new Rule(
            'if_boolean', 
            '~\{if:(\w+)\}~', 
            '<?php if ($this->data[\'$1\']): ?>');
        $this->rules[] = new Rule(
            'if_condition', 
            '~\{if:(\w+)([!<>=]+)(\w+)\}~', 
            '<?php if ($this->data[\'$1\']$2$this->data[\'$3\']): ?>');
        $this->rules[] = new Rule(
            'ifnot', 
            '~\{ifnot:(\w+)\}~', 
            '<?php if (!$this->data[\'$1\']): ?>');
        $this->rules[] = new Rule(
            'else', 
            '~\{else\}~', 
            '<?php else: ?>');
        $this->rules[] = new Rule(
            'elseif_boolean', 
            '~\{else:(\w+)\}~', 
            '<?php elseif ($this->data[\'$1\']): ?>');
        $this->rules[] = new Rule(
            'elseif_condition', 
            '~\{else:(\w+)([!<>=]+)(\w+)\}~', 
            '<?php elseif ($this->data[\'$1\']$2$this->data[\'$3\']): ?>');
        $this->rules[] = new Rule(
            'endif', 
            '~\{endif\}~', 
            '<?php endif; ?>');
        
        // Loops
        $this->rules[] = new Rule(
            'loop', 
            '~\{loop:(\w+)\}~', 
            '<?php foreach ($this->data[\'$1\'] as $element): $this->wrap($element); ?>');
        $this->rules[] = new Rule(
            'endloop', 
            '~\{endloop\}~', 
            '<?php $this->unwrap(); endforeach; ?>');
        
        // Importing
        // TODO: Allow content yielding for layout support
        $this->rules[] = new Rule(
            'import_view', 
            '~\{import:([.\w]+)\}~', 
            '<?php echo $this->importFile(\'$1\'); ?>');
        
        // Variables
        $this->rules[] = new Rule(
            'escape_var', 
            '~\{escape:(\w+)\}~', 
            '<?php $this->showVariable(\'$1\', true); ?>');
        $this->rules[] = new Rule(
            'variable', 
            '~\{(\w+)\}~', 
            '<?php $this->showVariable(\'$1\'); ?>');
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
        //$this->rules[] = $rule;
        
    	/* Attempt to fix execution of the variable rule before custom rules */
    	$array[] = $rule;
    	$this->rules = $array + $this->rules;
    }
    
    /**
     * Adds an array of data to the existing data.
     * @param $data array The data array to add.
     * @return void
     */
    public function add_data(array $data) {
        $this->data += $data;
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
