<?php
namespace View\Partial;

interface InjectableInterface
{
	public function inject($area, Partial $partial);
	
	public function compileInjectables();
	
	public function renderInjectables($area);
}