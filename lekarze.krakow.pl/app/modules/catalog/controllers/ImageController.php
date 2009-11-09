<?php
require_once 'KontorX/Controller/Action/CRUD.php';
class Catalog_ImageController extends KontorX_Controller_Action_CRUD {
	public $skin = array(
		'layout' => 'admin_catalog',
		'config' => array(
			'filename' => 'backend_config.ini')
	);

	protected $_modelClass = 'CatalogImage';
	
	protected function _getConfigFormFilename($controller) {
		return strtolower("$controller.xml");
	}
	
	protected function _getModel() {
		if (null == $this->_model) {
			$this->_model = parent::_getModel();

			$config = $this->_helper->config();
			$path = $config->path->upload->image;
			$path = $this->_helper->system()->getPublicHtmlPath($path);
			
			CatalogImage_Row::setUploadPath($path);
		}
		return $this->_model;
	}

	public function listAction() {
		$this->view->addHelperPath('KontorX/View/Helper');
		
		$config = $this->_helper->config('image.xml');
		
		$model = new Catalog_Model_Image();

		$grid = KontorX_DataGrid::factory($model->selectWithCatalog());
		$grid->setColumns($config->dataGridColumns->toArray());
		$grid->setValues((array) $this->_getParam('filter'));
		
		// setup grid paginatior
		$select = $grid->getAdapter()->getSelect();
		$paginator = Zend_Paginator::factory($select);
		$grid->setPaginator($paginator);
		$grid->setPagination($this->_getParam('page'), 20);

		$this->view->grid = $grid;
		$this->view->actionUrl = $this->_helper->url('list');
	}

	/**
	 * Konwetuje pliki by kończycły sie na .jpg
	 * @return void
	 */
	public function convertAction() {
		
		// pobieranie konfiguracji
        $config = $this->_helper->config('image.xml');

		$path = PUBLIC_PATHNAME . '/' . ltrim($config->resizer->dirname, '/');
		
		$model = new Catalog_Model_Image();
		$model->convert($path);
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');

		// add messages
		$flashMessenger->addMessage($model->getStatus());
		array_map(array($flashMessenger,'addMessage'), $model->getMessages());

		$this->_helper->redirector->goTo('list');
	}
	
	/**
	 * Ustawia grafikę na logo!
	 * @return void
	 */
	public function mainimageAction() {
		$primaryKey = $this->_getParam('id');

		$model = new Catalog_Model_Image();
		$model->setMainImage($primaryKey);
		
		/* @var $flashMessenger Zend_Controller_Action_Helper_FlashMessenger */
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$flashMessenger->addMessage($model->getStatus());
		array_map(array($flashMessenger,'addMessage'), $model->getMessages());

		// przekierowanie
		$this->_helper->redirector->goToUrlAndExit(getenv('HTTP_REFERER'));
	}
	
	/**
	 * Overwrite
	 */
	protected function _addGetForm() {
		$form = parent::_addGetForm();

		// setup @see Zend_Form
		$this->_setupZendForm($form);

		return $form;
	}

	protected function _addInsert(Zend_Form $form) {
		$data = $this->_addPrepareData($form);

		try {
			// dodawanie rekordu
	    	$model = $this->_getModel();
	    	$row = $model->createRow($data);
	    	$row->save();
		} catch (KontorX_Db_Table_Row_FileUpload_Exception $e) {
			if($row->hasMessages()) {
				$flashMessenger = $this->_helper->flashMessenger;
				foreach ($row->getMessages() as $message) {
					$flashMessenger->addMessage($message);
				}
			}
			
			throw new KontorX_Db_Table_Row_FileUpload_Exception(implode("\n",$row->getMessages()));
		}

		return $row;
	}
	
	/**
	 * Overwrite
	 */
	protected function _editGetForm(Zend_Db_Table_Row_Abstract $row) {
		$form = parent::_editGetForm($row);

		// setup @see Zend_Form
		$this->_setupZendForm($form, true);

		return $form;
	}
	
	/**
     * Ustawia opcje formularza
     *
     * @param Zend_Folm $form
     * @param bool $edit
     */
    protected function _setupZendForm(Zend_Form $form, $edit = false) {
    	$form->setAttrib('enctype','multipart/form-data');

    	require_once 'KontorX/Form/Element/File.php';
		$form->addElement(new KontorX_Form_Element_File('image'), 'image');
		$image = $form->getElement('image');
		$image->setLabel('image');

		if (true === $edit) {
			$image->setIgnore(true);
		}
    }
    
	public function thumbAction() {
        // wylaczenie renderowania widoku
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // pobieranie konfiguracji
        $config = $this->_helper->config('image.xml');

        $resizerConfig = $config->resizer->toArray();
        $resizerConfig['dirname'] = PUBLIC_PATHNAME . '/' . ltrim($resizerConfig['dirname'], '/');

        $type = $this->_getParam('type');
        $filename = $this->_getParam('file');
        $thumbPathname = $type . DIRECTORY_SEPARATOR . $filename;

        try {
            // resizing
            require_once 'KontorX/Image/Resizer.php';
            $resizer = new KontorX_Image_Resizer($resizerConfig);
            $resizer->setFilename($filename);
            $resizer->setMultiType($type);
            $image = $resizer->resize();

            // display
            $image->display();

            // save
            require_once 'KontorX/File/Write.php';
            $write = new KontorX_File_Write($resizerConfig);
            $write->setBasedir($resizer->getDirname());
            $write->setForce(true);
            $write->write($thumbPathname, $image->toString());

            return;
        } catch (KontorX_Image_Exception $e) {
            Zend_Registry::get('logger')->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::WARN);
        } catch (KontorX_File_Exception $e) {
            Zend_Registry::get('logger')->log($e->getMessage() . "\n" . $e->getTraceAsString(), Zend_Log::WARN);
        }

