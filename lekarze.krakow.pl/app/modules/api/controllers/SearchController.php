<?php
class Api_SearchController extends Zend_Controller_Action
{
	public function init()
	{
		// wyłącz widoki
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	public function indexAction()
	{
		
	}

	/**
	 * Wyszukaj w pobliżu danej lokalizacji wizytówki...
	 * 
	 * @param float $lat
	 * @param float $lng
	 * @return json
	 */
	public function nearAction()
	{
		$lat = $this->_getParam('lat');
		$lng = $this->_getParam('lng');
		$distance = $this->_getParam('distance', 0.5);

		$searchProxy = new Api_Model_SearchProxy();
		$result = $searchProxy->near($lat, $lng, $distance);
		$this->_helper->json($result);
	}
}