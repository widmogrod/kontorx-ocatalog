<?php
class Api_Model_SearchProxy
{
	/**
	 * 
	 * @param string $query
	 * @return array
	 */
	public function lucene($query)
	{
		$service = new Catalog_Model_Search();
		$resutl = $serwice->findLucene($query);
		return $resutl;
	}

	/**
	 * @param float $lat
	 * @param float $lng
	 * @param float $distance
	 * @return array
	 */
	public function near($lat, $lng, $distance = null)
	{
		$service = new Catalog_Model_Search();
		return $service->nearSimpleData($lat, $lng, $distance);
	}
}