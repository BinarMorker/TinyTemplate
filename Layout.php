<?php
/**
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace TinyTemplate;

class Layout extends Template {
	
	public function process(array $custom_rules, array $data) {
		$custom_rules[] = new Rule(
				'yield',
				'~\{yield\}~',
				'<?php $end = end($this->data); echo $end; ?>'
		);
		
		return parent::process($custom_rules, $data);
	}
	
}