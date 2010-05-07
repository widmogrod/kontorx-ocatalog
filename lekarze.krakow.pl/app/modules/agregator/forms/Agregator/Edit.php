<?php
class Agregator_Form_Agregator_Edit extends Agregator_Form_Agregator_Add
{
	protected $_primaryKey;
	
	public function setPrimaryKey($id)
	{
		$this->_primaryKey = $id;
	}
	
	public function getPrimaryKey()
	{
		return $this->_primaryKey;
	}

	public function init()
	{
		$this->getElement('link')
			 ->setValidators(array(
						// minimalna dlugosc 10
						array(
							'validator' => 'StringLength',
							'min' => 7
						),
						// unikalnosc URLu
						array(
							'validator' => 'Db_NoRecordExists',
							'options' => array(
								'table' => 'agregator_agregator',
								'field' => 'link',
								'exlude' => array(
									'field' => 'id',
									'value' => $this->getPrimaryKey()
								)
							)
						),
					));
		$this->getElement('submit')->setLabel('Edytuj');
	}
} 