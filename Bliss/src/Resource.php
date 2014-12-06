<?php
namespace Bliss;

abstract class Resource extends Component
{
	/**
	 * @return string
	 */
	abstract public function resourceName();
	
	/**
	 * @var int
	 */
	protected $id = 0;
	
	/**
	 * Set the resource's unique ID
	 * 
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = (int) $id;
	}
	
	/**
	 * Get the resource's unique ID
	 * 
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Add additional properties to the exported array
	 * 
	 * @return array
	 */
	public function toArray() {
		return array_merge(parent::toArray(), [
			"RESOURCE_NAME" => $this->resourceName()
		]);
	}
}