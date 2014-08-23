<?php #//php_start\\;	
	
	class _SYSTEM
	{	
		private $DB;
		private $Uri;
		private $config;
		private $globals = array();
		private $static_routes = array();
		private $builder;
		private $inclr_base_segment;
		private $inclr_end_segment;
		
		public function _SYSTEM()
		{
			global $GLOBALS;
			
			$this->globals = $GLOBALS;
			
			$this->config = new _CONFIG();
			
			/**
			* Checking if the database is applicable
			*/
			if($this->config->enable_database){ 
				/**
				* Load the database if it is required for this system
				*/				
				require_once($this->globals['path']['cores_path'] . '/db.c/db.c.php');
				
				/**
				* Then assign the db to a substitute parameters
				*/
				$this->DB = new _DB();			
			}
			
			/**
			* @Object _Uri - a url request manager, it helps the system to divide the request url after the base path
			*/
			$this->Uri = new _Uri();
			
			/**
			* Execute the caller of the static routes
			*/
			$this->static_routes = $this->_raw_routes();
			
			/**
			* Assign the builder object to variable
			*/
			
			$this->builder = new _Builder();
			
			new _Autoload();
		}
		
		public function application()
		{
			/**
			* BELOW IS THE SAMPLE DATABASE QUERY
			*
			
			$stmt = $this
					->DB
							->exec()
								->select("*")
								->from('zip_code')
										->where(array('zip_code_id'=>1))
					->run();
										
			return var_dump($stmt->fetch_object());							
										exit;
			*/
		
		
			/**
			* The application loader
			*/
			return $this->_request_analyzer();
		}
		
		/**
		* This is the request analyzer, it check if the request is 
		* still exists in builder
		*/
		public function _request_analyzer()
		{
			/**
			* Checking if the default routes, if they're not exist system will not continue
			*/
			if(!isset($this->static_routes['_404_']) OR !isset($this->static_routes['_root_'])) $this->_critical_exit();
			
			$is_home = false;
			
			if($this->Uri->get_segment(1) != ""){
				/**
				* If there's a first segment assign on the request
				*/
				
				if($this->_route_preg_match(implode('/', $this->Uri->get_segments()))){
						/**
						* if there's a reg expression, route is also is change to the module of the found reg exp
						*/
						$this->_route_preg_match(implode('/', $this->Uri->get_segments()));
						/**
						*	checking if the controller is exist, if not return the route to the default
						*/
						
						if(!$this->builder->_checker()){
							$this->builder->route = $this->static_routes['_root_'];
							$is_home = true;
						}
				}else{
					
					$this->builder->route = $this->builder->_clean_segment($this->Uri->get_segment(1));
					
					$this->first_segment_request_manager();
					if(!$this->builder->_caller()){
						$this->builder->route = $this->static_routes['_root_'];
						$is_home = true;
					}
				}
				
				
			}else if($this->static_routes['_root_'] != ""){
				/**
				* If there's no first segment assign on the request
				*/
				
				$this->builder->route = $this->static_routes['_root_'];				
				$is_home = true;
				
			}else{
				$this->_critical_exit();
			}
			
			return $this->_request_reader($is_home);
		}
		
		/**
		* @method _request_watcher - the request reader
		*/
		public function _request_reader($is_home = false)
		{
				$this->first_segment_request_manager();
				
				$this->last_segment = $this->builder->_clean_segment($this->Uri->get_segment(count($this->Uri->get_segments())));
				
				if($this->builder->_caller()){
					$current = $this->builder->_caller();
					$methodVariable = array($current, 'action_index');
					
					
					if($this->builder->fix_fn != ""){
						if(count($this->builder->static_segments) && isset($this->builder->static_segments[1]) && method_exists($current, 'action_' . $this->builder->static_segments[1])){
								$output = "";
								
								/**
								* detect the load before function
								*/
								if(method_exists($current, 'load_before')){
									$sub_method_before = array($current, 'load_before');
									$output .= call_user_func_array($sub_method_before, array());
								}
								
								$sub_method = array($current, 'action_' . $this->builder->static_segments[1]);
								$output .= call_user_func_array($sub_method, array());
								
								/**
								* detect the load after function
								*/
								if(method_exists($current, 'load_after')){
									$sub_method_before = array($current, 'load_after');
									$output .= call_user_func_array($sub_method_before, array());
								}
								
								return $output;
						}else if(!count($this->builder->static_segments) && method_exists($current, 'action_' . $this->builder->fix_fn)){
								
								$output = "";
								
								/**
								* detect the load before function
								*/
								if(method_exists($current, 'load_before')){
									$sub_method_before = array($current, 'load_before');
									$output .= call_user_func_array($sub_method_before, array());
								}
								
								$sub_method = array($current, 'action_' . $this->builder->fix_fn);
								$output .= call_user_func_array($sub_method, array());
								
								/**
								* detect the load after function
								*/
								if(method_exists($current, 'load_after')){
									$sub_method_before = array($current, 'load_after');
									$output .= call_user_func_array($sub_method_before, array());
								}
								
								return $output;
						}else if(method_exists($current, 'load_404')){
								
								/**
								* if _404 method is inside the class of the current requested segment
								* then the second requested segment is not exist
								* the inhireted 404 will return
								*/
								header("HTTP/1.0 404 Page not found!");							
								
								$sub_method = array($current, 'load_404');
								
								$output = "";
								
								/**
								* detect the load before function
								*/
								if(method_exists($current, 'load_before')){
									$sub_method_before = array($current, 'load_before');
									$output .= call_user_func_array($sub_method_before, array());
								}
								
								$output .= call_user_func_array($sub_method, array());
								
								
								/**
								* detect the load after function
								*/
								if(method_exists($current, 'load_after')){
									$sub_method_before = array($current, 'load_after');
									$output .= call_user_func_array($sub_method_before, array());
								}
								
								return $output;
								
						}else{
								/**
								* If request doesn't match any condition above
								* 404 must be call using below conditions
 								*/
								header("HTTP/1.0 404 Page not found!");
								
								/**
								* re-route the request to 404 instead of the current Uri segment
								*/
								$this->builder->route = $this->static_routes['_404_'];
								
								/**
								* Check the 404 file it in the builder
								*/
								$this->first_segment_request_manager();
								
								/**
								* Check the class if it is available, this is case it is calling the 404 class name
								*/
								if($this->builder->_caller()){
									/**
									* 404 is just like the other builders, it calls first the _index, it's class self constructor
									*/
									$current = $this->builder->_caller();
									$methodVariable = array($current, 'action_index');
									
									/**
									* Check if _index (the builder common constructor is exist)
									*/
									if(method_exists($current, 'action_index')){
										/**
										* Assign the current requested segment to a variable 
										* then pass it to the base class
										*/
										$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
										$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
										
										$output = "";
								
										/**
										* detect the load before function
										*/
										if(method_exists($current, 'load_before')){
											$sub_method_before = array($current, 'load_before');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										$output .= call_user_func_array($methodVariable, array(&$a, &$b));
										
										
										/**
										* detect the load after function
										*/
										if(method_exists($current, 'load_after')){
											$sub_method_before = array($current, 'load_after');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										return $output;
										
									}else{
										/**
										* if it is not exist, return a plane 404 message
										*/
										return '404: Page not found!';
									}
								}else{
									/**
									* if it is not exist, return a plane 404 message
									*/
									return '404: Page not found!';
								}
						}
					}else{
						/**
						* Check if the second segment is not null
						*/
						
						if($this->builder->ishome){
							$rr = "";
						}else if(count($this->builder->static_segments)){
							$rr = $this->builder->static_segments[1];
						}else{
							if(!$is_home){
								$rr = $this->Uri->get_segment(2);
								$this->inclr_base_segment = $this->Uri->get_segment(2);
								$this->inclr_end_segment = $this->Uri->get_segment(3);
							}else{
								$rr = $this->Uri->get_segment(1);
								$this->inclr_base_segment = $this->Uri->get_segment(1);
								$this->inclr_end_segment = $this->Uri->get_segment(2);
							}
						}
						
						if($rr != ""){
							/**
							* if not null, call the method base on the request 
							*/
														
							$rr = str_replace('-', '_', $rr);
							
							$sub_method = array($current, 'action_' . $rr);
							if(method_exists($current, 'action_' . $rr)){
								/**
								* if current requested segment is exist, then it call the method
								*/
								$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
								$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
								//return call_user_func_array($sub_method, array(&$a, &$b));
								
								$output = "";
								
								/**
								* detect the load before function
								*/
								if(method_exists($current, 'load_before')){
									$sub_method_before = array($current, 'load_before');
									$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
								}
								
								
								$output .= call_user_func_array($sub_method, array(&$a, &$b));
								
								/**
								* detect the load after function
								*/
								if(method_exists($current, 'load_after')){
									$sub_method_before = array($current, 'load_after');
									$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
								}
								
								return $output;
							
							//-------------------------------------------------------------------------------/
							//--FOR OTHER FILE NAME CONTROLLER READER #START#--//
							//-------------------------------------------------------------------------------/
							}else if($this->inclr_base_segment != "" && $this->builder->_checker($this->inclr_base_segment)){
								require_once($this->builder->bldr_file);
								$current = $this->builder->_caller($this->inclr_base_segment);
								if($current){
									if($this->inclr_end_segment != ""){
										$rr = $this->inclr_end_segment;
									}else{
										$rr = 'index';
									}
									
									$rr = str_replace('-', '_', $rr);
									$sub_method = array($current, 'action_' . $rr);
									if(method_exists($current, 'action_' . $rr)){
										/**
										* if current requested segment is exist, then it call the method
										*/
										$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
										$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
										//return call_user_func_array($sub_method, array(&$a, &$b));
										
										$output = "";
										
										/**
										* detect the load before function
										*/
										if(method_exists($current, 'load_before')){
											$sub_method_before = array($current, 'load_before');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										
										$output .= call_user_func_array($sub_method, array(&$a, &$b));
										
										/**
										* detect the load after function
										*/
										if(method_exists($current, 'load_after')){
											$sub_method_before = array($current, 'load_after');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										return $output;
									}else if(method_exists($current, 'load_404')){
										/**
										* if _404 method is inside the class of the current requested segment
										* then the second requested segment is not exist
										* the inhireted 404 will return
										*/
										header("HTTP/1.0 404 Page not found!");							
										$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
										$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
										$sub_method = array($current, 'load_404');
										
										$output = "";
										
										/**
										* detect the load before function
										*/
										if(method_exists($current, 'load_before')){
											$sub_method_before = array($current, 'load_before');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										$output .= call_user_func_array($sub_method, array(&$a, &$b));
										
										
										/**
										* detect the load after function
										*/
										if(method_exists($current, 'load_after')){
											$sub_method_before = array($current, 'load_after');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										return $output;
										
									}else{
										/**
										* If request doesn't match any condition above
										* 404 must be call using below conditions
										*/
										header("HTTP/1.0 404 Page not found!");
										
										/**
										* re-route the request to 404 instead of the current Uri segment
										*/
										$this->builder->route = $this->static_routes['_404_'];
										
										/**
										* Check the 404 file it in the builder
										*/
										$this->first_segment_request_manager();
										
										/**
										* Check the class if it is available, this is case it is calling the 404 class name
										*/
										if($this->builder->_caller()){
											/**
											* 404 is just like the other builders, it calls first the _index, it's class self constructor
											*/
											$current = $this->builder->_caller();
											$methodVariable = array($current, 'action_index');
											
											/**
											* Check if _index (the builder common constructor is exist)
											*/
											if(method_exists($current, 'action_index')){
												/**
												* Assign the current requested segment to a variable 
												* then pass it to the base class
												*/
												$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
												$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
												
												$output = "";
										
												/**
												* detect the load before function
												*/
												if(method_exists($current, 'load_before')){
													$sub_method_before = array($current, 'load_before');
													$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
												}
												
												$output .= call_user_func_array($methodVariable, array(&$a, &$b));
												
												
												/**
												* detect the load after function
												*/
												if(method_exists($current, 'load_after')){
													$sub_method_before = array($current, 'load_after');
													$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
												}
												
												return $output;
												
											}else{
												/**
												* if it is not exist, return a plane 404 message
												*/
												return '404: Page not found!';
											}
										}else{
											/**
											* if it is not exist, return a plane 404 message
											*/
											return '404: Page not found!';
										}
									}
								}else{
									/**
									* If request doesn't match any condition above
									* 404 must be call using below conditions
									*/
									header("HTTP/1.0 404 Page not found!");
									
									/**
									* re-route the request to 404 instead of the current Uri segment
									*/
									$this->builder->route = $this->static_routes['_404_'];
									
									/**
									* Check the 404 file it in the builder
									*/
									$this->first_segment_request_manager();
									
									/**
									* Check the class if it is available, this is case it is calling the 404 class name
									*/
									if($this->builder->_caller()){
										/**
										* 404 is just like the other builders, it calls first the _index, it's class self constructor
										*/
										$current = $this->builder->_caller();
										$methodVariable = array($current, 'action_index');
										
										/**
										* Check if _index (the builder common constructor is exist)
										*/
										if(method_exists($current, 'action_index')){
											/**
											* Assign the current requested segment to a variable 
											* then pass it to the base class
											*/
											$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
											$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
											
											$output = "";
									
											/**
											* detect the load before function
											*/
											if(method_exists($current, 'load_before')){
												$sub_method_before = array($current, 'load_before');
												$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
											}
											
											$output .= call_user_func_array($methodVariable, array(&$a, &$b));
											
											
											/**
											* detect the load after function
											*/
											if(method_exists($current, 'load_after')){
												$sub_method_before = array($current, 'load_after');
												$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
											}
											
											return $output;
											
										}else{
											/**
											* if it is not exist, return a plane 404 message
											*/
											return '404: Page not found!';
										}
									}else{
										/**
										* if it is not exist, return a plane 404 message
										*/
										return '404: Page not found!';
									}
								}
							//-------------------------------------------------------------------------------/
							//--FOR OTHER FILE NAME CONTROLLER READER #END#--//
							//-------------------------------------------------------------------------------/
							}else if($this->builder->_checker($this->Uri->get_segment(1) . '/' . $this->builder->route)){
								require_once($this->builder->bldr_file);
								$sub_ctrl = $this->builder->route . '_' . ucfirst($this->Uri->get_segment(1));
								if($sub_clr_class = $this->builder->_caller($sub_ctrl)){
									if($this->Uri->get_segment(2) != ""){
										$sub_ext_route = $this->Uri->get_segment(2);
										if(method_exists($sub_clr_class, 'action_' . $sub_ext_route)){
											$sub_method = array($sub_clr_class, 'action_' . $sub_ext_route);
											
											/**
											* if current requested segment is exist, then it call the method
											*/
											$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
											$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
											
											
											$output = "";
											
											/**
											* detect the load before function
											*/
											if(method_exists($sub_clr_class, 'load_before')){
												$sub_method_before = array($sub_clr_class, 'load_before');
												$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
											}
											
											
											$output .= call_user_func_array($sub_method, array(&$a, &$b));
											
											/**
											* detect the load after function
											*/
											if(method_exists($sub_clr_class, 'load_after')){
												$sub_method_before = array($sub_clr_class, 'load_after');
												$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
											}
											
											return $output;
											
										}else if($this->builder->_checker($this->Uri->get_segment(1) . '/' . $sub_ext_route, $sub_ext_route)){
											require_once($this->builder->bldr_file);
											$sub_ctrl = $this->builder->route . '_' . ucfirst($this->Uri->get_segment(1));
											if($sub_clr_class = $this->builder->_caller($sub_ctrl)){
												$sub_ext_route = $this->Uri->get_segment(3);
												if($sub_ext_route != ""){
													if(method_exists($sub_clr_class, 'action_' . $sub_ext_route)){
														$sub_method = array($sub_clr_class, 'action_' . $sub_ext_route);
														
														/**
														* if current requested segment is exist, then it call the method
														*/
														$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
														$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
														
														
														$output = "";
														
														/**
														* detect the load before function
														*/
														if(method_exists($sub_clr_class, 'load_before')){
															$sub_method_before = array($sub_clr_class, 'load_before');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														
														$output .= call_user_func_array($sub_method, array(&$a, &$b));
														
														/**
														* detect the load after function
														*/
														if(method_exists($sub_clr_class, 'load_after')){
															$sub_method_before = array($sub_clr_class, 'load_after');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														return $output;
													}else if($this->builder->_checker($this->Uri->get_segment(1) . '/' . $sub_ext_route, $this->Uri->get_segment(2))){
														
														require_once($this->builder->bldr_file);
														
														$sub_sub_class = ucfirst($this->Uri->get_segment(3)) . '_' . ucfirst($this->Uri->get_segment(1));
														if($sub_clr_class = $this->builder->_caller($sub_sub_class)){
															$sub_ext_route = $this->Uri->get_segment(4);
															if($sub_ext_route != ""){
																if(method_exists($sub_clr_class, 'action_' . $sub_ext_route)){
																	$sub_method = array($sub_clr_class, 'action_' . $sub_ext_route);
																	
																	/**
																	* if current requested segment is exist, then it call the method
																	*/
																	$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
																	$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
																	
																	
																	$output = "";
																	
																	/**
																	* detect the load before function
																	*/
																	if(method_exists($sub_clr_class, 'load_before')){
																		$sub_method_before = array($sub_clr_class, 'load_before');
																		$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																	}
																	
																	
																	$output .= call_user_func_array($sub_method, array(&$a, &$b));
																	
																	/**
																	* detect the load after function
																	*/
																	if(method_exists($sub_clr_class, 'load_after')){
																		$sub_method_before = array($sub_clr_class, 'load_after');
																		$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																	}
																	
																	return $output;
																}else if(method_exists($sub_clr_class, 'load_404')){
														
																	header("HTTP/1.0 404 Page not found!");
																	
																	$sub_method = array($sub_clr_class, 'load_404');
																	
																	/**
																	* if current requested segment is exist, then it call the method
																	*/
																	$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
																	$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
																	
																	
																	$output = "";
																	
																	/**
																	* detect the load before function
																	*/
																	if(method_exists($sub_clr_class, 'load_before')){
																		$sub_method_before = array($sub_clr_class, 'load_before');
																		$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																	}
																	
																	
																	$output .= call_user_func_array($sub_method, array(&$a, &$b));
																	
																	/**
																	* detect the load after function
																	*/
																	if(method_exists($sub_clr_class, 'load_after')){
																		$sub_method_before = array($sub_clr_class, 'load_after');
																		$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																	}
																	
																	return $output;
																}else if(isset($this->static_routes['_404_']) && $this->static_routes['_404_'] != ""){
																	header("HTTP/1.0 404 Page not found!");
																	$extracted_404 = explode('/', $this->static_routes['_404_']);
																	$this->builder->route = $extracted_404[0];
																	if($this->builder->_checker()){
																		if(!$this->builder->_caller())
																			require_once($this->builder->bldr_file);
																		
																		
																		$sub_clr_class = $this->builder->_caller();
																		
																		if(isset($extracted_404[1]) && $extracted_404[1] != "")
																			$sub_method = array($sub_clr_class, 'action_' . $extracted_404[1]);
																		else
																			$sub_method = array($sub_clr_class, 'action_index');
																	
																		/**
																		* if current requested segment is exist, then it call the method
																		*/
																		$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
																		$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
																		
																		
																		$output = "";
																		
																		/**
																		* detect the load before function
																		*/
																		if(method_exists($sub_clr_class, 'load_before')){
																			$sub_method_before = array($sub_clr_class, 'load_before');
																			$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																		}
																		
																		
																		$output .= call_user_func_array($sub_method, array(&$a, &$b));
																		
																		/**
																		* detect the load after function
																		*/
																		if(method_exists($sub_clr_class, 'load_after')){
																			$sub_method_before = array($sub_clr_class, 'load_after');
																			$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																		}
																		
																		return $output;
																		
																	}
																}
															}else if(method_exists($sub_clr_class, 'action_index')){
															
																$sub_method = array($sub_clr_class, 'action_index');
																
																/**
																* if current requested segment is exist, then it call the method
																*/
																$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
																$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
																
																
																$output = "";
																
																/**
																* detect the load before function
																*/
																if(method_exists($sub_clr_class, 'load_before')){
																	$sub_method_before = array($sub_clr_class, 'load_before');
																	$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																}
																
																
																$output .= call_user_func_array($sub_method, array(&$a, &$b));
																
																/**
																* detect the load after function
																*/
																if(method_exists($sub_clr_class, 'load_after')){
																	$sub_method_before = array($sub_clr_class, 'load_after');
																	$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																}
																
																return $output;
															}else if(method_exists($sub_clr_class, 'load_404')){
																
																header("HTTP/1.0 404 Page not found!");
																
																$sub_method = array($sub_clr_class, 'load_404');
																
																/**
																* if current requested segment is exist, then it call the method
																*/
																$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
																$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
																
																
																$output = "";
																
																/**
																* detect the load before function
																*/
																if(method_exists($sub_clr_class, 'load_before')){
																	$sub_method_before = array($sub_clr_class, 'load_before');
																	$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																}
																
																
																$output .= call_user_func_array($sub_method, array(&$a, &$b));
																
																/**
																* detect the load after function
																*/
																if(method_exists($sub_clr_class, 'load_after')){
																	$sub_method_before = array($sub_clr_class, 'load_after');
																	$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																}
																
																return $output;
															}else if(isset($this->static_routes['_404_']) && $this->static_routes['_404_'] != ""){
																header("HTTP/1.0 404 Page not found!");
																$extracted_404 = explode('/', $this->static_routes['_404_']);
																$this->builder->route = $extracted_404[0];
																if($this->builder->_checker()){
																	if(!$this->builder->_caller())
																		require_once($this->builder->bldr_file);
																	
																	
																	$sub_clr_class = $this->builder->_caller();
																	
																	if(isset($extracted_404[1]) && $extracted_404[1] != "")
																		$sub_method = array($sub_clr_class, 'action_' . $extracted_404[1]);
																	else
																		$sub_method = array($sub_clr_class, 'action_index');
																
																	/**
																	* if current requested segment is exist, then it call the method
																	*/
																	$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
																	$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
																	
																	
																	$output = "";
																	
																	/**
																	* detect the load before function
																	*/
																	if(method_exists($sub_clr_class, 'load_before')){
																		$sub_method_before = array($sub_clr_class, 'load_before');
																		$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																	}
																	
																	
																	$output .= call_user_func_array($sub_method, array(&$a, &$b));
																	
																	/**
																	* detect the load after function
																	*/
																	if(method_exists($sub_clr_class, 'load_after')){
																		$sub_method_before = array($sub_clr_class, 'load_after');
																		$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
																	}
																	
																	return $output;
																	
																}
															}
														}else{
															$this->_critical_exit();
														}
														
														//--------------------------------------------------------------------------
														//--------------------------------------------------------------------------
														//--------------------------------------------------------------------------
														#DEV--------------------------------------------------------------------------
														#DEV--------------------------------------------------------------------------
														#DEV--------------------------------------------------------------------------
														//--------------------------------------------------------------------------
														//--------------------------------------------------------------------------
														//--------------------------------------------------------------------------
													}else if(method_exists($sub_clr_class, 'load_404')){
														
														header("HTTP/1.0 404 Page not found!");
														
														$sub_method = array($sub_clr_class, 'load_404');
														
														/**
														* if current requested segment is exist, then it call the method
														*/
														$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
														$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
														
														
														$output = "";
														
														/**
														* detect the load before function
														*/
														if(method_exists($sub_clr_class, 'load_before')){
															$sub_method_before = array($sub_clr_class, 'load_before');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														
														$output .= call_user_func_array($sub_method, array(&$a, &$b));
														
														/**
														* detect the load after function
														*/
														if(method_exists($sub_clr_class, 'load_after')){
															$sub_method_before = array($sub_clr_class, 'load_after');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														return $output;
													}else if(isset($this->static_routes['_404_']) && $this->static_routes['_404_'] != ""){
														header("HTTP/1.0 404 Page not found!");
														$extracted_404 = explode('/', $this->static_routes['_404_']);
														$this->builder->route = $extracted_404[0];
														if($this->builder->_checker()){
															if(!$this->builder->_caller())
																require_once($this->builder->bldr_file);
															
															
															$sub_clr_class = $this->builder->_caller();
															
															if(isset($extracted_404[1]) && $extracted_404[1] != "")
																$sub_method = array($sub_clr_class, 'action_' . $extracted_404[1]);
															else
																$sub_method = array($sub_clr_class, 'action_index');
														
															/**
															* if current requested segment is exist, then it call the method
															*/
															$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
															$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
															
															
															$output = "";
															
															/**
															* detect the load before function
															*/
															if(method_exists($sub_clr_class, 'load_before')){
																$sub_method_before = array($sub_clr_class, 'load_before');
																$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
															}
															
															
															$output .= call_user_func_array($sub_method, array(&$a, &$b));
															
															/**
															* detect the load after function
															*/
															if(method_exists($sub_clr_class, 'load_after')){
																$sub_method_before = array($sub_clr_class, 'load_after');
																$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
															}
															
															return $output;
															
														}
													}
												}else if(method_exists($sub_clr_class, 'action_index')){
														$sub_method = array($sub_clr_class, 'action_index');
														
														/**
														* if current requested segment is exist, then it call the method
														*/
														$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
														$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
														
														
														$output = "";
														
														/**
														* detect the load before function
														*/
														if(method_exists($sub_clr_class, 'load_before')){
															$sub_method_before = array($sub_clr_class, 'load_before');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														
														$output .= call_user_func_array($sub_method, array(&$a, &$b));
														
														/**
														* detect the load after function
														*/
														if(method_exists($sub_clr_class, 'load_after')){
															$sub_method_before = array($sub_clr_class, 'load_after');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														return $output;
												}else if(isset($this->static_routes['_404_']) && $this->static_routes['_404_'] != ""){
														header("HTTP/1.0 404 Page not found!");
														$extracted_404 = explode('/', $this->static_routes['_404_']);
														$this->builder->route = $extracted_404[0];
														if($this->builder->_checker()){
															if(!$this->builder->_caller())
																require_once($this->builder->bldr_file);
															
															
															$sub_clr_class = $this->builder->_caller();
															
															if(isset($extracted_404[1]) && $extracted_404[1] != "")
																$sub_method = array($sub_clr_class, 'action_' . $extracted_404[1]);
															else
																$sub_method = array($sub_clr_class, 'action_index');
														
															/**
															* if current requested segment is exist, then it call the method
															*/
															$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
															$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
															
															
															$output = "";
															
															/**
															* detect the load before function
															*/
															if(method_exists($sub_clr_class, 'load_before')){
																$sub_method_before = array($sub_clr_class, 'load_before');
																$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
															}
															
															
															$output .= call_user_func_array($sub_method, array(&$a, &$b));
															
															/**
															* detect the load after function
															*/
															if(method_exists($sub_clr_class, 'load_after')){
																$sub_method_before = array($sub_clr_class, 'load_after');
																$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
															}
															
															return $output;
															
														}
												}
												
											}
										}else{
											########################################################
											########################################################
											########################################################
											//Return 404 error here
											if(method_exists($sub_clr_class, 'load_404')){
														header("HTTP/1.0 404 Page not found!");
														
														$sub_method = array($sub_clr_class, 'load_404');
														
														/**
														* if current requested segment is exist, then it call the method
														*/
														$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
														$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
														
														
														$output = "";
														
														/**
														* detect the load before function
														*/
														if(method_exists($sub_clr_class, 'load_before')){
															$sub_method_before = array($sub_clr_class, 'load_before');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														
														$output .= call_user_func_array($sub_method, array(&$a, &$b));
														
														/**
														* detect the load after function
														*/
														if(method_exists($sub_clr_class, 'load_after')){
															$sub_method_before = array($sub_clr_class, 'load_after');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														return $output;
												
												
											}else{
												if(isset($this->static_routes['_404_']) && $this->static_routes['_404_'] != ""){
														header("HTTP/1.0 404 Page not found!");
														$extracted_404 = explode('/', $this->static_routes['_404_']);
														$this->builder->route = $extracted_404[0];
														if($this->builder->_checker()){
															if(!$this->builder->_caller())
																require_once($this->builder->bldr_file);
															
															
															$sub_clr_class = $this->builder->_caller();
															
															if(isset($extracted_404[1]) && $extracted_404[1] != "")
																$sub_method = array($sub_clr_class, 'action_' . $extracted_404[1]);
															else
																$sub_method = array($sub_clr_class, 'action_index');
														
															/**
															* if current requested segment is exist, then it call the method
															*/
															$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
															$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
															
															
															$output = "";
															
															/**
															* detect the load before function
															*/
															if(method_exists($sub_clr_class, 'load_before')){
																$sub_method_before = array($sub_clr_class, 'load_before');
																$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
															}
															
															
															$output .= call_user_func_array($sub_method, array(&$a, &$b));
															
															/**
															* detect the load after function
															*/
															if(method_exists($sub_clr_class, 'load_after')){
																$sub_method_before = array($sub_clr_class, 'load_after');
																$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
															}
															
															return $output;
															
														}
												}
											}
										}
									}else if(method_exists($sub_clr_class, 'action_index')){
														$sub_method = array($sub_clr_class, 'action_index');
														
														/**
														* if current requested segment is exist, then it call the method
														*/
														$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
														$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
														
														
														$output = "";
														
														/**
														* detect the load before function
														*/
														if(method_exists($sub_clr_class, 'load_before')){
															$sub_method_before = array($sub_clr_class, 'load_before');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														
														$output .= call_user_func_array($sub_method, array(&$a, &$b));
														
														/**
														* detect the load after function
														*/
														if(method_exists($sub_clr_class, 'load_after')){
															$sub_method_before = array($sub_clr_class, 'load_after');
															$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
														}
														
														return $output;
									}else if(isset($this->static_routes['_404_']) && $this->static_routes['_404_'] != ""){
														header("HTTP/1.0 404 Page not found!");
														$extracted_404 = explode('/', $this->static_routes['_404_']);
														$this->builder->route = $extracted_404[0];
														if($this->builder->_checker()){
															if(!$this->builder->_caller())
																require_once($this->builder->bldr_file);
															
															
															$sub_clr_class = $this->builder->_caller();
															
															if(isset($extracted_404[1]) && $extracted_404[1] != "")
																$sub_method = array($sub_clr_class, 'action_' . $extracted_404[1]);
															else
																$sub_method = array($sub_clr_class, 'action_index');
														
															/**
															* if current requested segment is exist, then it call the method
															*/
															$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
															$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
															
															
															$output = "";
															
															/**
															* detect the load before function
															*/
															if(method_exists($sub_clr_class, 'load_before')){
																$sub_method_before = array($sub_clr_class, 'load_before');
																$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
															}
															
															
															$output .= call_user_func_array($sub_method, array(&$a, &$b));
															
															/**
															* detect the load after function
															*/
															if(method_exists($sub_clr_class, 'load_after')){
																$sub_method_before = array($sub_clr_class, 'load_after');
																$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
															}
															
															return $output;
															
														}
									}
								}
								
								
							}else if(method_exists($current, 'load_404')){
								/**
								* if _404 method is inside the class of the current requested segment
								* then the second requested segment is not exist
								* the inhireted 404 will return
								*/
								header("HTTP/1.0 404 Page not found!");							
								$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
								$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
								$sub_method = array($current, 'load_404');
								
								$output = "";
								
								/**
								* detect the load before function
								*/
								if(method_exists($current, 'load_before')){
									$sub_method_before = array($current, 'load_before');
									$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
								}
								
								$output .= call_user_func_array($sub_method, array(&$a, &$b));
								
								
								/**
								* detect the load after function
								*/
								if(method_exists($current, 'load_after')){
									$sub_method_before = array($current, 'load_after');
									$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
								}
								
								return $output;
								
							}else{
								/**
								* If request doesn't match any condition above
								* 404 must be call using below conditions
 								*/
								header("HTTP/1.0 404 Page not found!");
								
								/**
								* re-route the request to 404 instead of the current Uri segment
								*/
								$this->builder->route = $this->static_routes['_404_'];
								
								/**
								* Check the 404 file it in the builder
								*/
								$this->first_segment_request_manager();
								
								/**
								* Check the class if it is available, this is case it is calling the 404 class name
								*/
								if($this->builder->_caller()){
									/**
									* 404 is just like the other builders, it calls first the _index, it's class self constructor
									*/
									$current = $this->builder->_caller();
									$methodVariable = array($current, 'action_index');
									
									/**
									* Check if _index (the builder common constructor is exist)
									*/
									if(method_exists($current, 'action_index')){
										/**
										* Assign the current requested segment to a variable 
										* then pass it to the base class
										*/
										$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
										$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
										
										$output = "";
								
										/**
										* detect the load before function
										*/
										if(method_exists($current, 'load_before')){
											$sub_method_before = array($current, 'load_before');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										$output .= call_user_func_array($methodVariable, array(&$a, &$b));
										
										
										/**
										* detect the load after function
										*/
										if(method_exists($current, 'load_after')){
											$sub_method_before = array($current, 'load_after');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										return $output;
										
									}else{
										/**
										* if it is not exist, return a plane 404 message
										*/
										return '404: Page not found!';
									}
								}else{
									/**
									* if it is not exist, return a plane 404 message
									*/
									return '404: Page not found!';
								}
							}
							
						}else{
							
							if(method_exists($current, 'action_index')){
								/**
								* call the default method
								*/
								$a = 'root';
								$b = $this->Uri->get_segment(1);
								
								$output = "";
									
								/**
								* detect the load before function
								*/
								if(method_exists($current, 'load_before')){
									$sub_method_before = array($current, 'load_before');
									$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
								}
								
								$output .= call_user_func_array($methodVariable, array(&$a, &$b));
								
								/**
								* detect the load after function
								*/
								
								if(method_exists($current, 'load_after')){
									$sub_method_after = array($current, 'load_after');
									$output .= call_user_func_array($sub_method_after, array(&$a, &$b));
								}
								
								return $output;
								
							}else if(method_exists($current, 'load_404')){
								/**
								* if _404 method is inside the class of the current requested segment
								* then the second requested segment is not exist
								* the inhireted 404 will return
								*/
								header("HTTP/1.0 404 Page not found!");							
								$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
								$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
								$sub_method = array($current, 'load_404');
								
								$output = "";
								
								/**
								* detect the load before function
								*/
								if(method_exists($current, 'load_before')){
									$sub_method_before = array($current, 'load_before');
									$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
								}
								
								$output .= call_user_func_array($sub_method, array(&$a, &$b));
								
								
								/**
								* detect the load after function
								*/
								if(method_exists($current, 'load_after')){
									$sub_method_before = array($current, 'load_after');
									$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
								}
								
								return $output;
								
							}else{
								/**
								* If request doesn't match any condition above
								* 404 must be call using below conditions
 								*/
								header("HTTP/1.0 404 Page not found!");
								
								/**
								* re-route the request to 404 instead of the current Uri segment
								*/
								$this->builder->route = $this->static_routes['_404_'];
								
								/**
								* Check the 404 file it in the builder
								*/
								$this->first_segment_request_manager();
								
								/**
								* Check the class if it is available, this is case it is calling the 404 class name
								*/
								if($this->builder->_caller()){
									/**
									* 404 is just like the other builders, it calls first the _index, it's class self constructor
									*/
									$current = $this->builder->_caller();
									$methodVariable = array($current, 'action_index');
									
									/**
									* Check if _index (the builder common constructor is exist)
									*/
									if(method_exists($current, 'action_index')){
										/**
										* Assign the current requested segment to a variable 
										* then pass it to the base class
										*/
										$a = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(1) : 'root';
										$b = ($this->Uri->get_segment(2)) ? $this->Uri->get_segment(2) : $this->Uri->get_segment(1);
										
										$output = "";
								
										/**
										* detect the load before function
										*/
										if(method_exists($current, 'load_before')){
											$sub_method_before = array($current, 'load_before');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										$output .= call_user_func_array($methodVariable, array(&$a, &$b));
										
										
										/**
										* detect the load after function
										*/
										if(method_exists($current, 'load_after')){
											$sub_method_before = array($current, 'load_after');
											$output .= call_user_func_array($sub_method_before, array(&$a, &$b));
										}
										
										return $output;
										
									}else{
										/**
										* if it is not exist, return a plane 404 message
										*/
										return '404: Page not found!';
									}
								}else{
									/**
									* if it is not exist, return a plane 404 message
									*/
									return '404: Page not found!';
								}
							}
						}
						
					}
				}
		}
		
		/**
		* @Object first_segment_request_manager -- managing the first segment of the request
		*/
		public function first_segment_request_manager()
		{
				if($this->builder->_checker()){
					require_once($this->builder->bldr_file);
				}else{
					/**
					* if no found, 404 must be check
					*/
				
					$this->builder->route = $this->static_routes['_404_'];
					if(!$this->builder->_checker()){
						/**
						* if 404 not exist, critical error will execute
						*/
						$this->_critical_exit();
					}
				}
		}
		
		/**
		* Get the static routes from routes.a.c.php
		*/
		public function _raw_routes()
		{
			return include $this->globals['path']['apps_path'] . '/config/routes.a.c.php';			
		}
		
		public function _critical_exit()
		{
			header("HTTP/1.0 500 Internal Server Error");
			exit('Looks like your copy of <a href="http://www.ac-base.org">AC-Base framework</a> is broken, please <a href="http://www.ac-base.org/pakages/latest">download</a> our latest version and re-install to work your system properly. Thanks you!');
		}
		
		/**
		* using expression route -- this method will read it
		*/
		public function _route_preg_match($subject = "")
		{
				$captured_route = array();
				foreach($this->static_routes as $route => $controller){
					if($route != "_root_" || $route != ""){
						/** implode('/', $this->Uri->get_segments())
						* EXPLODE to get the first segment
						*/
						$get_segment = explode('/', $route);
						/**if(isset($get_segment[0]) && $get_segment[0] != "" && preg_match("/" . $get_segment[0] . '/i', $subject) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $get_segment[0])){*/
						
						//$subject = str_replace("/", "\/", $subject);
						$route_pattern = "/" . str_replace("/", "\/", $route) . "/i";
						
						if(($route == $subject) || (preg_match_all($route_pattern, $subject))){
							
							/**
							* once it matches with the condition -- it'll return the controller name
							*/
							if(count($this->Uri->get_segments()) > count(explode("/",$route))){
								$baseCounter = count(explode("/",$route)) + 1;
								$ic = 1;
								for($i = $baseCounter; $i <= count($this->Uri->get_segments()); $i++){
									$captured_route[$ic] = $this->Uri->get_segment($i);
									$ic++;
								}
							}else{
								$this->builder->ishome = true;
							}
							
							$this->builder->static_segments = $captured_route;
							
							$controller = explode("/", $controller);
							
							$this->builder->route = $controller[0];
							
							if(isset($controller[1]) && $controller[1] != ""){
								if($controller[1] != "$1"){
									$this->builder->fix_fn = $controller[1];
								}
							}
							
							return true;
							
							break;						
						}
					}
				}
				
				
			
		}
	}
	
#//php_end\\;?>