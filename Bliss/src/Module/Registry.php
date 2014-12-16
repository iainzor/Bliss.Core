<?php
namespace Bliss\Module;

use Bliss\App\Container as App;

class Registry implements \Iterator
{
	/**
	 * @var \Bliss\App\Container
	 */
	private $app;
	
	/**
	 * @var array
	 */
	private $modules = [];
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\App\Container $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}
	
	/**
	 * Register a directory containing multiple modules
	 * 
	 * @param string $dirname
	 * @throws \Exception
	 */
	public function registerModulesDirectory($dirname)
	{
		if (!is_dir($dirname)) {
			throw new \Exception("Invalid directory: {$dirname}");
		}
		
		$this->app->log("Registering '{$dirname}' as a directory containing modules");
		
		foreach (new \DirectoryIterator($dirname) as $dir) {
			if ($dir->isDir() && !$dir->isDot() && !preg_match("/^[\._]/", $dir->getFilename())) {
				$this->registerModule($dir->getBasename(), $dir->getPathname());
			}
		}
	}
	
	/**
	 * Register a module with the registry
	 * 
	 * @param string $namespace
	 * @param string $dirname
	 * @throws \Exception
	 */
	public function registerModule($namespace, $dirname)
	{
		if (!is_dir($dirname)) {
			throw new \Exception("Invalid directory: {$dirname}");
		}
		
		$this->app->autoloader()->registerNamespace($namespace, $dirname ."/src");
		$this->app->log("Registering module '{$namespace}'");
		
		$alias = strtolower(preg_replace("/[^a-z0-9]/i", "", $namespace));
		$className = $namespace ."\\Module";
		
		$this->modules[$alias] = [
			"className" => $className,
			"rootPath" => $dirname,
			"instance" => null
		];
	}
	
	/**
	 * Attempt to get a module by its name
	 * 
	 * @param string $moduleName
	 * @return \Bliss\Module\ModuleInterface
	 * @throws \Exception
	 */
	public function get($moduleName)
	{
		$alias = strtolower(preg_replace("/[^a-z0-9]/i", "", $moduleName));
		
		if (isset($this->modules[$alias])) {
			$config = $this->modules[$alias];
			$instance = $config["instance"];
			
			if ($instance === null) {
				$this->app->log("Creating module instance for '{$moduleName}'");
				
				$className = $config["className"];
				$rootPath = $config["rootPath"];
				$instance = new $className($this->app, $rootPath, $moduleName);

				if (!($instance instanceof ModuleInterface)) {
					throw new \Exception("Module '{$instance->name()}' must be an instance of Bliss\\Module\\ModuleInterface");
				}
				
				$instance->init();
				
				$this->modules[$alias]["instance"] = $instance;
			}
			
			return $instance;
		}
		
		throw new \Exception("Module '{$moduleName}' could not be found", 404);
	}
	
	/**
	 * Implementation of \Iterator
	 */
		public function current() { $name = key($this->modules); return $this->get($name); }
		public function key() { return key($this->modules); }
		public function next() { return next($this->modules); }
		public function rewind() { return reset($this->modules); }
		public function valid() { $key = key($this->modules); return $key !== null && $key !== false; }

}