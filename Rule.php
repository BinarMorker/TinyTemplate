<?php
/**
 * @author François Allard <binarmorker@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace TinyTemplate;

/**
 * A Rule can be applied to a template to process its content in different ways.
 */
class Rule {
    
    /**
     * @var string The Rule's name, for referencing in case of errors.
     */
    private $name;
    
    /**
     * @var string The actual regex rule to be applied to the template.
     */
    private $rule;
    
    /**
     * @var string The replacement for the rule defined above.
     */
    private $replacement;
    
    /**
     * Sets the Rule's values.
     * @param $name string The Rule's name.
     * @param $rule string The Rule's regex.
     * @param $replacement string The Rule's replacement.
     */
    public function __construct($name, $rule, $replacement) {
        $this->name = $name;
        $this->rule = $rule;
        $this->replacement = $replacement;
    }
    
    /**
     * Returns the Rule's name.
     * @returns string
     */
    public function name() {
        return $this->name;
    }
    
    /**
     * Returns the Rule's regex.
     * @returns string
     */
    public function rule() {
        return $this->rule;
    }
    
    /**
     * Returns the Rule's replacement.
     * @returns string
     */
    public function replacement() {
        return $this->replacement;
    }
    
}