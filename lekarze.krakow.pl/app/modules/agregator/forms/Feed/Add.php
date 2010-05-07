<?php
class Agregator_Form_Feed_Add extends Zend_Form
{
	public function init()
	{
		$this->setMethod(self::METHOD_POST);
		
		$this->addElements(array(
			'url' => array(
				'type' => 'text',
				'options' => array(
					'label' => 'Link do RSS',
					'required' => true,
					'value' => 'http://',
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
								'field' => 'url'
							)
						),
					),
				)
			),
			'submit' => array(
				'type' => 'submit',
				'options' => array(
					'label' => 'Dodaj',
					'ignore' => true
				)
			)
		));
	}
} 