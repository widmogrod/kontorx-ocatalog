<?php
class System_Model_Update extends Promotor_Model_Abstract {

	/**
	 * @param string $path
	 * @return void
	 */
	public function __construct($path) {
		$this->setModulesPathname($path);
	}

	/**
	 * @var void
	 */
	protected $_modulesPathname;

	/**
	 * @param string $path
	 * @return System_Model_Update
	 */
	public function setModulesPathname($path) {
		if (!is_dir($path)) {
			throw new Promotor_Model_Exception(
					sprintf('update path "%s" do not exsists', $path));
		}

		$this->_modulesPathname = (string) rtrim($path,'\\/') . '/';
		return $this;
	}
	
	/**
	 * @param string $module
	 * @return string
	 */
	public function getModulesPathname($module = null) {
		return (null === $module)
			? $this->_modulesPathname
			: $this->_modulesPathname . basename($module) . '/' . $this->getUpdatesDirname();
	}

	/**
	 * @var void
	 */
	protected $_updatesDirname = 'updates';
	
	/**
	 * @param string $dirname
	 * @return System_Model_Update
	 */
	public function setUpdatesDirname($dirname) {
		$this->_updatesDirname = dirname($dirname);
		return $this;
	}
	
	/**
	 * @return unknown_type
	 */
	public function getUpdatesDirname() {
		return $this->_updatesDirname;
	}
	
	/**
	 * @var array
	 */
	protected $_enabledModules;

	/**
	 * @return array
	 */
	public function getEnabledModulesNames() {
		if (null === $this->_enabledModules) {
			$front = Zend_Controller_Front::getInstance();
			$this->_enabledModules = (array) $front->getControllerDirectory();
			$this->_enabledModules = array_map('dirname', $this->_enabledModules);
			$this->_enabledModules = array_map('basename', $this->_enabledModules);
		}
		return $this->_enabledModules;
	}
	
	/**
	 * @var array
	 */
	protected $_modules;
	
	/**
	 * @return array
	 */
	public function getAllModulesNames() {
		if (null === $this->_modules) {
			$this->_modules = array();
			$pathname = $this->getModulesPathname();
			$iterator = new DirectoryIterator($pathname);
			while ($iterator->valid()) {
				if ($iterator->isDir() && !$iterator->isDot()) {
					$filename = $iterator->getFilename();
					// nazwa modułu tylko alfa..
					if (preg_match('/^[\w]+$/', $filename)) {
						$this->_modules[] = $filename;
					}
				}
				$iterator->next();
			}
		}
		return $this->_modules;
	}

	/**
	 * @return void
	 */
	public function findAll() {
		$all =  $this->getAllModulesNames();
		$enabled = $this->getEnabledModulesNames();

		$result = array();
		foreach ($all as $module) {
			$pathname = $this->getModulesPathname($module);

			/* @var $manager KontorX_Update_Manager */
			$manager = new KontorX_Update_Manager($pathname);

			$result[$module] = array(
				'id' => $module,
				'name' => $module,
				'revision' => $manager->getLastUpdate(),
				'newest' => $manager->getNewestUpdate(),
				'enabled' => array_key_exists($module, $enabled)
			);
		}

		return $result;
	}

	/**
	 * @param string $module
	 * @param bool $force
	 * @return unknown_type
	 */
	public function update($module, $force = false) {
		$pathname = $this->getModulesPathname($module);

		/* @var $manager KontorX_Update_Manager */
		$manager = new KontorX_Update_Manager($pathname);
		
		$force = (true === $force)
			? KontorX_Update_Manager::FORCE
			: null;

		try {
			$status = $manager->update($force);
		} catch(KontorX_Update_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}

		$status = (true === $status)
			? self::SUCCESS
			: self::FAILURE;

		$this->_setStatus($status);
		$this->_addMessages($manager->getMessages(false));
	}
	
	/**
	 * @param string $module
	 * @param bool $force
	 * @return unknown_type
	 */
	public function downgrade($module, $force = false) {
		$pathname = $this->getModulesPathname($module);

		/* @var $manager KontorX_Update_Manager */
		$manager = new KontorX_Update_Manager($pathname);
		
		$force = (true === $force)
			? KontorX_Update_Manager::FORCE
			: null;

		try {
			$status = $manager->downgrade($force);
		} catch(KontorX_Update_Exception $e) {
			$this->_addException($e);
			$this->_setStatus(self::FAILURE);
			return;
		}

		$status = (true === $status)
			? self::SUCCESS
			: self::FAILURE;

		$this->_setStatus($status);
		$this->_addMessages($manager->getMessages(false));
	}
}