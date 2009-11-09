<?php
require_once 'Zend/Form/DisplayGroup.php';
class Catalog_Form_DisplayGroup extends Zend_Form_DisplayGroup {
	/**
     * Load default decorators
     * 
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator('Fieldset');
//                 ->addDecorator('DtDdWrapper');
        }
    }
}
