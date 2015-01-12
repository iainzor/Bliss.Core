<?php
namespace Database;

class Registry
{
	/**
	 * @var array
	 */
	private $servers = [];
	
	/**
	 * Add a database server to the registry
	 * 
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @param array $options
	 */
	public function addServer($dsn, $username = null, $password = null, array $options = [])
	{
		$this->servers[] = [
			"dsn" => $dsn,
			"username" => $username,
			"password" => $password,
			"options" => $options
		];
	}
	
	/**
	 * Count the number of servers registered
	 * 
	 * @return int
	 */
	public function totalServers()
	{
		return count($this->servers);
	}
	
	/**
	 * Generate a random connection using the available servers
	 * 
	 * @return \Database\PDO
	 * @throws \Exception
	 */
	public function generateConnection()
	{
		if ($this->totalServers() === 0) {
			throw new \Exception("No connections have been registered");
		}
		
		$config = $this->servers[array_rand($this->servers)];
		$options = array_replace([
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		], $config["options"]);
		
		return new PDO($config["dsn"], $config["username"], $config["password"], $options);
	}
}