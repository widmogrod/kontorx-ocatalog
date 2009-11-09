<?php
require_once 'Zend/View/Helper/Abstract.php';
class Catalog_View_Helper_LogoFromRowset extends Zend_View_Helper_Abstract {

	public function logoFromRowset($imageId, $imageRowset) {
		if (!is_array($imageRowset)) {
			return null;
		} else
		if (is_object($imageRowset)) {
			if (method_exists($imageRowset, 'toArray')) {
				$imageRowset = $imageRowset->toArray();
			} else {
				$imageRowset = get_object_vars($imageRowset);
			}
		}

		$result = null;
		foreach ($imageRowset as $image) {
			if ($image['id'] == $imageId) {
				$result = $image['image'];
				break;
			}
		}
		return (string) $result;
	}

	public function direct() {
		return $this;
	}
}
