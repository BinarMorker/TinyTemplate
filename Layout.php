<?php
/**
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace TinyTemplate;

class Layout extends Template {
	
	public function process (array $rules, array $data) {
		$array[] = new Rule(
				'yield',
				'~\{yield\}~',
				'<?php $end = end($this->data); echo $end; ?>'
		);
		
		$arr_rules = $array + $rules;
		
		return parent::process($arr_rules, $data);
	}
	
}