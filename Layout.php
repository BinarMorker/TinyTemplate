<?php

namespace TinyTemplate;

class Layout extends Template {
    
    protected $view = null;
    
    public function __construct($file) {
        parent::__construct($file);
    }
    
    public function set_view($view) {
        $this->view = $view;
    }
    
    public function process($rules, $data) {
        $this->rules = $rules;
        $this->rules[] = new Rule(
            'yield_content',
            '~\{content\}~', 
            '<?php echo $this->view ?>'
        );
        return parent::process($this->rules, $data);
    }
    
}