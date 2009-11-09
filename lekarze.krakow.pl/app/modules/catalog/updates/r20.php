<?php
class Catalog_Update_R20 extends KontorX_Update_Db_Mysql_Table {
	
	public function __construct() {
		parent::__construct('catalog_type');
	}

	public function up() {
		$this->removeColumn('ico');
		$this->addColumn('alias', array(
			'type' => 'VARCHAR(100)',
			'null' => 'NOT NULL',
			'after' => 'AFTER `name`'
		));
		$this->addColumn('alias', array(
			'type' => 'VARCHAR(100)',
			'null' => 'NOT NULL',
			'after' => 'AFTER `name`'
		));
		$this->addColumn('meta_title', array(
			'type' => 'VARCHAR(255)',
			'null' => 'NOT NULL',
			'after' => 'AFTER `name`'
		));
		$this->addColumn('meta_description', array(
			'type' => 'VARCHAR(255)',
			'null' => 'NOT NULL',
			'after' => 'AFTER `name`'
		));
		$this->addColumn('meta_keywords', array(
			'type' => 'VARCHAR(255)',
			'null' => 'NOT NULL',
			'after' => 'AFTER `name`'
		));
		$this->addColumn('description', array(
			'type' => 'MEDIUMTEXT',
			'null' => 'NOT NULL',
			'after' => 'AFTER `name`'
		));
		$this->addIndex('new_alias', array(
			'columns' => array('alias')
		));
	}

	public function down() {
		$this->_setStatus(self::SUCCESS);
	}
}

return new Catalog_Update_R20();