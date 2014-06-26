<?php

class homeController {

	private $registry;
	private $controller = 'home';

	public function __construct($registry) {
		$this->registry = $registry;
		$this->registry->getObject('template')->buildFromTemplates(['index.html']);
	}
	
}

?>