<?php

	/**
	* pagination libraray to generate pagination.
	*/
	class pagination {

		private $registry;
		private $no_of_pages;
		private $current_page = 0;
		private $last = false;
		private $first = true;
		private $query;
		private $method = 'do';
		private $cacheId;		
		private $limit = 10;
		
		function __construct($registry) {
			$this->registry = $registry;
		}

		public function setParameters($query, $method = 'do', $limit = 10) {
			$this->query = $query;
			$this->method = ($method==='cache')?$method:'do';
			$this->limit = (is_numeric($limit))?$limit:10;
		}

		public function getNoOfPages() {
			$temp = $this->query;
			$this->registry->getObject('db')->executeQuery($temp);
			$this->no_of_pages = ceil($this->registry->getObject('db')->numRows()/$this->limit);
			return $this->no_of_pages;
		}

		public function getPaginatedResults($current_page = 0) {
			$this->current_page = $current_page;
			$limit = ' LIMIT '.($current_page*$this->limit).','.$this->limit;
			$query = $this->query.$limit;			
			if($this->method === 'cache') {
				$this->cacheId = $this->registry->getObject('db')->cacheQuery($query);
			} else {
				$this->registry->getObject('db')->executeQuery($query);
			}
			return ($this->method==='cache')?$this->cacheId:true;
		}

		public function getAllResults() {
			if($this->method === 'cache') {
				$this->cacheId = $this->registry->getObject('db')->cacheQuery($this->query);
			} else {
				$this->registry->getObject('db')->executeQuery($this->query);
			}
			return ($this->method==='cache')?$this->cacheId:true;
		}

	}

?>