<?php
class Catalog_Form_CatalogTimeAddWeek extends Promotor_Form_Scaffold {
	public function init() {
		$this->setMethod('post');

		$this->setDecorators(array(
			'FormElements',
            array('TabContainer', array(
                'id' => 'tabContainer',
                'style' => 'width: 100%; height: 500px;',
                'dijitParams' => array(
                    'tabPosition' => 'left'
                ),
            )),
//            'Dojo_ValidateOnSubmit',
            'DijitForm',
        ))
        ->addPrefixPath('Promotor_Form_Decorator_','Promotor/Form/Decorator', self::DECORATOR);
        
        $this->addElement('SubmitButton','action', array('ignore' => true,'label'=>'Dodaj'));

		$subForm = $this->_getContentForm();
        $this->addSubForm($subForm, 'contenttab');
	}
	
	/**
     * @return Zend_Dojo_Form_SubForm
     */
    protected function _getContentForm() {
    	$subForm = new Zend_Dojo_Form_SubForm();
        $subForm->setAttribs(array(
            'name'   => 'contenttab',
            'legend' => 'Treść',
        ));
        $subForm
        	->addElement(new KontorX_Form_Element_Db_Select('catalog_id',
            	array(
            		'tableName' 	=> 'catalog',
            		'tableCols' 	=> array('id','name'),
            		'optionKey' 	=> 'id',
            		'optionValue' 	=> 'name',
                    'label'      	=> 'Wizytówka',
                    'required'      => true
				))
            )
            ->addElement(
//                'TimeTextBox',
				'text',
                'time',
                array(
                	'class'			=> 'timepicker',
                    'label'      	=> 'Godzina',
                    'required'      => true,
                	'timePattern' 	=> 'HH:mm:ss',
//                	'clickableIncrement' => 'T00:15:00',
//                	'visibleIncrement' => 'T00:15:00'
				)
            )
            ->addElement(
                'select',
                'day',
                array(
                    'label'      => 'Dzień',
                	'required'       => true,
                	'multiOptions' => array(
                		null => null,
                		1 => 'poniedziałek',
                		2 => 'wtorek',
                		3 => 'środa',
                		4 => 'czwartek',
                		5 => 'piątek',
                		6 => 'sobota',
                		7 => 'niedziela'
                	)
				)
            )
            ->addElement(
                'select',
                'start_end',
                array(
                    'label'      	=> 'Początek/Koniec',
                	'required'   	=> true,
                	'multiOptions' 	=> array(
                		null => null,
                		'START' => 'Początek',
                		'END' => 'Koniec',
                	)
				)
            );
		
        return $subForm;
    }
}