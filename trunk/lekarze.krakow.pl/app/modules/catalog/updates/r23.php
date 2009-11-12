<?php
class Catalog_Update_R20 extends KontorX_Update_Db_Mysql_Table {
	
	public function __construct() {
		parent::__construct('catalog');
	}

	public function up() {
		$this->addColumn('idx', array(
			'type' => 'BIGINT',
			'null' => 'NOT NULL',
			'after' => 'AFTER `id`'
		));
	}

	public function down() {
		$this->_setStatus(self::SUCCESS);
	}
}

return new Catalog_Update_R20();