        // wyswietlanie domyslnej grafiki
        $this->_helper->redirector->goToUrlAndExit(
            $this->getFrontController()->getBaseUrl() . '/' . rtrim($resizerConfig['dirname']) . '/' . basename($config->defaultImage)
        );
    }
    
//	/**
//	 * Generowanie miniaturek
//	 *
//	 * TODO Optymalizacja
//	 */
//	public function thumbAction() {
//		// wylaczenie renderowania widoku
//		$this->_helper->layout->disableLayout();
//		$this->_helper->viewRenderer->setNoRender();
//
//		// pobieranie konfiguracji
//		$config = $this->_helper->config();
//		$path = $config->path->upload->image;
//		$uploadPath = $this->_helper->system()->getPublicHtmlPath($path);
//
////		$uploadPath = $config->path->upload;
//		$noImagePath = $config->default->image;
//
//		$type = $this->_getParam('type');
//
//		$imageName = $this->_getParam('file');
//		$imagePath = $uploadPath . '/' . basename($imageName);
//
//		// na starcie wysylane sa naglowki - to przez cache!
//		$this->_response->setHeader('Content-type', image_type_to_mime_type(IMAGETYPE_JPEG));
//
//		// czy obrazek istnieje
//		if (!is_file($imagePath)) {
//			// logowanie zdarzeń
//			$logger = Zend_Registry::get('logger');
//			$logger->log(get_class($this)."::thumbAction() noe exsists $imagePath", Zend_Log::DEBUG);
//
//			$this->_helper->redirector->goToUrlAndExit($this->getFrontController()->getBaseUrl()  .$noImagePath);
//		}
//
//		try {
//			$image = new KontorX_Image($imagePath);
//		} catch (KontorX_Image_Exception $e) {
//			// logowanie zdarzeń
//			$logger = Zend_Registry::get('logger');
//			$logger->log($e->getMessage() . "\n" .  $e->getTraceAsString(), Zend_Log::NOTICE);
//
//			$this->_helper->redirector->goToUrlAndExit($this->getFrontController()->getBaseUrl()  .$noImagePath);
//		}
//
//		$types = $config->image->resize->type->toArray();
//
//		if (array_key_exists($type, $types)) {
//			$x 		= @$types[$type]['x'];
//			$y 		= @$types[$type]['y'];
//			$xCrop 	= @$types[$type]['crop']['x'];
//			$yCrop 	= @$types[$type]['crop']['y'];
//			$style 	= @$types[$type]['style'];
//
//			switch ($style) {
//				case 'max':
//					if (is_numeric($x) && null === $y) {
//						$image->resizeToMaxWidth($x);
//					} else
//					if (is_numeric($y) && null === $x) {
//						$image->resizeToMaxHeight($y);
//					} else
//					if (is_numeric($x) && is_numeric($y)) {
//						$image->resizeToMax($x, $y);
//					}
//					break;
//				case 'crop':
//					$image->resizeToMaxWidth($x);
//					$image->crop(0,0,$xCrop,$yCrop);
//					break;
//				default: $image->resize($x, $y);
//			}
//		} else {
//			$image->resize(50,50);
//		}
//
//		// print - bo naglowej jest wysylany wyzej
//		$img = $image->display(IMAGETYPE_JPEG, null, true);
//		$this->_response->setBody($img);
//
//		// zapisywanie miniaturki do odpowiedniego katalogu
//		// jest tutaj mała sztuczka
//		// nazwa katalogu i pliku odpowiada sciezce generowanej przez ZF
//		// zatem gdy miniaturka bedzie istniec omijamy generator miniaturek
//		// i kierujemy sie bezposrednio do sciezki z miniaturka - leprza wydajnosc
//		$thumbDirname  = $uploadPath . '/' . $type;
//		$thumbPathname = $thumbDirname . '/' .$imageName;
//		if (!is_dir($thumbDirname)) {
//			if (!@mkdir($thumbDirname,0755)) {
//				// logowanie zdarzeń
//				$logger = Zend_Registry::get('logger');
//				$logger->log(get_class($this).'::thumbAction mkdir('.$thumbDirname.')' , Zend_Log::WARN);
//				return;
//			}
//		}
//		if (!@file_put_contents($thumbPathname, $img)) {
//			// logowanie zdarzeń
//			$logger = Zend_Registry::get('logger');
//			$logger->log(get_class($this).'::thumbAction file_put_contents('.$thumbPathname.')', Zend_Log::WARN);
//		} else {
//			@chmod($thumbPathname, 0755);
//		}
//	}
}