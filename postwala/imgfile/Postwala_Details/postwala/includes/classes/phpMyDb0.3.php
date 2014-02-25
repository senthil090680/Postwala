<?php
/*
 * Name:	phpMyDb
 * URL:		http:/neo22s.com/
 * Version:	v0.3
 * Date:	15/02/2010
 * Author:	Chema Garrido
 * Support: http://forum.neo22s.com
 * License: GPL v3
 * Notes:	Mysql Object with cache integrated, requires fileCache class
 */

class phpMyDb {//requires file cache class
	private $dbh;//data base handler
	private $query_counter=0;//count queries
	private $db_time=0;//application start time
	private $query_cache;//fileCache object
	private $query_cache_status=false; //cache deactivated by default
	private $query_cache_counter=0;//count cached queries
	private $debug=false; //no debug by default
	private $log=array();//log for the debug system
	private $insert_last_id;//last insert ID for mysql_insert_id()
	
		// DB Constructor - connects to the server and selects a database
		public function phpMyDb($dbuser, $dbpass, $dbname, $dbhost,$dbcharset){
			$this->db_time=microtime(true);//db time starts
			$this->dbh = @mysql_connect($dbhost,$dbuser,$dbpass);
			if (!$this->dbh){
				$this->print_error("<ol><li><b>Error establishing a database connection!</b><li>Are you sure you have the correct user/password?<li>Are you sure that you have typed the correct hostname?<li>Are you sure that the database server is running?</ol>");
			}
			$this->selectDB($dbname);
			$this->query('SET NAMES '.$dbcharset);
		}
		
		// Select a DB (if another one needs to be selected)
		public function selectDB($db){
			if ( !@mysql_select_db($db,$this->dbh)){
				$this->print_error("<ol><li><b>Error selecting database <u>$db</u>!</b><li>Are you sure it exists?<li>Are you sure there is a valid database connection?</ol>");
			}
		}
		
		// Closes DB connection
		public function closeDB(){
			unset($this->dbh);
			mysql_close();
			$msg=$this->query_counter." queries generated in ".round( (microtime(true) - $this->db_time),5)."s";
			if ($this->query_cache) $msg.= " and ".$this->query_cache_counter." queries cached";
			//echo $msg;
			$this->addLog("Function closeDB: ".$msg);
		}
		
		// Normal query
		public function query($query) {
			$this->addLog("Function query: ".$query);
			$this->query_counter++;
			$return_val=@ mysql_query($query,$this->dbh) or $this->print_error("(".mysql_errno().") in line ".__LINE__." error:".mysql_error()." <br/>Query: ". $query." <br/>File: ". $_SERVER['PHP_SELF'] );
			$this->addLog("End function query");
			return $return_val;
		}
		
		///Select functions
		
		//normal select
		public function select($fields, $from, $where) {  
			$this->addLog("Function select");
            $query = "SELECT " . $fields . " FROM `" . $from . "` WHERE " . $where;  
            $result = $this->query($query);  
            return $result;  
        }  
        
        //insert into
		public function insert($into, $values) {  
			$this->addLog("Function insert");
            $query = "INSERT INTO " . $into . " VALUES(" . $values . ")";  
            if($this->query($query)) {
            	$this->setLastID(mysql_insert_id());
            	return true;  //succed
            }
            else  return false;  //not succed    
        } 
        
        //delete from
        public function delete($from, $where) {  
        	$this->addLog("Function delete");
            $query = "DELETE FROM " . $from . " WHERE " . $where;  
            if($this->query($query))   return true;  //succed
            else  return false;  //not succed        
         } 
         
        //update, aware! $value= column='test', name='test2' .....
        public function update($table,$values, $where) {  
        	$this->addLog("Function update");
            $query = "UPDATE $table SET $values WHERE $where";
            if($this->query($query))   return true;  //succed
            else  return false;  //not succed        
        } 
                         
        //returns an array with the SQL values, uses cache if enabled
		public function getRows($query,$type="assoc",$cache="cache"){
			$this->addLog("Function getRows $type");
			if ($this->query_cache_status){//cache activated??
				if ($cache=="cache") $values = $this->query_cache->cache($query);//setting values from cache
				elseif ($cache=="APP") $values = $this->query_cache->APP($query);//setting values from cache
			}
			else $values==false;
			
			if ($values==false) {	//not value from cache found
				$result=$this->query($query);
				if (mysql_num_rows($result)>0){//checking if there's more than one result
					$values=array();
					switch ($type){
						case "assoc":
							while($row = mysql_fetch_assoc($result))  array_push($values, $row);  
						break;
						case  "row":
							while($row = mysql_fetch_row($result))  array_push($values, $row);  
						break;
						case "object":
							while($row = mysql_fetch_object($result))  array_push($values, $row);  
						break;
						case "value":
							$row=mysql_fetch_row($result);
							$values=$row[0];//return value
						break;	
					} 
					if ($this->query_cache_status){//save cache
						if ($cache=="cache") $this->query_cache->cache($query, $values);
						elseif ($cache=="APP") $this->query_cache->APP($query, $values);
						$this->addLog("Function getRows saved in $cache");
						
					}
				}//theres more than 1 row
				else $values=false;//not found any row
			}//if values was false
			else{//found in cache
				$this->addLog("Function getRows from $cache query: $query");
				$this->query_cache_counter++;
			}
			return $values;
		}
		
		// return the 1st value from a field of a query
		public function getValue($query,$cache="cache"){
			return $this->getRows($query,"value",$cache);
		}
		
		private function setLastID($id){
			$this->insert_last_id=$id;
		}
		public function getLastID(){
			return $this->insert_last_id;
		}
		/////////////Tool functions
		
		//sets cache active or inactive
		public function setCache($state,$time=3600,$path="cache/"){
			$this->query_cache_status=$state;
			if ($this->query_cache_status) $this->query_cache = new fileCache($time,$path);//active
			else unset($this->query_cache);//unset
		}
		
		//sets debug on or off
		public function setDebug($state){
			$this->debug=$state;
		}
		
		public function returnDebug($type="HTML"){
			if ($this->debug){
				switch($type){
					case "array":
						return $this->log;
					break;
					case "HTML":
						//print_r ($this->log);s
						echo '<ol>';
						foreach($this->log as $key=>$value){
							echo '<li>'.$value.'</li>';
						}
						echo '</ol>';	
					break;
				}	
			}
			else return false;	
		}
		
		//add debug log
		public function addLog($value){
			if ($this->debug) array_push($this->log,round( (microtime(true) - $this->db_time),5)."s - ". $value);  
		}
		
		// Print SQL/DB error.
		private function print_error($str = ""){
			if ( !$str ) $str = mysql_error();
			// If there is an error then take note of it
			ocSqlError("<b>SQL/DB Error</b> <br />$str");
			die();
		}
		
		public function getQueryCounter($type="queries"){
			switch($type){
				case "queries":
					return $this->query_counter;
				break;
				case "cache":
					return $this->query_cache_counter;
				break;
			}
			
		}
}
?>
