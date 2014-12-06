<?php
namespace Bliss;

class Component
{
	/**
	 * @var array
	 */
	private $properties = [];
	
	/**
	 * Convert the component's properties to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$refClass = new \ReflectionClass($this);
		$props = $refClass->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);
		$data = call_user_func(function($properties) {
			$data = [];
			foreach ($properties as $property) {
				$data[$property["name"]] = $property["value"];
			}
			return $data;
		}, $this->properties);

		foreach ($props as $refProp) {
			$name = $refProp->getName();
			$value = $this->{$name};
			
			$data[$name] = $this->_parse($name, $value);
		}
		
		return $data;
	}
	
	/**
	 * Parse a value based on its type
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 */
	protected function _parse($name, $value)
	{
		$newValue = null;
		
		if (is_object($value) && method_exists($value, "toArray") ) {
			$newValue = $value->toArray();
		} else if ($value instanceOf \DateTime) {
			$newValue = $value->getTimestamp();
		} else if (method_exists($this, "get{$name}")) {
			$newValue = call_user_func(array($this, "get{$name}"));
		} else if (is_array($value)) {
			$newValue = [];
			foreach ($value as $n => $v) {
				$newValue[$n] = $this->_parse($n, $v);
			}
		} else {
			$newValue = $value;
		}
		
		return $newValue;
	}
	
	/**
	 * Magic caller method
	 * - GET and SET properties
	 * 
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, array $arguments) 
	{
		if (preg_match("/^set(.+)$/is", $name, $matches)) {
			$this->_set($matches[1], isset($arguments[0]) ? $arguments[0] : null);
		} else if (preg_match("/^get(.*)$/i", $name, $matches)) {
			return $this->_get($matches[1]);
		} else {
			throw new \Exception("Invalid method: ". get_class($this) ."::{$name}()");
		}
	}
	
	/**
	 * Set a property
	 * @param array $name
	 * @param type $value
	 */
	private function _set($name, $value)
	{
		$name{0} = strtolower($name{0});
		$key = strtolower($name);
		
		if (preg_match("/^[0-9]+$/", $value)) {
			$value = (int) $value;
		} else if (preg_match("/^[0-9]*\.[0-9]+$/", $value)) {
			$value = (float) $value;
		}

		if (property_exists($this, $name)) {
			$this->{$name} = $value;
		} else {
			$this->properties[$key] = [
				"name" => $name,
				"value" => $value
			];
		}
	}
	
	/**
	 * Attempt to get a property's value
	 * 
	 * @param array $name
	 * @return mixed
	 * @throws \Exception
	 */
	private function _get($name)
	{
		$name{0} = strtolower($name{0});
		$key = strtolower($name);
		
		if (property_exists($this, $name)) {
			$ref = new \ReflectionProperty($this, $name);
			if (!$ref->isPrivate()) {
				return $this->{$name};
			}
		} else {
			if (array_key_exists($key, $this->properties)) {
				return $this->properties[$key]["value"];
			} else {
				throw new \Exception("Could not find property '{$name}'");
			}
		}
	}
	
	/**
	 * Generate a new instance of the calling class using late static binding
	 * 
	 * @param array $properties
	 * @return static
	 */
	final public static function factory(array $properties)
	{
		$instance = new static();
		
		return self::populate($instance, $properties);
	}
	
	/**
	 * Populate a component with a set of properties
	 * 
	 * @param \Bliss\Component $component
	 * @param array $properties
	 * @return \Bliss\Component
	 */
	final public static function populate(Component $component, array $properties)
	{
		foreach ($properties as $name => $value) {
			$method = "set{$name}";
			call_user_func([$component, $method], $value);
		}
		
		return $component;
	}
}