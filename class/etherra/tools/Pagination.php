<?php
namespace etherra;

class tools_Pagination {
	public $total;
	public $count_per_page;
	public $name;
	public $offset = 0;
	public $pages_to_show = 10; //count of pages to show as once

	public $view1;
	public $view2;
	public $pages = array();
	public $current_page = -1;
	public $count_pages = 0;
	public $first_url = '';
	public $prev_url = '';
	public $next_url = '';
	public $last_url = '';

	public $dont_pass = array(); //variables which do not to pass to getUrl

	function __construct($total,$count_per_page=10,$name='page'){
		$this->total = $total;
		$this->name = $name;
		$this->count_per_page = $count_per_page;
		$page = $this->getPage();

		$this->current_page = $page;

		//$this->view1 = $this->current_page*$this->count_per_page+1;
		$this->view1 = $this->current_page*$this->count_per_page+1-$this->count_per_page;
		$this->offset = $this->count_per_page * ($page-1);

		if ($total > $this->count_per_page){
			$this->count_pages = ceil($total/$this->count_per_page);

			$page_begin = ceil($this->current_page-$this->pages_to_show/2);

			if ($page_begin>$this->count_pages-$this->pages_to_show) $page_begin=$this->count_pages-$this->pages_to_show;
			if ($page_begin<1) $page_begin=1;

			$page_end = $page_begin+$this->pages_to_show-1;
			if ($page_end>=$this->count_pages) $page_end = $this->count_pages;

			for ( $i=$page_begin; $i <= $page_end; $i++ ){
				if ($i==$this->current_page){
			 	$this->pages[] = array('num'=>$i,'url'=>'current');
				}
				else {
					$this->pages[] = array('num'=>$i,'url'=>$this->getUrl(array($this->name=>$i)));
				}
			}
			if ($this->current_page == $this->count_pages)
				$this->view2 = $total;
			else
				$this->view2 = $this->view1+$this->count_per_page-1;
		}
		else {
	  $this->count_pages = 1;
	  $this->view1 = 1;
	  $this->view2 = $total;
		}

		if ($this->current_page!=1){
			$this->prev_url = $this->getUrl(array($name=>$this->current_page-1));
			$this->first_url = $this->getUrl(array($name=>1));
		}
		if ($this->current_page!=$this->count_pages){
			$this->next_url = $this->getUrl(array($name=>$this->current_page+1));
			$this->last_url = $this->getUrl(array($name=>$this->count_pages));
		}

	}



	function getUrl($values=array()){
		$first = true;
		$values = array_merge($this->getGetVariables($this->dont_pass),$values);
		$vals = array();
		$this->makeUrlsParts($values,$vals);
		if (empty($vals)) return '';
		return '?'.implode('&amp;',$vals);
	}

	function makeUrlsParts($values,&$vals,$base=''){
		foreach ($values as $key => $val) {
			if(is_array($val))
			{
				if (empty($base)){
					$this->makeUrlsParts($val, $vals, $key);
				}
				else {
					$this->makeUrlsParts($val, $vals, $base.'['.$key.']');
				}
			}
			else {
				if (empty($base)){
					$vals[] = $key."=".$val;
				}
				else {
					$vals[] = $base."[".$key."]=".$val."";
				}
			}
		}
	}


	function getGetVariables($exceptions){
		$res = array();
		foreach ($_GET as $key=>$value){
	  if (!in_array($key,$exceptions)){
	  	$res[$key] = $value;
	  }
		}
		return $res;
	}

	function getPage(){
		if (isset($_GET[$this->name])){
	  $page = $_GET[$this->name];
	   
	  if ($page>(ceil($this->total/$this->count_per_page))){
	  	$page = ceil($this->total/$this->count_per_page);
	  }
	   
	  if ($page<1) return 1;
	  return $page;
		}
		else
	  return 1;
	}


}

