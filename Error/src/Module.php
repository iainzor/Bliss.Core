<?php
namespace Error;

use Response\Format\InvalidFormatException;

class Module extends \Bliss\Module\AbstractModule implements ErrorHandlerInterface
{
	public function handleError($number, $string, $file, $line)
	{
		throw new \Exception("Error '{$string}' in file '{$file}' on line '{$line}'", $number);
	}

	public function handleException(\Exception $e) 
	{
		ob_end_clean();
		
		$response = $this->app->response();
		$request = $this->app->request();
		$formatName = $request->getFormat();
		$format = $response->format($formatName);
		
		switch ($e->getCode()) {
			case 404:
				$response->setCode(404);
				break;
			default:
				$response->setCode(500);
				break;
		}
		
		if ($e instanceof InvalidFormatException || !$format->requiresView()) {
			$request->setFormat(null);
		}
		
		$this->app->execute([
			"module" => "error",
			"controller" => "error",
			"action" => "handle",
			"format" => $request->getFormat(),
			"exception" => $e
		]);
	}
}