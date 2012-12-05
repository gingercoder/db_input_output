<?php


/**
 * Database Construct and Manipulation class
 * Includes Export and Import routines for your MySQL database system
 * v 1.0.0
 * @author Rick Trotter
 */
abstract class db {
       
    private static $boolConnected;
    
    function dbconnect($dbhost, $dbuser, $dbpass, $dbname){
        // Connect to the database
        if(!self::$boolConnected)
        {
                $conn= mysql_connect($dbhost, $dbuser, $dbpass);
                if($conn === FALSE)
                {
                    die ("Cannot connect to MySql database control.");
                    die();
                }
                else{
                    $db = mysql_select_db($dbname, $conn);
                    if($db === FALSE)
                    {
                        die ("Cannot connect to your database.");
                        die();
                    }
                }
                $boolConnected = true;
        }
    }

    // Function to run the SQL passed to you from the code
	public function execute($sql){
		
                // Run the requested SQL
		$result = mysql_query($sql);
		if($result)
                {
                    return true;
		}
		else
                {
                    return false;
		}
	}
	
	// Function to return a single row from SQL
	public function returnrow($sql){
		
                $sql .= " LIMIT 1";
		$result = mysql_query($sql);
                
		if($result)
                {
                    return mysql_fetch_array($result);
		}
		else
                {
                    return false;	
		}	
	}
        
	public function returnallrows($sql){
		// Get all rows from the database given the SQL from the application
                $result = mysql_query($sql);
		$resultset = array();
                
                while($arow = mysql_fetch_assoc($result)){
                    $resultset[] = $arow;
                }
                
		return $resultset;
		
	}
	
        public function escapechars($var){
            // Escape any nasty code in the user input text
            return mysql_real_escape_string(trim($var));
            
        }
	
        public function getnumrows($sql){
            // Get the number of rows for a SQL query
            $result = mysql_query($sql);
            $numrows = mysql_numrows($result);
            return $numrows;
        }
        
        
        public function getlastid(){
            // Get the last inserted SQL ID
            $id = mysql_insert_id();
            return $id;
        }
        
        public function disconnect(){
            mysql_disconnect;
        }
        
	// Function to back up your database
	// @param string $outputpath location where you want to 
        public function backupDatabase($outputpath)
            {
                $outputpath = $this->escapechars($outputpath);
                
                //save file
                $path = $outputpath.'/backup-'.date('YmdHis').'.sql';
                $fp = fopen($path,'w');
                // populate a list of all the tables
                
                $tables = $this->returnallrows('SHOW TABLES');
                // iterate through each table
                foreach($tables as $table)
                {
                    foreach($table as $item){
                        $return = '-- DUMPING CONSTRUCT AND DATA FOR '.$item.';\n\n';
                        
                        $sql = "SHOW CREATE TABLE $item";
                        $result = $this->returnallrows($sql);
                        // Dump the generate SQL
                        foreach($result as $entry){
                            foreach($entry as $entryarray){
                                $return .= $entryarray . ";\n\n";
                            }
                        }
                        // Dump the data for the table
                        $sql = "SELECT * FROM $item";
                        $data = $this->returnallrows($sql);
                        $fieldsql = 'SHOW COLUMNS FROM '.$item;
                        $num_fields = $this->getnumrows($fieldsql);
                        
                        foreach($data as $output){
                            $return .= "INSERT INTO $item VALUES(";
                            $i = 1;
                            foreach($output as $blob){
                                
                                $blob = addslashes($blob);
                                $blob = ereg_replace("\n","\\n",$blob);
                                
                                $return .= "$blob";
                                if($i < $num_fields){
                                    $return .= ",";
                                }
                                $i++;
                            }
                            $return .= ");\n\n";
                        }
                        fwrite($fp,$return);
                    }
                }
        }
        
        
        // Function to load a SQL file into your database
	// @param string $myfile location and filename of the SQL file to load
        function loadSQL($myfile){
            // load file
            $myfile = $this->escapechars($myfile);
	    
	    $commands = file_get_contents($myfile);
	    
            //delete comments
            $lines = explode("\n",$commands);
            $commands = '';
            foreach($lines as $line){
                $line = trim($line);
                if( $line && (substr($line,0,2) != '--') ){
                    $commands .= $line . "\n";
                }
            }
            //convert to array
            $commands = explode(";", $commands);
            //run commands
	    $total = $success = 0;
            foreach($commands as $command){
		if(trim($command)){
                    $this->execute($command);
		    $total++;
                }
            }
	    
	    return $total.' commands executed';
        }

}

