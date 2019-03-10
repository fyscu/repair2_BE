<?php
use Medoo\Medoo;

class DB_MySQL {
    public static $db_type = "MySQL";
    private $_db;

    function __call ($name, $args){
        if($name=='search'){
            $i=count($args);
            if (method_exists($this, $f='search_'.$i)){
                return call_user_func_array(array($this, $f), $args);
            }
        }
    }

    /**
     * @param array $dbconfig from config.php
     */
    public function __construct($dbconfig){
        $this->_db = new Medoo([
        	// required
        	'database_type' => 'mysql',
        	'database_name' => $dbconfig['database_name'],
        	'server' => $dbconfig['server'],
        	'username' => $dbconfig['username'],
        	'password' => $dbconfig['password'],
         
        	// [optional]
        	'charset' => $dbconfig['charset'],
        	'port' => $dbconfig['port'],
         
        	// [optional] Enable logging (Logging is disabled by default for better performance)
        	'logging' => false,
         
        	// [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
        	'option' => $dbconfig['option'],
         
        	// [optional] Medoo will execute those commands after connected to the database for initialization
        	'command' => [
        		'SET SQL_MODE=ANSI_QUOTES'
        	]
        ]);
    }

    /**
     * @param string $table
     * @param string $key
     * @param string $value
     * @return array
     */
    public function select($table, $key, $value){
        $datas = $this->_db->select($table, "*", [$key => $value]);
        return $datas;
    }

    /**
     * @param string $table
     * @param string $key
     * @param array $data
     * @param string $value
     * @return array
     */
    public function insert($table, $key, $data, $value){
        $data[$key]=$value;
        $datas = $this->_db->insert($table, $data);
        
        if ($datas->rowCount()<1){
            return array('code'=>406,'error'=>$this->_db->error());
        } else {
            return array('code'=>200);
        }
    }

    /**
     * @param string $table
     * @param string $key
     * @param string $value
     * @return array
     */
    public function delete($table, $key, $value){
        $datas = $this->_db->delete($table, [$key => $value]);
        
        if ($datas->rowCount()<1){
            return array('code'=>406,'error'=>$this->_db->error());
        } else {
            return array('code'=>200);
        }
    }
    
    /**
     * @param string $table
     * @param mixed $dull
     * @param array $where https://medoo.in/api/where
     * @return array
     */
    public function deletequery($table, $dull, $where){
        $datas = $this->_db->delete($table, $where);
        
        if ($datas->rowCount()<1){
            return array('code'=>406,'error'=>$this->_db->error());
        } else {
            return array('code'=>200);
        }
    }

    /**
     * @param string $table
     * @param string $key
     * @param array $data data of doucment
     * @param string $value
     * @return array
     */
    public function update($table, $key, $data, $value){
        $datas = $this->_db->update($table, $data, [$key=>$value]);

        if ($datas->rowCount()<1){
            return array('code'=>406,'error'=>$this->_db->error());
        } else {
            return array('code'=>200);
        }
    }
    
    /**
     * @param string $table
     * @param mixed $cols "*" or array
     * @param array $where https://medoo.in/api/where
     * @return array hits arrays 
     */
    public function search_3($table, $cols, $where){
        $datas = $this->_db->select($table, $cols, $where);
        return $datas;
    }
    
    /**
     * @param string $table
     * @param array $join https://medoo.in/api/select
     * @param mixed $cols "*" or array
     * @param array $where https://medoo.in/api/where
     * @return array hits arrays 
     */
    public function search_4($table, $join, $cols, $where){
        $datas = $this->_db->select($table, $join, $cols, $where);
        //return array("data"=>$datas, "error"=>$this->_db->error());
        return $datas;
    }
    
    /**
     * @param string $sql
     * @return array
     */
    public function query($sql){
        $datas = $this->_db->query($sql)->fetchAll();
        return $datas;
    }
}
