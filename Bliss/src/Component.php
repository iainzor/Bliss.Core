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
		$data = array();
		$refClass = new \ReflectionClass($this);
		$props = $refClass->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);

		foreach ($props as $refProp) {
			$name = $refProp->getName();
			$property = $this->{$name};

			if (is_object($property) && method_exists($property, "toArray") ) {
				$data[$name] = $property->toArray();
			} else if ($property instanceOf \DateTime) {
				$data[$name] = $property->getTimestamp();
			} else if (method_exists($this, "get{$name}")) {
				$data[$name] = call_user_func(array($this, "get{$name}"));
			} else if (!is_object($property)) {
				$data[$name] = $property;
			}
		}
		
		// Add any extra parameters to the array
		foreach ($this->properties as $name => $value) {
			if (!isset($data[$name])) {
				$data[$name] = $value;
			}
		}
		
		return $data;
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
			$propName = $matches[1];
			$key = strtolower($propName);
			$value = call_user_func(function($args) {
				$value = isset($args[0]) ? $args[0] : null;
				
				if (preg_match("/^[0-9]+$/", $value)) {
					$value = (int) $value;
				} else if (preg_match("/^[0-9]*\.[0-9]+$/", $value)) {
					$value = (float) $value;
				}
				
				return $value;
			}, $arguments);
			
			if (property_exists($this, $propName)) {
				$this->{$propName} = $value;
			} else {
				$this->properties[$key] = $value;
			}
		} else if (preg_match("/^get(.*)$/i", $name, $matches)) {
			$propName = $matches[1];
			
			if (property_exists($this, $propName)) {
				return $this->{$propName};
			} else {
				$key = strtolower($propName);
				
				if (array_key_exists($key, $this->properties)) {
					return $this->properties[$key];
				} else {
					throw new \Exception("Could not find property '{$propName}'");
				}
			}
		} else {
			throw new \Exception("Invalid method: ". get_class($this) ."::{$name}()");
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