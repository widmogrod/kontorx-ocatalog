<?php
class Agregator_Form_Agregator_Add extends Zend_Form
{
	public function init()
	{
		$this->setMethod(self::METHOD_POST);
		
		$this->addElements(array(
			new KontorX_Form_Element_Db_Select('feed_id',array(
				'label' => 'Źródło wiadomości',
				'required' => true,
				'tableName'   => 'agregator_feed',
				'tableCols'   => array('title','id'),
				'optionValue' => 'title',
				'optionKey'   => 'id'
			)),
			'title' => array(
				'type' => 'text',
				'options' => array(
					'label' => 'Tytuł wpisu',
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
			'description' => array(
				'type' => 'textarea',
				'options' => array(
					'label' => 'Krótka treść wpisu',
					'required' => false,
				)
			),
			
			'dateModified' => array(
				'type' => 'text',
				'options' => array(
					'class' => 'datepicker',
					'label' => 'Data utworzenia',
					'description' => 'format RRRR-MM-DD GG:MM:SS',
					'required' => false,
					'validators' => array(
						new Zend_Validate_Date('Y-m-d')
					)
				)
			),
			
			
			'link' => array(
				'type' => 'text',
				'options' => array(
					'label' => 'Link do źródła wpisu',
					'required' => true,
					'description' => 'link musi się zaczynać od http://',
					'validators' => array(
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
							)
						),
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