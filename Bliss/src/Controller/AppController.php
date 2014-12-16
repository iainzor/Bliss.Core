<?php
namespace Bliss\Controller;

class AppController extends AbstractController 
{
	public function indexAction()
	{
		$data = $this->app->toArray();
		$data["pages"] = [
			[
				"title" => "Home",
				"path" => "",
			], [
				"title" => "Documentation",
				"path" => "docs"
			]
		];
		
		return $data;
	}
}