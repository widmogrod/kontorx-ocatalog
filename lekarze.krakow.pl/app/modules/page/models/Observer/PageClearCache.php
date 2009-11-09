<?php
class Page_Model_Observer_PageClearCache extends Promotor_Observable_Observer_Abstract {
	public function update(Promotor_Observable_List $list) {
		$model = new Page_Model_Page();
		try {
			$result = $model->getResultCache()->clean(
				Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
				array(get_class($model))
			);
			$status = $result ? self::SUCCESS : self::FAILURE;
			$this->_setStatus($status);
		} catch (Zend_Cache_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
		}
	}
}