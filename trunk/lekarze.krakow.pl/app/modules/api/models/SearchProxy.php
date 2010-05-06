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
		$serwice = new Catalog_Model_Search();
		$resutl = $serwice->findLucene($query);
		return $resutl;
	}
}