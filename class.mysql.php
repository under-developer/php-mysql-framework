<?php 

class mysql{

    private $server = null;
    private $username = null;
    private $password = null;
    private $database = null;
    private $port = null;
    
    private $conn = null;
    private $no_db_conn = null;
 

    public function __construct($server = "localhost",$username,$password,$database = "",$port = 3306){
        $server = (string)$server;
        $username = (string)$username;
        $password = (string)$password;
        $database = (string)$database;
        $port = (int)$port;
        
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
        
        $this->conn = mysqli_connect($server,$username,$password,$database,$port);
        $this->no_db_conn = mysqli_connect($server,$username,$password,"",$port);
 
    }

    public function close(){
        if(!mysqli_close($this->conn) && !mysqli_close($this->no_db_conn)){
            return false;
        }else{
            return true;
        }
         
    }

    public function query($sql_command,$server = null){
    
        if($server === null){
            $server = $this->conn;
        }
        
        return mysqli_query($server,$sql_command);
    
    }

    
    public function create($array){
        $status = NULL;
        
        foreach($array as $index => $name){
            if(strtolower($index) === "database"){
                if(is_array($name)){

                    foreach ($name as $db_names => $table_array) {

                        if(is_array($table_array)){
                            if(!mysql::query("CREATE DATABASE IF NOT EXISTS `$db_names`",$this->no_db_conn)){
                                throw new Exception(mysqli_error($this->no_db_conn));
                                exit;
                            }

                            $new_conn = mysqli_connect($this->server,$this->username,$this->password,$db_names,$this->port);
                            if(!$new_conn){
                                throw new Exception(mysqli_error($new_conn));
                                exit;
                            }
                            if(!mysql::query("USE `$db_names`",$new_conn)){
                                throw new Exception(mysqli_error($new_conn));
                                exit;
                            }
    
                            foreach ($table_array as $table_name => $column_array) {
                                $sql = "CREATE TABLE IF NOT EXISTS `$table_name`(";
                                foreach ($column_array as $col_name => $col_prop) {
                                    if(!is_array($col_prop)){
                                        $sql .= "`$col_name` $col_prop,";
                                    }else{
                                        if(isset($col_prop["default"])){
                                            $def = mysqli_real_escape_string($new_conn,$col_prop["default"]);
                                            $sql .= '`'.$col_name.'` '.$col_prop["type"].' DEFAULT "'.$def.'" ,';
                                        }else{
                                            $sql .= "`$col_name` ".$col_prop["type"].",";
                                        }
                                    }
                                }
                                $sql = rtrim($sql,",");
                                $sql .= ")";
                                if(!mysql::query($sql,$new_conn)){
                                    throw new Exception(mysqli_error($new_conn));
                                    exit;
                                }else{
                                    $status = true;
                                }
                            }
    
                            mysqli_close($new_conn);
                        }else{
                            if(!mysql::query("CREATE DATABASE IF NOT EXISTS `$table_array`",$this->no_db_conn)){
                                throw new Exception(mysqli_error($this->no_db_conn));
                                exit;
                            }else{
                                $status = true;
                            }
                        }

                    }

                }
            }else if(strtolower($index) === "table"){
                foreach ($name as $table_name => $content){
                    $sql = "CREATE TABLE IF NOT EXISTS `$table_name`(";
                    
                    if(isset($content["database"])){
                        $insert_db = mysqli_real_escape_string($this->conn,$content["database"]);
                    }else{
                        $insert_db = $this->database;
                    }
                    $new_conn = mysqli_connect($this->server,$this->username,$this->password,$insert_db,$this->port);
                    if(isset($content["column"])){
                        foreach($content["column"] as $col_name => $col_content){
                            $col_name  = mysqli_real_escape_string($new_conn,$col_name);
                            if(is_array($col_content)){
                                $sql .= "`$col_name` ".$col_content["type"];
                                if(isset($col_content["default"])){
                                    $def =  mysqli_real_escape_string($new_conn,$col_content["default"]);
                                    $sql .= ' DEFAULT "'.$def.'",';
                                }else{
                                    $sql .= ",";
                                }
                            }else{
                                $sql .= "`$col_name` $col_content,";
                            }

                        }
                        $sql = rtrim($sql,",");
                        $sql .= ")";
                        if(!mysql::query($sql,$new_conn)){
                            throw new Exception(mysqli_error($new_conn));
                            exit;
                        }else{
                            $status = true;
                        }
                        mysqli_close($new_conn);
                    }else{
                        throw new Exception("the columns to be created are not specified");
                        exit;
                    }
                }
            }
        }
        return $status;
    }
}