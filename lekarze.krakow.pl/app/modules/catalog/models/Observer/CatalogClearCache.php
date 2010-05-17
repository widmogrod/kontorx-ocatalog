<?php
class Catalog_Model_Observer_CatalogClearCache extends Promotor_Observable_Observer_Abstract {
	public function update(Promotor_Observable_List $list) {
		$model = new Catalog_Model_Catalog();
		try {
			$result = $model->getResultCache()->clean(
				Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
				array(get_class($model))
			);

			$status = $result ? self::SUCCESS : self::FAILURE;
			$this->_setStatus($status);
			$this->_addMessage($status);
		} catch (Zend_Cache_Exception $e) {
			$this->_addException($e);
			$this->_addMessage(self::FAILURE);
			$this->_setStatus(self::FAILURE);
		}
		
		if (!Zend_Registry::isRegistered('Zend_Cache_Hour')) {
			return;
		}
		
//		/* @var Zend_Cache_Core */
//		$cache = Zend_Registry::get('Zend_Cache_Hour');
//		if (!$cache instanceof Zend_Cache_Core) {
//			return;
//		}

		try {
			$result = $cache->clean(
				Zend_Cache::CLEANING_MODE_ALL
			);

			$status = $result ? self::SUCCESS : self::FAILURE;
			$this->_setStatus($status);
			$this->_addMessage($status);
		} catch (Zend_Cache_Exception $e) {
			$this->_addException($e);
			$this->_addMessage(self::FAILURE);
			$this->_setStatus(self::FAILURE);
		}
	}
}