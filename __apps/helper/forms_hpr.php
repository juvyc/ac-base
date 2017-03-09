<?php
 class Forms_Hpr extends Base_System
 {
	 public function field_constructor($field=array(), $props=array(), $isView=0)
	 {
		 if(empty($field)) return '';
			 
		 $fName = isset($field['name']) ? $field['name'] : '';
		 $fId = (isset($field['id'])) ? $field['id'] : $fName;
		 
		 if(isset($field['id'])){
		 	 $fValue = (isset($field['value'])) ? $field['value'] : ((isset($props['group_values']) && isset($props['group_values'][$field['id']])) ? $props['group_values'][$field['id']] : '');
		 	 if(isset($field['func'])){
		 	 	 $fValue = $field['func']($fValue);
		 	 }
		 	 $forId =$field['id']; 
		 }else{
		 	 $fValue = (isset($field['value'])) ? $field['value'] : ((isset($props['group_values']) && isset($props['group_values'][$fName])) ? $props['group_values'][$fName] : '');
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
				$_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					$_html .= '<select name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" '. $fLblRequired .' '. $fLblAttribs .'>';
						if(!empty($field['opt_start'])){
							$_text = (is_array($field['opt_start'])) ? $field['opt_start']['text'] : $field['opt_start'];
							$_value = (is_array($field['opt_start'])) ? $field['opt_start']['value'] : $field['opt_start'];
							$_html .= '<option value="'. $_value .'">'. $_text .'</option>';
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
				}
			 }else if($field['type'] == 'checkbox'){
				 if($isView){
					 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				 }else{
					 $fClass = (isset($field['class'])) ? $field['class'] : '';
					 if(!empty($field['options']) && is_array($field['options'])){
						 foreach($field['options'] as $opt){
							 $fLblRequired = isset($opt['required']) ? 'required' : '';
							 $_html .= '<label class="'. $fLblClass .'"><input type="checkbox" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $opt['value'] .'" '. $fLblRequired .' '. $fLblAttribs .'/> '. $opt['label'] .'</label>';
						 }
					 }else{
						 $_html .= '<label class="'. $fLblClass .'"><input type="checkbox" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/> '. $field['label'] .'</label>';
					 }
				 }
			 }else if($field['type'] == 'password'){
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				 }else{
					$_html .= '<input type="password" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
				 }
			 }else if($field['type'] == 'date'){
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				 }else{
					$_html .= '<input type="date" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
				 }
			 }else if($field['type'] == 'email'){
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					$_html .= '<input type="email" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
				}
			 }else if($field['type'] == 'text'){
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					$_html .= '<input type="text" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
				}
			 }else if($field['type'] == 'file'){
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					$_html .= '<input type="file" name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" value="'. $fValue .'" '. $fLblRequired .' '. $fLblAttribs .'/>';
				}
			 }else if($field['type'] == 'paragraph'){
				 $_html .= '<label class="'. $fLblClass .'" for="'. $forId .'">'. $field['label'] .'</label>';
				 if($isView){
					$_html .= '<div id="'. $fId .'" class="'. $fClass .'">'. $fValue .'</div>';
				}else{
					$_html .= '<textarea name="'. $fName .'" id="'. $fId .'" class="'. $fClass .'" '. $fLblRequired .' '. $fLblAttribs .'>'. $fValue .'</textarea>';
				}
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
			 $row_class = (isset($field['rClass'])) ? 'row frm-field-row ' . $field['rClass'] : 'frm-field-row row';
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
						 $_html .= '<div class="col-xs-'. $_field['gW'] .' frm-field">';
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
					$_html .= '<div class="col-xs-'. ((!empty($field['gW'])) ? $field['gW'] : '12') .' frm-field">';
						$_html .= $this->field_constructor($field, $props, $isView);
					$_html .= '</div>';
				 $_html .= '</div>';
			 }
		 }
		 
		 return $_html;
	 }
	 
	 //save form data
	 public function _save($tblname, $fields=array(), $where=array())
	 {
		 $db = $this->Ini()->DB()->exec();
		 if(!empty($where)){ //then update
			 $stmt = $db
					->update($tblname)
					->set($fields)
					->where($where)
				->run();
			return $stmt->affected_rows();
		 }else{ //insert
			 $stmt = $db
					->insert($tblname)
					->data($fields)
				->run();
				
			return $stmt->insert_id();
		 }
	 }
	 
	 //list view builder
	 public function lvBuilder($fields, $data=array(), $loopType='foreach', $footer=array())
	 {
		 $_html = '<ul class="lst-tbl-view">';
			$_html .= '<li class="tbl-row lst-tbl-view-head"><div class="row">';
			foreach($fields as $fld => $props){
				$addClass = (isset($props['hClass'])) ? $props['hClass'] : '';
				$_html .= '<div class="col-xs-'. $props['gW'] .' '. $addClass .'" data-field="'. $fld .'">'. $props['label'] .'</div>';
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
							$_html .= '<div class="col-xs-'. $props['gW'] .' '. $addClass .'" data-field="'. $fld .'">'. ($_ival) .'</div>';
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
								$_html .= '<div class="col-xs-'. $ffprop['gW'] .' '. $addClass .'" data-field="'. $ffld .'">'. ($ffprop['func']($$ffld)) .'</div>';
							}else{
								$_html .= '<div class="col-xs-'. $ffprop['gW'] .' '. $addClass .'" data-field="'. $ffld .'">'. $ffval .'</div>';
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
								$_html .= '<div class="col-xs-'. $props['gW'] .' '. $addClass .'" data-field="'. $fld .'">'. ($props['func']($item)) .'</div>';
							}else{
								$_html .= '<div class="col-xs-'. $props['gW'] .' '. $addClass .'" data-field="'. $fld .'">'. (isset($item[$fld]) ? $item[$fld] : '') .'</div>';
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