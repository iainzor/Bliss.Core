<?php
namespace Cache\Storage;

use Cache\Resource\Resource,
	Cache\Resource\ResourceInterface;

class MemcacheStorage implements StorageInterface
{
	use UtilitiesTrait;
	
	/**
	 * @var \Memcache
	 */
	private $memcache;
	
	/**
	 * Constructor
	 * 
	 * @param \Memcache $memcache
	 */
	public function __construct(\Memcache $memcache)
	{
		$this->memcache = $memcache;
	}
	
	/**
	 * Close the connection to the memcached server
	 */
	public function __destruct() 
	{
		$this->memcache->close();
	}
	
	/**
	 * Attempt to get cache from the memcached server
	 * 
	 * @param string $hash
	 * @return mixed
	 */
	public function get($hash) 
	{
		return $this->memcache->get($hash);
	}
	
	/**
	 * Delete cache from the memcached server
	 * 
	 * @param string $hash
	 * @return boolean
	 */
	public function delete($hash) 
	{
		return $this->memcache->delete($hash);
	}

	/**
	 * Add a resource's contents to the memcached server
	 * 
	 * @param string $hash
	 * @param mixed $contents
	 * @return boolean
	 */
	public function put($hash, $contents) 
	{
		return $this->memcache->set($hash, $contents);
	}
}