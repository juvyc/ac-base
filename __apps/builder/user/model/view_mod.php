<?php
 /**
 * @View_Mod
 * Type: Object
 * Desc: It handles view methods of user builder
 */
 
 class View_Mod extends Base_System
 {
	public 	$where = '', 
			$sorter = ' ORDER BY `id` DESC ',
			$page_num = 1,
			$page_rows = 10;
			
	public function constructor()
	{
		$this->db = $this->Ini()->DB()->exec();
		$this->get = $this->Ini()->Action()->GET();
		
		if($this->get->param('s-category') > 0){
			$this->where .= ($this->where == "") ? " WHERE a.`category` = " . $this->db->escape_string($this->get->param('s-category')) : " AND a.`category` = " . $this->db->escape_string($this->get->param('s-category'));
		}
	}
	
	public function getLists()
	{
		
		$stmt = "
				SELECT 
						*
						FROM ". $this->db->db_prefix ."users
						". $this->where ."
						GROUP BY id
						".$this->sorter;
		
		if($this->get->param('s-itemperpage')){
			if($this->get->param('s-itemperpage') != 'all'){
				$stmt .= "LIMIT ". ($this->page_num - 1) * $this->page_rows .",". $this->page_rows;
			}
		}else{
			$stmt .= "LIMIT ". ($this->page_num - 1) * $this->page_rows .",". $this->page_rows;
		}
		
		return $this->db->query($stmt)->run();
	}
	
	public function getListsRows()
	{
		$stmt = "
						SELECT 
							COUNT(DISTINCT(id)) AS num_rows
						FROM ". $this->db->db_prefix ."users
						". $this->where;
		$query = $this->db->query($stmt)->run()->fetch_object();
		
		return (count($query)) ? $query->num_rows : 0;
	}
 }