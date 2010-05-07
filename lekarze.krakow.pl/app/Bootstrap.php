<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	/**
	 * @var Zend_Controller_Front
	 */
	public $frontController;
	
	protected function _initFr() {
		Zend_Controller_Action_HelperBroker::addPath('KontorX/Controller/Action/Helper','KontorX_Controller_Action_Helper');
	}
	
	protected function _initRequest(array $options = array()) {
        $this->bootstrap('frontController');
        $this->request = new Zend_Controller_Request_Http();
        $this->frontController->setRequest($this->request);
    }
    
    protected function _initRouter() {
    	$configRouter = new Zend_Config_Ini(APP_PATHNAME.'/configuration/router.ini', APP_ENV, true);
    	$this->bootstrap('frontController');
    	$this->frontController
    		 ->getRouter()
    		 ->addConfig($configRouter);
    }

	protected function _initPlugins() {
    	$this->bootstrap('frontController');
    	$front = $this->frontController;
		$front->registerPlugin(new KontorX_Controller_Plugin_i18n(),30);
		$front->registerPlugin(new KontorX_Controller_Plugin_Bootstrap(),98);

		$configSystem = new Zend_Config_Ini(APP_PATHNAME.'/configuration/system.ini', APP_ENV, true);
		$configSystem = new KontorX_Config_Vars($configSystem);
		
		$systemPlugin = new KontorX_Controller_Plugin_System($configSystem);
		$systemPlugin->setApplicationPath(APP_PATHNAME);
		$systemPlugin->setPublicHtmlPath(PUBLIC_PATHNAME);
		$systemPlugin->setTempPath(TMP_PATHNAME);
		$front->registerPlugin($systemPlugin,20);
    }
    
	protected function _initDbDebug() {
		if ($this->getEnvironment() == 'development') {
			$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
			$profiler->setEnabled(true);

			$this->bootstrap('db');
			$db = $this->getResource('db');
			$db->setProfiler($profiler);
			
			$this->bootstrap('frontController');
	    	$front = $this->frontController;
			$front->registerPlugin(KontorX_Controller_Plugin_Debug::getInstance(), 300);
		}	
	}
    
	protected function _initLog() {
		switch ($this->getEnvironment()) {
			case 'development':
			case 'testing':
				$writer = new Zend_Log_Writer_Firebug();
				break;

			default:
			case 'production':
				$host = isset($_SERVER['SERVER_NAME'])
					?  getenv('SERVER_NAME') : getenv('HTTP_HOST');

				$mail = new Zend_Mail();
				$mail->setFrom('no-reply@' . $host)
				     ->addTo('admin@eu1.pl');

				$writer = new Zend_Log_Writer_Mail($mail);
				$writer->setSubjectPrependText('['. CATALOG_TYPE .'] Błędy z strony: '. $host);

//				$writer->addFilter(Zend_Log::DEBUG);
				break;
		}

	    $logger = new Zend_Log();
		$logger->addWriter($writer);

		// w aplikacji wykorzystywane
		Zend_Registry::set('logger', $logger);
	}
    
    protected function _initLocale() {
    	try {
		    $locale = new Zend_Locale('auto');
		} catch (Zend_Locale_Exception $e) {
		    $locale = new Zend_Locale('pl');
		}
		Zend_Registry::set('Zend_Locale', $locale);
    }
    
    protected function _initAcl() {
    	$configAcl = new Zend_Config_Ini(APP_PATHNAME.'/configuration/acl.ini', null, true);

    	$acl = KontorX_Acl::startMvc($configAcl);
		$aclPlugin = $acl->getPluginInstance();
		$aclPlugin->setNoAclErrorHandler('login','auth','user');
		$aclPlugin->setNoAuthErrorHandler('privileges','error','default');
    }
    
    /*protected function _initTranslate() {
		$translator = new Zend_Translate('Tmx', APP_LANGUAGE_PATHNAME . '/pl/validation.xml', 'pl');
		Zend_Validate_Abstract::setDefaultTranslator($translator);
		
		$translator = new Zend_Translate('Tmx', APP_LANGUAGE_PATHNAME . '/pl/application.xml', 'pl');
		Zend_Form::setDefaultTranslator($translator);
    }*/
    
    protected function _initCache() {
    	$configCache = new Zend_Config_Ini(APP_PATHNAME.'/configuration/cache.ini', APP_ENV, array('allowModifications' => true));
		$configCache = new KontorX_Config_Vars($configCache);

		$cacheDBQuery = Zend_Cache::factory(
		    $configCache->dbquery->frontend->name,
		    $configCache->dbquery->backend->name,
		    $configCache->dbquery->frontend->options->toArray(),
		    $configCache->dbquery->backend->options->toArray()
		);
		
		$cacheDatabase = Zend_Cache::factory(
		    $configCache->database->frontend->name,
		    $configCache->database->backend->name,
		    $configCache->database->frontend->options->toArray(),
		    $configCache->database->backend->options->toArray()
		);

		$cacheHour = Zend_Cache::factory(
		    $configCache->hour->frontend->name,
		    $configCache->hour->backend->name,
		    $configCache->hour->frontend->options->toArray(),
		    $configCache->hour->backend->options->toArray()
		);

		Zend_Registry::set('cacheDBQuery', $cacheDBQuery);
		Zend_Registry::set('Zend_Cache', $cacheDBQuery);

		Zend_Registry::set('Zend_Cache_Hour', $cacheHour);

		Zend_Paginator::setCache($cacheDBQuery);
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cacheDatabase);
		KontorX_Db_Table_Abstract::setDefaultResultCache($cacheDatabase);
		Promotor_Model_Abstract::setDefaultResultCache($cacheDBQuery);
    }
    
	public function run() {
		$this->bootstrap('frontcontroller');
		$this->frontController->dispatch();
	}
}
