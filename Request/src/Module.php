<?php
namespace Request;

class Module extends \Bliss\Module\AbstractModule
{
	const PARAM_MODULE = "module";
	const PARAM_CONTROLLER = "controller";
	const PARAM_ACTION = "action";
	const PARAM_FORMAT = "format";
	
	/**
	 * @var array
	 */
	private $_defaultParams = [
		self::PARAM_MODULE => "request",
		self::PARAM_CONTROLLER => "index",
		self::PARAM_ACTION => "index",
		self::PARAM_FORMAT => null
	];
	
	/**
	 * @var array
	 */
	private $params = [];
	
	/**
	 * @var string
	 */
	private $uri;
	
	public function init()
	{
		$input = file_get_contents("php://input");
		if (strlen($input)) {
			$dataArray = json_decode($input, true);

			if (is_array($dataArray)) {
				$this->_defaultParams += $dataArray;
			}
		}
		
		if ($this->isPost()) {
			$postVars = filter_input_array(INPUT_POST);
			if ($postVars) {
				$this->_defaultParams += $postVars;
			}
		}
	}
	
	/**
	 * Set the requested URI
	 * 
	 * @param string $uri
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;
	}
	
	/**
	 * Get the requested URI
	 * 
	 * @return string
	 */
	public function uri()
	{
		return $this->uri;
	}
	
	/**
	 * Merges parameters into the request
	 * 
	 * @param array $params
	 */
	public function setParams(array $params)
	{
		$this->params = array_merge($this->params, $params);
	}
	
	/**
	 * Get all parameters of the request
	 * 
	 * @return array
	 */
	public function params()
	{
		if (!isset($this->params)) {
			$this->params = $this->_defaultParams;
		}
		
		return $this->params;
	}
	
	/**
	 * Get a single parameter value
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function param($name, $defaultValue = null)
	{
		return isset($this->params[$name])
			? $this->params[$name]
			: $defaultValue;
	}
	
	/**
	 * Set a parameter value
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		$this->params[$name] = $value;
	}
	
	/**
	 * Set the request's parameters to the default values
	 */
	public function reset() { $this->params = $this->_defaultParams; }
	
	/**
	 * Get the requested method
	 * 
	 * @return string
	 */
	public function method()
	{
		return filter_input(INPUT_SERVER, "REQUEST_METHOD");
	}
	
	/**
	 * Check if a POST request has been made
	 * 
	 * @return boolean
	 */
	public function isPost()
	{
		return $this->method() === "POST";
	}
	
	/**
	 * Check if a DELETE request has been made
	 * 
	 * @return boolean
	 */
	public function isDelete()
	{
		return $this->method() === "DELETE";
	}
	
	/**
	 * Getters and setters
	 */
	public function getModule() { return $this->param(self::PARAM_MODULE); }
	public function setModule($module) { $this->set(self::PARAM_MODULE, $module); }
	
	public function getController() { return $this->param(self::PARAM_CONTROLLER); }
	public function setController($controller) { return $this->set(self::PARAM_CONTROLLER, $controller); }
	
	public function getAction() { return $this->param(self::PARAM_ACTION); }
	public function setAction($action) { $this->set(self::PARAM_ACTION, $action); }
	
	public function getFormat() { return $this->param(self::PARAM_FORMAT); }
	public function setFormat($format) { $this->set(self::PARAM_FORMAT, $format); }
}