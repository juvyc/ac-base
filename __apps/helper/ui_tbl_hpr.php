<?php
 class Ui_tbl_Hpr
 {
	protected $mod_props;
	private $ui = '';
	protected $fields = [];
	protected $limit = 0;
	public function build($mod, $data, $total_rows=0)
	{
		$this->mod_props = new $mod();
		
		$this->fields = $this->mod_props->ui_tbl_view();
		$this->limit = $this->mod_props->ui_tbl_limit;
		
		$this->ui = '<div class="ui-tbl-lst-cont tbl-'. $this->mod_props->_table .'">';
		
		$this->ui .= '<table class="table table-striped">';
			if(property_exists($this->mod_props, 'ui_tbl_head') && $this->mod_props->ui_tbl_head == true){
				$this->ui_head();
			}
			
			$this->ui_body($data);
			
			if(property_exists($this->mod_props, 'ui_tbl_foot') && $this->mod_props->ui_tbl_foot == true){
				$this->ui_head();
			}
			
		$this->ui .= '</table>';
		
		$this->ui .= $this->ui_pagination($total_rows);
		
		$this->ui .= '</div>';
		
		return $this->ui;
	}
	
	private function ui_body($data)
	{
		if(property_exists($this->mod_props, 'ui_tbl_while_db') && $this->mod_props->ui_tbl_while_db == true && $data->num_rows()){
			while($row = $data->fetch_array()){
				if(!empty($this->fields)){
					$this->ui .= '<tr class="lst-item lst-item-'. $row['id'] .'">';
					foreach($this->fields as $fn => $lbl){
						$this->ui .= '<td class="lst_td_'. $fn .'">'. (is_array($lbl) ? $lbl['sf']($row, $fn) : $row[$fn]) .'</td>';
					}
					$this->ui .= '</tr>';
				}
			}
		}else if(!empty($data)){
			$this->ui .= '<tbody>';
			foreach($data as $row){
				
				if(!is_array($row)) $row = (array) $row;
				
				if(!empty($this->fields)){
					$this->ui .= '<tr class="lst-item lst-item-'. $row['id'] .'">';
					foreach($this->fields as $fn => $lbl){
						$this->ui .= '<td class="lst_td_'. $fn .'">'. (is_array($lbl) ? $lbl['sf']($row, $fn) : $row[$fn]) .'</td>';
					}
					$this->ui .= '</tr>';
				}
			}
			$this->ui .= '</tbody>';
		}
	}
	
	private function ui_head()
	{
		if(!empty($this->fields)){
			$this->ui .= '<thead>';
			$this->ui .= '<tr>';
			foreach($this->fields as $fn => $lbl){
				$this->ui .= '<th class="lst_th_'. $fn .'">'. (is_array($lbl) ? $lbl['lbl'] : $lbl) .'</th>';
			}
			$this->ui .= '</tr>';
			$this->ui .= '</thead>';
		}
	}
	
	private function ui_pagination($total_rows)
	{
		$page = $_REQUEST['p'] ?? 1;
		
		$count = $total_rows;
		
		if( $count > 0 ) {
			$total_pages = ceil($count/$this->limit);
			
			if ($page > $total_pages) $page=$total_pages;
		} else {
			$total_pages = 0;
		}
		
		ob_start();
?>
		<div class="btn-toolbar" role="toolbar">
		  <div class="btn-group" role="group">
			<button type="button" class="btn btn-secondary btn-sm page-item" title="Go to First" data-num="1"><i class="fas fa-angle-double-left"></i></button>
		  </div>
		  &nbsp;
		  <button type="button" class="btn btn-secondary btn-sm page-item" title="Previous" data-num="<?php echo ($page > 1) ? ($page - 1) : 1; ?>"><i class="fas fa-chevron-left"></i></button>
		  &nbsp;
		  <div class="btn-group" role="group">
			<label class="mr-2">Page:</label>
			<input type="number" min="1" step="1" max="<?php echo $total_pages; ?>" class="form-control form-control-sm text-center inputPager" value="<?php echo $page; ?>" style="width:60px;"/> <span> &nbsp; of <?php echo $total_pages; ?></span>
		  </div>
		  &nbsp;
		  <button type="button" class="btn btn-secondary btn-sm page-item" title="Next" data-num="<?php echo ($page < $total_pages) ? ($page + 1) : $total_pages ; ?>"><i class="fas fa-angle-right"></i></button>
		  &nbsp;
		  <div class="btn-group" role="group">
			<button type="button" class="btn btn-secondary btn-sm page-item" title="Go to Last" data-num="<?php echo $total_pages; ?>"><i class="fas fa-angle-double-right"></i></button>
		  </div>
		</div>
		
<?php
		$resp = ob_get_contents();
		ob_end_clean();
		
		return $resp;
	}
 }