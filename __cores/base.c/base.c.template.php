<?php #//php_start\\;
	
	/**
	* Template manager
	*/
	
	class Base_Template
	{
		private $globals;
		private $param;
		private $value;
		
		public function __construct($param='', $value ='')
		{
			global $GLOBALS;
			$this->globals = $GLOBALS;
			
			$this->param = $param;
			$this->value = $value;
		}
		
		public function use_prepared($param_prepared, $prepare_tpl){
			
			$template_file="";
			
			$conf_theme = include $this->globals['path']['apps_path'] . '/config/theme.a.c.php';
			
			$tplFile = $conf_theme['common_tpl'];
			
			if($param_prepared != "" && $prepare_tpl !=""){
				
					
					if(isset($_SESSION['_prepared_data_']) && is_array($_SESSION['_prepared_data_']) && count($_SESSION['_prepared_data_'])){
						$prepared_data = array();
						foreach($_SESSION['_prepared_data_'] as $PDKey => $PDValue){
							$prepared_data[$PDKey] = $PDValue;
						}
					}
					
					if(isset($conf_theme['common_assets']) && count($conf_theme['common_assets'])){
						if(isset($conf_theme['common_assets']['css']) && $conf_theme['common_assets']['css'] != ""){
							$assets_css = $conf_theme['common_assets']['css'];
						}
						
						if(isset($conf_theme['common_assets']['js']) && $conf_theme['common_assets']['js'] != ""){
							$assets_js = $conf_theme['common_assets']['js'];
						}
					}
					
					if(isset($conf_theme['static_data']) && count($conf_theme['static_data'])){
						foreach($conf_theme['static_data'] as $cp => $cv)
						{
							$$cp = $cv;				
						}
					}
					
					if(is_array($this->param)){
						foreach($this->param as $p => $v)
						{
							$$p = $v;				
						}
					}else if($this->param !=''){
						$get_na = $this->param;
						$$get_na = $this->value;
					}
					
				ob_start();	
					require_once($this->globals['path']['themes_path'] . '/' . $prepare_tpl . '_tpl.php');
				$prepared_tpl_lo = ob_get_contents();	
				ob_end_clean();
				
				$$param_prepared = $prepared_tpl_lo;
				
				ob_start();	
					if(is_array($tplFile)){
						foreach($tplFile as $file){
							require_once($this->globals['path']['themes_path'] . '/' . $file . '_tpl.php');
						}
					}else{
						require_once($this->globals['path']['themes_path'] . '/' . $tplFile . '_tpl.php');
					}
				$template_file = ob_get_contents();	
				ob_end_clean();
				
			}
			return $template_file;
		}
		
		public function set_template($tplFile =""){
			
			$template_file="";
			
			$conf_theme = include $this->globals['path']['apps_path'] . '/config/theme.a.c.php';
			
			if($tplFile !=""){
					
					if(isset($_SESSION['_prepared_data_']) && is_array($_SESSION['_prepared_data_']) && count($_SESSION['_prepared_data_'])){
						$prepared_data = array();
						foreach($_SESSION['_prepared_data_'] as $PDKey => $PDValue){
							$prepared_data[$PDKey] = $PDValue;
						}
					}
					
					if(isset($conf_theme['common_assets']) && count($conf_theme['common_assets'])){
						if(isset($conf_theme['common_assets']['css']) && $conf_theme['common_assets']['css'] != ""){
							$assets_css = $conf_theme['common_assets']['css'];
						}
						
						if(isset($conf_theme['common_assets']['js']) && $conf_theme['common_assets']['js'] != ""){
							$assets_js = $conf_theme['common_assets']['js'];
						}
					}
					
					if(isset($conf_theme['static_data']) && count($conf_theme['static_data'])){
						foreach($conf_theme['static_data'] as $cp => $cv)
						{
							$$cp = $cv;				
						}
					}
					
					if(is_array($this->param)){
						
						foreach($this->param as $p => $v)
						{
							$$p = $v;				
						}
					}else if($this->param !=''){
						$get_na = $this->param;
						$$get_na = $this->value;
					}
				
				ob_start();
					if(is_array($tplFile)){
						foreach($tplFile as $file){
							require($this->globals['path']['themes_path'] . '/' . $file . '_tpl.php');
						}
					}else{
						require($this->globals['path']['themes_path'] . '/' . $tplFile . '_tpl.php');
					}
				$template_file = ob_get_contents();	
				ob_end_clean();
				
			}
			return $template_file;
			
		}
	}
	
#//php_end\\;?>