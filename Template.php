<?php
/**
 * @author François Allard <binarmorker@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace TinyTemplate;

/**
 * A Template is a view that can be processed through the Engine.
 */
class Template {
    
    /**
     * @var string The Template's contents.
     */
    private $template = "";
    
    /**
     * @var array[object|array] The loop stack.
     */
    private $stack = array();
    
    /**
     * @var array[mixed] The data to be passed to the Template.
     */
    private $data = array();
    
    /**
     * @var array[Rule] The rules to be applied to the Template.
     */
    private $rules = array();
    
    /**
     * @var Layout The layout for the template
     */
    private $layout;
    
    /**
     * Creates the Template from a filename.
     * @param $file string The filename and path of the Template.
     * @param $layout string The filename and path of the Layout.
     * @throws InvalidArgumentException If the file cannot be found.
     */
    public function __construct($file) {
        $this->template = @file_get_contents($file, true);
        
        if ($this->template === false) {
            throw new \InvalidArgumentException('File not found');
        }
    }
    
    /**
     * Imports a file in the form of a new Template.
     * @var $file string The filename of the imported Template.
     * @returns string
     */
    private function importFile($file) {
        $template = new Template($file);
        return $template->process($this->rules, $this->data);
    }
    
    /**
     * Shows the content of a variable stored in the data.
     * @var $name string The variable name in the data array.
     * @var $sanitize boolean If the variable should be escaped before being returned.
     * @returns mixed
     */
	private function showVariable($name, $sanitize = false) {
        if (isset($this->data[$name])) {
            if ($sanitize) {
                echo htmlentities($this->data[$name]);
            } else {
                echo $this->data[$name]; 
            }
        } else {
            echo '{' . $name . '}';
        }
    }
    
    /**
     * Wraps the content of the loop into the data array so it can be used
     * @var $element object|array The element that will be looped into.
     */
    private function wrap($element) {
        $this->stack[] = $this->data;
        foreach ($element as $k => $v) {
            $this->data[$k] = $v;
        }
    }
    
    /**
     * Removes the loop variables from inside the data so we cannot use it afterwards.
     */
    private function unwrap() {
        $this->data = array_pop($this->stack);
    }
    
    /**
     * Process the Template and convert its variables into values.
     * @var $rules array[Rule] The rules to be applied to the Template.
     * @var $data array The data to be passed to the Template.
     * @returns string
     */
    public function process(array $rules, array $data) {
        // PHP code exclusion
        // TODO: Add exclusions for CDATA, dhtml and other dynamic server-side scripting.
        $this->rules[] = new Rule(
            'php_exclude', 
            '~(<\?)~', 
            '<?php echo \'<?\'; ?>'
        );
        
        // If conditionning
        $this->rules[] = new Rule(
            'if_boolean', 
            '~\{if:(\w+)\}~', 
            '<?php if (isset($this->data[\'$1\']) && $this->data[\'$1\']): ?>'
        );
        $this->rules[] = new Rule(
        		'if_condition',
        		'~\{if:(\w+)([!<>=]+)(\w+)\}~',
        		'<?php if (isset($this->data[\'$1\'])) {$base = $this->data[\'$1\'];}else{$base = \'$1\';};
        		if (isset($this->data[\'$3\'])) {$value = $this->data[\'$3\'];}else{$value = \'$3\';}?>
        		<?php if ($base $2 $value) : ?>'
        );
        $this->rules[] = new Rule(
            'ifnot', 
            '~\{ifnot:(\w+)\}~', 
            '<?php if (!$this->data[\'$1\']): ?>'
        );
        $this->rules[] = new Rule(
            'else', 
            '~\{else\}~', 
            '<?php else: ?>'
        );
        $this->rules[] = new Rule(
            'elseif_boolean', 
            '~\{else:(\w+)\}~', 
            '<?php elseif (isset($this->data[\'$1\']) && $this->data[\'$1\']): ?>'
        );
        $this->rules[] = new Rule(
            'elseif_condition', 
            '~\{else:(\w+)([!<>=]+)(\w+)\}~', 
            '<?php elseif (((isset($this->data[\'$1\']) && isset($this->data[\'$3\'])) && $this->data[\'$1\'] $2 $this->data[\'$3\']) ||
        		((isset($this->data[\'$1\']) && !isset($this->data[\'$3\'])) && $this->data[\'$1\'] $2 \'$3\') ||
        		((!isset($this->data[\'$1\']) && isset($this->data[\'$3\'])) && \'$1\' $2 $this->data[\'$3\']) ||
        		((!isset($this->data[\'$1\']) && !isset($this->data[\'$3\'])) && \'$1\' $2 \'$3\')): ?>'
        );
        $this->rules[] = new Rule(
            'endif', 
            '~\{endif\}~', 
            '<?php endif; ?>'
        );
        
        // Loops
        $this->rules[] = new Rule(
            'loop', 
            '~\{loop:(\w+)\}~', 
            '<?php foreach ($this->data[\'$1\'] as $element): $this->wrap($element); ?>'
        );
        $this->rules[] = new Rule(
            'endloop', 
            '~\{endloop\}~', 
            '<?php $this->unwrap(); endforeach; ?>'
        );
        
        // Importing
        $this->rules[] = new Rule(
            'import_view', 
            '~\{import:(([^\/\s]+\/)?(.*))\}~', 
            '<?php echo $this->importFile(\'$1\'); ?>'
        );
        
        // Clean variables
        $this->rules[] = new Rule(
            'escape_var', 
            '~\{escape:(\w+)\}~', 
            '<?php $this->showVariable(\'$1\', true); ?>'
        );
        
        // Custom rules
        $this->rules = array_merge($this->rules, $rules);
        
        // Variables
        $this->rules[] = new Rule(
            'variable', 
            '~\{(\w+)\}~', 
            '<?php $this->showVariable(\'$1\'); ?>'
        );
        
        // Arrays
        $this->rules[] = new Rule(
        		'variable_array',
        		'~\{(\w+)\[(\w+)\]\}~',
        		'<?php echo (isset($this->data[\'$1\'][\'$2\'])) ? $this->data[\'$1\'][\'$2\'] : "{$1[$2]}"; ?>'
        );
        
        $this->rules[] = new Rule(
        		'variable_array_escape',
        		'~\{escape:(\w+)\[(\w+)\]\}~',
        		'<?php echo htmlentities($this->showVariable(\'$1\')[\'$2\']); ?>'
        );
        
        $this->data = $data;
        $this->stack = array();
        
        foreach ($this->rules as $rule) {
            $this->template = preg_replace($rule->rule(), $rule->replacement(), $this->template);
        }
        
        $this->template = '?>' . $this->template;
        
        ob_start();
        eval($this->template);
        return ob_get_clean();
    }
}