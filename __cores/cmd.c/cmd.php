<?php
/**
NOTE: This method is only use for CLI and who have knowledge in building things through commands
*/

define('ROOT_PATH', '../../');

class CMD
{
	private $config_params = [];
	private $argv = [];
	public function __construct($argv = [])
	{
		$this->argv = $argv;
		$this->readConf();
		
		if($this->conf_get('MODE') != 'dev') die('ERR: Current mode is not dev! Please check the documentation before using this method');
		
		if($this->argv[1] == '-builder' && $this->argv[2] == 'c' && !empty($this->argv[3])){
			$this->create_builder();
		}
		
		print_r($this->argv);
		die('ERR: Strange command is not recognized.');
	}
	
	/**
	* @create_builder
	* CMD arguments: 
		* -builder
		* create
		* i.e profile
	*/
	private function create_builder()
	{
		if(is_dir(ROOT_PATH . '__apps/builder/' . $this->argv[3])){
			die('ERR: ' . $this->argv[3] . ' already exist!');
		}
		
		$pathbuilder = ROOT_PATH . '__apps/builder/' . $this->argv[3];
		
		if(!mkdir($pathbuilder, 0777)){
			die('ERR: unable to create builder dir ' . $this->argv[3] . ', please make sure you write it correctly or you have writable permission in your builder directory.');
		}
		
		if(!mkdir($pathbuilder . '/controller', 0777)){
			die('ERR: unable to create builder controller dir under ' . $this->argv[3] . ', please make sure you write it correctly or you have writable permission in your builder directory.');
		}
		
		$tmplt = file_get_contents(dirname(__FILE__) . '/tmp_builder');
		$tmplt = str_replace('[Builder_Name]', ucfirst($this->argv[3]), $tmplt);
		
		file_put_contents($pathbuilder . '/controller/' . $this->argv[3] . '_clr.php', $tmplt);
		
		die('Builder [' . $this->argv[3] . '] is successfully created.');
	}
	
	public function conf_get($_n = null)
	{
		if($_n) return $this->config_params[$_n];
	}
	
	private function conf()
	{
		ob_start();
			include_once ROOT_PATH . ".conf";
			$_confdata = ob_get_contents();
		ob_end_clean();
		
		return $_confdata;
	}
	
	private function readConf()
	{
		$tmpconf = $this->conf();
		$extract_conf = explode('\n', $tmpconf);
		foreach($extract_conf as $_l){
			$_septv = explode('=', $_l);
			if(!empty($_septv[0])) $this->config_params[$_septv[0]] = $_septv[1] ?? '';
		}
	}
}