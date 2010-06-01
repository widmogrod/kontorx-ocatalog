<?php
/**
 * @author gabriel
 *
 */
class Catalog_View_Helper_Text2html extends Zend_View_Helper_Abstract {

	/**
	 * @param string $text
	 * @return string
	 * 
	 * @todo A co jesli w tekście są inne myślniki?
	 */
	public function text2html($text) {
		// tworzy listę <ul><li>
		$text = preg_replace(
			'#\n*\-\s*([^\n]+)#i','<li>$1</li>',
			$text);
		$text = preg_replace(
			'#(<li>(.*)</li>)#i','<ul>$1</ul>', $text);
		
		print $text;
	}
}