<?php
class Catalog_View_Helper_Text2html extends Zend_View_Helper_Abstract {

	public function text2html($text) {
		// tworzy listę <ul><li>
		$text = preg_replace(
			'#\-\s*([^\n\r]+)[\n\r]+#i','<li>$1</li>',
			$text);
		$text = preg_replace(
			'#(<li>(.*)</li>)#i','<ul>$1</ul>', $text);
		
		print $text;
	}
}