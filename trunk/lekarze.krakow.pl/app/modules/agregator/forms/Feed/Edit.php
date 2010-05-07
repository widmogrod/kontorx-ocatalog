<?php
class Agregator_Form_Feed_Edit extends Zend_Form
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
		$this->setMethod(self::METHOD_POST);
		
		$this->addElements(array(
			'url' => array(
				'type' => 'text',
				'options' => array(
					'label' => 'Link do RSS',
					'required' => true,
					'description' => 'link musi się zaczynać od http://',
					'validators' => array(
						// minimalna dlugosc 10
						array(
							'validator' => 'StringLength',
							'min' => 10
						),
						// unikalnosc URLu
						array(
							'validator' => 'Db_NoRecordExists',
							'options' => array(
								'table' => 'agregator_feed',
								'field' => 'url',
								'exlude' => array(
									'field' => 'id',
									'value' => $this->getPrimaryKey()
								)
							)
						),
					),
				)
			),
			'title' => array(
				'type' => 'text',
				'options' => array(
					'label' => 'Tytuł kanału',
					'required' => true,
					'validators' => array(
						// minimalna dlugosc 10
						array(
							'validator' => 'StringLength',
							'min' => 3
						)
					)
				)
			),
			'copyright' => array(
				'type' => 'text',
				'options' => array(
					'label' => 'Prawa autorskie',
					'required' => false,
				)
			),
			'link' => array(
				'type' => 'text',
				'options' => array(
					'label' => 'Link do źródła kanału RSS',
					'required' => true,
					'description' => 'link musi się zaczynać od http://',
					'validators' => array(
						// minimalna dlugosc 10
						array(
							'validator' => 'StringLength',
							'min' => 7
						)
					)
				)
			),
			'submit' => array(
				'type' => 'submit',
				'options' => array(
					'label' => 'Edytuj',
					'ignore' => true
				)
			)
		));
	}
} 