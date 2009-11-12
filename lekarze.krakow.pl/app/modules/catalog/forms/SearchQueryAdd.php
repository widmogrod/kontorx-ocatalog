<?php
class Catalog_Form_SearchQueryAdd extends Promotor_Form_Scaffold {
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
        	->addElement(
        		'TextBox', 
        		'query',
            	array(
                    'label'      	=> 'Zapytanie',
                    'required'      => true
				)
            )
            ->addElement(
//                'TimeTextBox',
				'text',
                'time',
                array(
//                	'class'			=> 'timepicker',
                    'label'      	=> 'Czas',
                    'required'      => false,
                	'value' => date('Y-m-d')
//                	'timePattern' 	=> 'HH:mm:ss',
//                	'clickableIncrement' => 'T00:15:00',
//                	'visibleIncrement' => 'T00:15:00'
				)
            );
		
        return $subForm;
    }
}