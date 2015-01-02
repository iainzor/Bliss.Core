<?php
namespace Cache\Storage;

trait UtilitiesTrait 
{
	/**
	 * Generate an MD5 hash for the passed arguments
	 * 
	 * @param string $resourceName
	 * @param int $resourceId
	 * @param array $params
	 * @return string
	 */
	public function generateHash($resourceName, $resourceId = null, array $params = [])
	{
		$parts = [$resourceName, $resourceId, json_encode($params)];
		
		return call_user_func(function() use ($parts) {
			$hashParts = [];
			foreach ($parts as $part) {
				$hash = substr(md5($part), 0, 10);
				$hashParts[] = $hash;
			}
			return implode("-", $hashParts);
		});
	}
}