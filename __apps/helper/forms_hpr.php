<?php
 class Forms_Hpr extends \Base_System
 {
	
	 public $ready_fields_list = [];
	 public $foreign_save_list = []; //Some actions required after the insertion of the parent table
	 public $current_user, $global_mod, $conn_init;
	 
	 private $all_vals = [];
	 
	 public function sync()
	 {
		 $this->global_mod =  $this->Ini()->Mod('common')->load('global');
		 $this->current_user = $this->global_mod->_getCurrentUserInfo();
	 }
	 
	 public function field_constructor($field=array(), $props=array(), $isView=0)
	 {
		 if(empty($field)) return '';
			 
		 $fName = isset($field['name']) ? $field['name'] : '';
		 $fId = (isset($field['id'])) ? $field['id'] : $fName;
		 
		 if(isset($field['id'])){
		 	 $fValue = (isset($props['group_values']) && isset($props['group_values'][$field['id']])) ? $props['group_values'][$field['id']] : (isset($field['value']) ? $field['value'] : '');
		 	 if(isset($field['func'])){
		 	 	 $fValue = $field['func']($fValue);
		 	 }
		 	 $forId =$field['id']; 
		 }else{
		 	 $fValue = (isset($props['group_values']) && isset($props['group_values'][$fName])) ? $props['group_values'][$fName] : (isset($field['value']) ? $field['value'] : '');
		 	 if(isset($field['func'])){
		 	 	 $fValue = $field['func']($fValue);
		 	 }
		 	 $forId =$fName;
		 }
		 
		 if($isView){
			 $fClass = (isset($field['class'])) ? $field['class'] : '';
		 }else{
			$fClass = (isset($field['class'])) ? $field['class'] : 'form-control';
		 }
		 
		 $fAddClass = (isset($field['addClass'])) ? ' ' . $field['addClass'] : '';
		 $fClass .= $fAddClass;
		 $fLblClass = (isset($field['lblClass'])) ? $field['lblClass'] : '';
		 $fLblRequired = (isset($field['required'])) ? 'required' : '';
		 $fLblAttribs = (isset($field['attribs'])) ? $field['attribs'] : '';
		 $fconClass = (isset($field['fconClass'])) ? $field['fconClass'] : '';
		 $placeholder = (isset($field['placeholder'])) ? ' placeholder="'. $field['placeholder'] .'" ' : '';
		 
		 $_html = "";
		 
		 if($isView){
		 	 if(!empty($field['label'])){
		 	 	 $_html .= '<label class="view-lbl '. $fLblClass .'">'. $field['label'] .'</label>';
			 }
			 if(isset($field['formatted'])){
				 $_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $field['formatted']($fValue) .'</div>';
			 }else{
				$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
			 }
		 }else{
			 if($field['type'] == 'select'){
				if($fconClass) $_html .= '<div class="form-group row">';
				$_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					$_html .= '<select '. $placeholder . (isset($field['multiVal']) ? 'multiple' : '') .' name="'. $fName . (isset($field['multiVal']) ? '[]' : '') .'" id="'. $fId .'" class="'. $fClass .'" '. $fLblRequired .' '. $fLblAttribs .'>';
						if(!empty($field['opt_start'])){
							$_text = (is_array($field['opt_start'])) ? $field['opt_start']['text'] : $field['opt_start'];
							$_value = (is_array($field['opt_start'])) ? $field['opt_start']['value'] : $field['opt_start'];
							//$_html .= '<option value="'. $_value .'">'. $_text .'</option>';

							if(isset($field['multiVal']) && isset($props['group_values']) && isset($props['group_values'][$field['multiVal']]) && is_array($props['group_values'][$field['multiVal']])){
								$_html .= '<option '. ((in_array($_value, $props['group_values'][$field['multiVal']])) ? 'selected="selected"' : '') .' value="'. $_value .'">'. $_text .'</option>';
							}else{
								$_html .= '<option '. (($fValue == $_value) ? 'selected="selected"' : '') .' value="'. $_value .'">'. $_text .'</option>';
							}

						}
						if(isset($field['options'])){
							if(isset($field['while_db'])){ //if using while db
								if($field['while_db']){
									while($opt = $field['options']->fetch_array()){
										if(!empty($field['opt_keys']) && is_array($field['opt_keys'])){
											$_text = $opt[$field['opt_keys']['text']];
											$_value = $opt[$field['opt_keys']['value']];
										}else{
											$_value = $_text = $opt;
										}
										
										if(isset($field['multiVal']) && isset($props['group_values']) && isset($props['group_values'][$field['multiVal']]) && is_array($props['group_values'][$field['multiVal']])){
											$_html .= '<option '. ((in_array($_value, $props['group_values'][$field['multiVal']])) ? 'selected="selected"' : '') .' value="'. $_value .'">'. $_text .'</option>';
										}else{
											$_html .= '<option '. (($fValue == $_value) ? 'selected="selected"' : '') .' value="'. $_value .'">'. $_text .'</option>';
										}
									}
								}
							}else{
								foreach($field['options'] as $opt){
									if(!empty($field['opt_keys']) && is_array($field['opt_keys'])){
										$_text = $opt[$field['opt_keys']['text']];
										$_value = $opt[$field['opt_keys']['value']];
									}else{
										$_value = $_text = $opt;
									}
									
									if(isset($field['multiVal']) && isset($props['group_values']) && isset($props['group_values'][$field['multiVal']]) && is_array($props['group_values'][$field['multiVal']])){
										$_html .= '<option '. ((in_array($_value, $props['group_values'][$field['multiVal']])) ? 'selected="selected"' : '') .' value="'. $_value .'">'. $_text .'</option>';
									}else{
										$_html .= '<option '. (($fValue == $_value) ? 'selected="selected"' : '') .' value="'. $_value .'">'. $_text .'</option>';
									}
								}
							}
						}
					$_html .= '</select>';
					if($fconClass) $_html .= '</div>';
				}
				if($fconClass) $_html .= '</div>'; //end of container
			 }else if($field['type'] == 'checkbox'){
				 if($fconClass) $_html .= '<div class="form-group row">';
				 if($isView){
					 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				 }else{
					 if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					 $fClass = (isset($field['class'])) ? $field['class'] : '';
					 if(!empty($field['options']) && is_array($field['options'])){
						 foreach($field['options'] as $opt){
							 $fLblRequired = isset($opt['required']) ? 'required' : '';
							 $_html .= '<label class="'. $fLblClass .'"><input type="checkbox" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $opt['value'] .'" '. $fLblRequired .' '. $fLblAttribs .'/> '. $opt['label'] .'</label>';
						 }
					 }else{
						 $_html .= '<label class="'. $fLblClass .'"><input type="checkbox" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/> '. $field['label'] .'</label>';
					 }
					 if($fconClass) $_html .= '</div>';
				 }
				 if($fconClass) $_html .= '</div>';
			 }else if($field['type'] == 'password'){
				 if($fconClass) $_html .= '<div class="form-group row">';
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				 }else{
					if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					$_html .= '<input type="password" '. $placeholder .' name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
					if($fconClass) $_html .= '</div>';
				 }
				 if($fconClass) $_html .= '</div>';
			 }else if($field['type'] == 'date' || $field['type'] == 'time'){
				  if($fconClass) $_html .= '<div class="form-group row">';
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				 }else{
					if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					$_html .= '<input type="'. $field['type'] .'" '. $placeholder .' name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
					if($fconClass) $_html .= '</div>';
				 }
				 if($fconClass) $_html .= '</div>';
			 }else if($field['type'] == 'email'){
				 if($fconClass) $_html .= '<div class="form-group row">';
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					$_html .= '<input type="email" '. $placeholder .' name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
					if($fconClass) $_html .= '</div>';
				}
				if($fconClass) $_html .= '</div>';
			 }else if($field['type'] == 'text'){
				 if($fconClass) $_html .= '<div class="form-group row">';
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					$_html .= '<input type="text" '. $placeholder .' name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
					if($fconClass) $_html .= '</div>';
				}
				if($fconClass) $_html .= '</div>';
				
			 }else if($field['type'] == 'number'){
				 if($fconClass) $_html .= '<div class="form-group row">';
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					$_html .= '<input type="number" '. $placeholder .' name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
					if($fconClass) $_html .= '</div>';
				}
				if($fconClass) $_html .= '</div>';
				
			 }else if($field['type'] == 'file'){
				 if($fconClass) $_html .= '<div class="form-group row">';
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					$_html .= '<input type="file" '. $placeholder .' name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
					if($fconClass) $_html .= '</div>';
				}
				if($fconClass) $_html .= '</div>';
			 }else if($field['type'] == 'paragraph'){
				 if($fconClass) $_html .= '<div class="form-group row">';
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					if($fconClass) $_html .= '<div class="'. $fconClass .'">';
					$_html .= '<textarea '. $placeholder .' name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" '. $fLblRequired .' '. $fLblAttribs .'>'. $fValue .'</textarea>';
					if($fconClass) $_html .= '</div>';
				}
				if($fconClass) $_html .= '</div>';
			 }else if($field['type'] == 'label'){
			 	  $_html .= '<label class="'. $fLblClass .'" '. $fLblAttribs .' for="'. $forId .'">'. $field['label'] .'</label>';
			 }else if($field['type'] == 'hidden'){
				$_html .= '<input type="hidden" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';	
			 }else if($field['type'] == 'submit' || $field['type'] == 'button'){
				 $_html .= '<input type="'. $field['type'] .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblAttribs .'/>';
			 }
		}
		 
		 return $_html;
	 }
	 
	 public function fields_builder($fields=array(), $props=array(), $isView=0)
	 {
		 $_html = "";
		 foreach($fields as $field){
			 $row_class = (isset($field['rClass'])) ? 'form-group row ' . $field['rClass'] : 'form-group row';
			 $row_style = (isset($field['rStyle'])) ? $field['rStyle'] : '';
			 if($field['type'] == 'group'){
				  if(!empty($field['begin'])){
					 $_html .= $field['begin']; 
				  }
				  $_html .= '<div class="'. $row_class .'" style="'. $row_style .'">';
				 foreach($field['fields'] as $_field){
				 	 if($_field['type'] =='hidden'){
				 	 	 $_html .= $this->field_constructor($_field, $props, $isView);
				 	 }else{
						 $_html .= '<div class="col-md-'. $_field['gW'] .' frm-field">';
							$_html .= $this->field_constructor($_field, $props, $isView);
						 $_html .= '</div>';
					 }
				 }
				 $_html .= '</div>';
				 if(!empty($field['end'])){
					 $_html .= $field['end']; 
				 }
			 }else if($field['type'] == 'div'){
				 $_html .= $field['content'];
			 }else if($field['type'] == 'hidden'){
			 	 $_html .= $this->field_constructor($field, $props, $isView);
			 }else{
				 $_html .= '<div class="'. $row_class .'" style="'. $row_style .'">';
					$_html .= '<div class="col-md-'. ((!empty($field['gW'])) ? $field['gW'] : '12') .' frm-field">';
						$_html .= $this->field_constructor($field, $props, $isView);
					$_html .= '</div>';
				 $_html .= '</div>';
			 }
		 }
		 
		 return $_html;
	 }
	 
	 /**
	 @param $fields, lists of form fields
	 @param $vals, lists of submitted form fields
	 */
	 public function ready_fields($fields=[], $vals=[])
	 {
		$_list_ready_fields = [];
		if(!empty($fields)){
			foreach($fields as $field){
				if($field['type'] == 'group'){
					foreach($field['fields'] as $fldprop){
						if($fldprop['type'] == 'div' 
							|| $fldprop['type'] == 'button'
							|| $fldprop['type'] == 'submit'
							|| $fldprop['type'] == 'label'
						) continue;
						if(isset($fldprop['save']) && $fldprop['save'] === false) continue;
						
						if(!empty($fldprop['save'])){
							$this->foreign_save_list[$fldprop['name']] = $fldprop['save'];
						}else{
							if(!empty($vals)){
								$_list_ready_fields[$fldprop['name']] = (!empty($vals[$fldprop['name']]) ? $vals[$fldprop['name']] : (isset($fldprop['defval']) ? $fldprop['defval'] : $vals[$fldprop['name']]));
							}else{
								$_list_ready_fields[] = $fldprop['name'];
							}
						}
					}
				}else{
					$fldprop = $field;
					if($fldprop['type'] == 'div' 
						|| $fldprop['type'] == 'button'
						|| $fldprop['type'] == 'submit'
						|| $fldprop['type'] == 'label'
					) continue;
					
					if(isset($fldprop['save']) && $fldprop['save'] === false) continue;
					
					if(!empty($fldprop['save'])){
						$this->foreign_save_list[$fldprop['name']] = $fldprop['save'];
					}else{
						if(!empty($vals)){
							$_list_ready_fields[$fldprop['name']] = (!empty($vals[$fldprop['name']]) ? $vals[$fldprop['name']] : (isset($fldprop['defval']) ? $fldprop['defval'] : $vals[$fldprop['name']]));
						}else{
							$_list_ready_fields[] = $fldprop['name'];
						}
					}
				}
				
			}
		}else if(!empty($vals)){
			foreach($vals as $k => $v){
				$_list_ready_fields[$k] = $v;
			}
		}
		
		$this->all_vals = $vals;
		
		$this->ready_fields_list = $_list_ready_fields;
		
		//Allow to directly access the response with the function
		return $this;
	 }
	 
	 /*
	 @param $tblname, table name where to save the data
	 @param $where, condition statement if the action is update
	 */
	 public function final_save($tblname, $where=[])
	 {
		$resp = $this->_save($tblname, $this->ready_fields_list, $where); 
		
		$this->ready_fields_list = [];
		$this->foreign_save_list = [];
		
		return $resp;
	 }
	 
	 //save form data
	 public function _save($tblname, $fields=[], $where=[])
	 {	 
		 $this->conn_init = $this->Ini()->DB();
		 $db = $this->conn_init->exec();
		 
		 if(!empty($where)){ //then update
			$getCurrent = $db->select()->from($tblname)->where($where)->run()->fetch_array();
			
			if(!empty($getCurrent)){
				 $affected_rows = $db
						->update($tblname)
						->set($fields)
						->where($where)
					->run()->affected_rows();
				
				$this->sub_action_processor($db, 'update', $getCurrent['id']);
				
				if($affected_rows){
					
					$db
						->insert('upsert_logs')
						->data([
							'type' => 'update',
							'table_name' => $tblname,
							'reference_id' => json_encode($where),
							'date_time_log' => $this->global_mod->dateTime(),
							'short_description' => '::USERNAME:: updated the record',
							'long_description' => json_encode($fields),
							'logged_in_user_id' => (!empty($this->current_user)) ? $this->current_user->id : 0,
						])
					->run();
				}
				
				return $affected_rows;
			}
			
			return false;
			
		 }else{ //insert
			 $insert_id = $db
					->insert($tblname)
					->data($fields)
				->run()->insert_id();
			
			if($insert_id){
				
				$this->sub_action_processor($db, 'insert', $insert_id);
				
				$db
					->insert('upsert_logs')
					->data([
						'type' => 'insert',
						'table_name' => $tblname,
						'reference_id' => $insert_id,
						'date_time_log' => $this->global_mod->dateTime(),
						'short_description' => '::USERNAME:: inserted a new record',
						'long_description' => json_encode($fields),
						'logged_in_user_id' => (!empty($this->current_user)) ? $this->current_user->id : 0,
					])
				->run();
			}
			
			return $insert_id;
		 }
	 }
	 
	 private function sub_action_processor($db, $act, $curr_id)
	 {
		 if(!empty($this->foreign_save_list)){
			
			foreach($this->foreign_save_list as $fn => $al){
				//get the value
				$this_val = $this->all_vals[$fn];
				foreach($al as $arg){
					if($act == 'update'){
						$db->delete($arg['t'])->where($arg['fk'], $curr_id)->run();
					}
					//If action is insert
					if(in_array($arg['a'], [$act, 'upsert'])){
						//if function argument is present then prioritize it
						if(isset($arg['f'])) $arg['f']($curr_id, $this_val);
						else{
							if(is_array($this_val)){
								foreach($this_val as $v){
									$atts = [];
									$atts[$arg['fk']] = $curr_id;
									$atts[$arg['fv']] = $v;
									if(!empty($arg['xt'])){
										foreach($arg['xt'] as $xfn => $xfv){
											$atts[$xfn] = $xfv;
										}
									}
									
									$db
										->insert($arg['t'])
										->data($atts)
									->run();
								}
							}else{
								$atts = [];
								$atts[$arg['fk']] = $curr_id;
								$atts[$arg['fv']] = $this->all_vals[$fn];
								if(!empty($arg['xt'])){
									foreach($arg['xt'] as $xfn => $xfv){
										$atts[$xfn] = $xfv;
									}
								}
								
								$db
									->insert($arg['t'])
									->data($atts)
								->run();
							}
						}
					}
				}
			}
		}
	 }
	 
	 //list view builder
	 public function lvBuilder($fields, $data=array(), $loopType='foreach', $footer=array())
	 {
		 $_html = '<ul class="lst-tbl-view">';
			$_html .= '<li class="tbl-row lst-tbl-view-head"><div class="row">';
			foreach($fields as $fld => $props){
				$addClass = (isset($props['hClass'])) ? $props['hClass'] : '';
				$_html .= '<div class="col-md-'. $props['gW'] .' '. $addClass .'" data-field="'. $fld .'">'. $props['label'] .'</div>';
			}
			$_html .= '</div></li>';
			$icc = 0;
			if(!empty($data)){
				if($loopType == 'while_db'){
					if(count($footer)){
						foreach($footer as $ffld=>$ffprop){
							if(!empty($ffprop['trace_value'])){
								$$ffld=array();
							}
						}
					}
					$rowClassName = '';
					while($item = $data->fetch_array()){
						if($icc % 2) $rowClassName = 'lst-tbl-view-item-alt';
						else $rowClassName = 'lst-tbl-view-item';
						$icc++;
						$_html .= '<li class="tbl-row '. $rowClassName .'"><div class="row">';
						foreach($fields as $fld => $props){
							
							$_ival = (isset($props['func'])) ? $props['func']($item) : (isset($item[$fld]) ? $item[$fld] : '');
							
							if(count($footer)){
								foreach($footer as $ffld=>$ffprop){
									$_dfld = $ffld;
									if(isset($ffprop['alias'])) $_dfld = $ffprop['alias'];
									
									if(!empty($ffprop['trace_value']) && $_dfld == $fld){
										if(!empty($ffprop['_index'])){
											$$ffld[$item[$ffprop['_index']]] = $_ival;
										}else{
											$$ffld[] = $_ival;
										}
									}
								}
							}
							
							$addClass = (isset($props['iClass'])) ? $props['iClass'] : '';
							$_html .= '<div class="col-md-'. $props['gW'] .' '. $addClass .'" data-field="'. $fld .'">'. ($_ival) .'</div>';
						}
						$_html .= '</div></li>';
					}
					
					if(count($footer)){
						if($rowClassName == 'lst-tbl-view-item-alt') 
							$rowClassName = 'lst-tbl-view-item';
						else $rowClassName = 'lst-tbl-view-item-alt';
						
						$_html .= '<li class="tbl-row '. $rowClassName .'"><div class="row">';
						foreach($footer as $ffld=>$ffprop){
							$addClass = (isset($ffprop['iClass'])) ? $ffprop['iClass'] : '';
							$ffval = (isset($ffprop['value'])) ? $ffprop['value'] : '';
							if(!empty($ffprop['trace_value'])){
								$_html .= '<div class="col-md-'. $ffprop['gW'] .' '. $addClass .'" data-field="'. $ffld .'">'. ($ffprop['func']($$ffld)) .'</div>';
							}else{
								$_html .= '<div class="col-md-'. $ffprop['gW'] .' '. $addClass .'" data-field="'. $ffld .'">'. $ffval .'</div>';
							}
						}
						$_html .= '</div></li>';
					}
					
				}else{
					foreach($data as $item){
						if($icc % 2) $rowClassName = 'lst-tbl-view-item-alt';
						else $rowClassName = 'lst-tbl-view-item';
						$icc++;
						$_html .= '<li class="tbl-row '. $rowClassName .'"><div class="row">';
						foreach($fields as $fld => $props){
							$addClass = (isset($props['addItemClass'])) ? $props['addItemClass'] : '';
							if(isset($props['func'])){
								$_html .= '<div class="col-md-'. $props['gW'] .' '. $addClass .'" data-field="'. $fld .'">'. ($props['func']($item)) .'</div>';
							}else{
								$_html .= '<div class="col-md-'. $props['gW'] .' '. $addClass .'" data-field="'. $fld .'">'. (isset($item[$fld]) ? $item[$fld] : '') .'</div>';
							}
						}
						$_html .= '</div></li>';
					}
				}
			}
		 $_html .= '</ul>';
		 
		 return $_html;
		 
	 }
 }