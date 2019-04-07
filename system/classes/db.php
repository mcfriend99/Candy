<?php

/**
 * Candy-PHP - Code the simpler way.
 *
 * The open source PHP Model-View-Template framework.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2018 Ore Richard Muyiwa
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	Candy-PHP
 * @author		Ore Richard Muyiwa
 * @copyright      2017 Ore Richard Muyiwa
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://candy-php.com/
 * @since	Version 1.0.0
 */

if(!defined('CANDY')){
	header('Location: /');
}

define('CANDY_SQL_RESULT_UNKNOWN', -1);
define('CANDY_SQL_RESULT_SELECT', 0);
define('CANDY_SQL_RESULT_INSERT', 1);
define('CANDY_SQL_RESULT_UPDATE', 2);
define('CANDY_SQL_RESULT_DELETE', 3);

/**
 * Class DB
 * 
 * Candy's database class based on PDO.
 * To use DB, #db_host, #db_user, #db_pass and #db_name must be set in configs/db.candy
 * 
 */
class DB {

    /**
     * @var The private connection object.
     */
    private $con = null;

    /**
     * @var Array of errors that occurred during operation.
     */
    public $errors = array();

    /**
     * DB constructor.
     */
    function __construct(){ }

    /**
     * Initialize a database connection.
     * @return bool
     */
    private function connect(){
		if($this->con != null) return true;
		try {
            
			$this->con = new PDO(get_config('db_engine', 'db') . ':host='. get_config('db_host', 'db') .';dbname='. get_config('db_name', 'db') . ';charset=utf8', get_config('db_user', 'db'), get_config('db_pass', 'db'));
			
			do_action('on_database_connect');

            return true;
        } catch (PDOException $e) {
            $this->errors[] .= $e->getMessage();
            throw new Exception('DB_ERROR :: Failed to open database with the specified config. (Because: ' . $e->getMessage() . ')<br>Make sure the database configuration to correct.');
        }
	}

    /**
     *
     * Selects data from a database table.
	 *
	 * !column_name = != column   (Can be anywhere within column name)
	 * ?column_name = OR column   (Can be anywhere within column name)
	 * %column_name = LIKE column (Can be anywhere within column name)
	 * (column_name = Open group. (Must be first)
	 * column_name) = Close group (Must be last)
     *
     * @param $table                    Table to select data from.
     * @param array $columns            Array list of columns to select. Use null to select all, negate columns with '!'.
     * @param array $where              Name=>Value array of conditions to where clause. Use null to specify none.
     * @param string $order             String indicating order of result E.g. "id DESC". Use null to specify default.
     * @param string $limit             String containing lower limit and count in the form "1, 5". Use null to add no limit.
     * @param string $where_extra       Extra clauses into the where clause that you may consider not specifiable in $where.
     * @return object                   An object containing all data.
     */
    public function select($table, $columns = null, $where = null, $order = null, $limit = null, $where_extra = null){
		$_cols = ""; $_cls = "";
		if($this->connect()){
			if($columns == null) $_cols = "*";
			else {
				foreach($columns as $col){
				$_cols .= ",{$col}";
				}
				$_cols = substr($_cols, 1);
			}
			if($where != null){
				foreach($where as $key => $value){
					$p = "=";

					$is_liked = false;

                    if($key[0] == '('){
                        $_pr = '(';
                    } else $_pr = '';
                    if($key[strlen($key) -1] == ')'){
                        $_pe = ')';
                    } else $_pe = '';

					if(strpos($key, '?') > -1){
						$is_and = false;
					} else {
						$is_and = true;
					}

                    if(strpos($key, '=') > -1){
                        $p = "=";
                    } else if(strpos($key, '!') > -1){
                        $p = "!=";
                    } else if(strpos($key, '>') > -1){
                        $p = ">";
                    }  else if(strpos($key, '>=') > -1){
                        $p = ">=";
                    }  else if(strpos($key, '<') > -1){
                        $p = "<";
                    }  else if(strpos($key, '<=') > -1){
                        $p = "<=";
                    } else if(strpos($key, '%') > -1){
                        $value = $this->escape('%' . $value . '%');
                        $is_liked = true;
                    }

                    $key = preg_replace('~[%?!()<>=]+~', '', $key);

					if(!$is_liked){
                        $_cls .= " " .($is_and ? "AND" : " OR "). " {$_pr} `$key` {$p} :{$key} {$_pe}";
					} else {
                        $_cls .= " " .($is_and ? "AND" : " OR "). " {$_pr} `$key` LIKE {$value} {$_pe}";
					}
				}
				$_cls = " WHERE ".substr($_cls, 4);
			}
            $_cls .= $where_extra;
			if($order != null) $order = "ORDER BY {$order}";
			if($limit != null) $limit = "LIMIT {$limit}";

			$sql = $this->con->prepare("SELECT {$_cols} FROM `{$table}`{$_cls} {$order} {$limit}");
			if($where != null){
				foreach($where as $key => $value){
					$is_liked = preg_match('~[%]+~', $key) ? true : false;
					$key = preg_replace('~[%?!()<>=]+~', '', $key);
					if(!$is_liked)
						$sql->bindValue(":{$key}", $value, PDO::PARAM_STR);
				}
			}

			do_action('before_sql_select');

			$sql->execute();
			if($result = $sql->fetchAll(PDO::FETCH_OBJ)){
				$result = apply_filters('sql_result', $result, CANDY_SQL_RESULT_SELECT);
				do_action('on_sql_result', $result, CANDY_SQL_RESULT_SELECT);
				return $result;
			}
		}
		return false;
	}

    /**
     *
     * Selects data from a database table using OR instead of AND.
     *
     * @param $table                    Table to select data from.
     * @param array $columns            Array list of columns to select. Use null to select all.
     * @param array $where              Name=>Value array of conditions to where clause. Use null to specify none.
     * @param string $order             String indicating order of result E.g. "id DESC". Use null to specify default.
     * @param string $limit             String containing lower limit and count in the form "1, 5". Use null to add no limit.
     * @param string $where_extra       Extra clauses into the where clause that you may consider not specifiable in $where.
     * @return object                   An object containing all data.
     */
    public function orSelect($table, $columns = null, $where = null, $order = null, $limit = null, $where_extra = null){
		$_cols = ""; $_cls = "";
		if($this->connect()){
			if($columns == null) $_cols = "*";
			else {
				foreach($columns as $col){
				$_cols .= ",{$col}";
				}
				$_cols = substr($_cols, 1);
			}
			if($where != null){
				foreach($where as $key => $value){
                    $p = "=";

                    $is_liked = false;

                    if($key[0] == '('){
                        $_pr = '(';
                    } else $_pr = '';
                    if($key[strlen($key) -1] == ')'){
                        $_pe = ')';
                    } else $_pe = '';

                    if(strpos($key, '?') > -1){
                        $is_and = false;
                    } else {
                        $is_and = true;
                    }

                    if(strpos($key, '=') > -1){
                        $p = "=";
                    } else if(strpos($key, '!') > -1){
                        $p = "!=";
                    } else if(strpos($key, '>') > -1){
                        $p = ">";
                    }  else if(strpos($key, '>=') > -1){
                        $p = ">=";
                    }  else if(strpos($key, '<') > -1){
                        $p = "<";
                    }  else if(strpos($key, '<=') > -1){
                        $p = "<=";
                    } else if(strpos($key, '%') > -1){
                        $value = $this->escape('%' . $value . '%');
                        $is_liked = true;
                    }

                    $key = preg_replace('~[%?!()<>=]+~', '', $key);

                    if($is_liked) $_cls .= " OR `$key` LIKE {$value}";
                    else $_cls .= " OR `$key` {$p} :{$key}";
				}
				$_cls = " WHERE ".substr($_cls, 4);
			}
            $_cls .= $where_extra;
			if($order != null) $order = "ORDER BY {$order}";
			if($limit != null) $limit = "LIMIT {$limit}";
			$sql = $this->con->prepare("SELECT {$_cols} FROM `{$table}`{$_cls} {$order} {$limit}");
			if($where != null){
				foreach($where as $key => $value){
                    $is_liked = preg_match('~[%]+~', $key) ? true : false;
                    $key = preg_replace('~[%?!()<>=]+~', '', $key);
                    if(!$is_liked)
					    $sql->bindValue(":{$key}", $value, PDO::PARAM_STR);
				}
			}

			do_action('before_sql_select');

			$sql->execute();
			if($result = $sql->fetchAll(PDO::FETCH_OBJ)){ 
				$result = apply_filters('sql_result', $result, CANDY_SQL_RESULT_SELECT);
				do_action('on_sql_result', $result, CANDY_SQL_RESULT_SELECT);
				return $result;
			}
		}
		return false;
	}

    /**
     *
     * Selects data from a database table using LIKE instead of = and !=.
     *
     * @param $table                    Table to select data from.
     * @param array $columns            Array list of columns to select. Use null to select all.
     * @param array $where              Name=>Value array of conditions to where clause. Use null to specify none.
     * @param string $order             String indicating order of result E.g. "id DESC". Use null to specify default.
     * @param string $limit             String containing lower limit and count in the form "1, 5". Use null to add no limit.
     * @param string $where_extra       Extra clauses into the where clause that you may consider not specifiable in $where.
     * @return object                   An object containing all data.
     */
    public function likeSelect($table, $columns = null, $where = null, $order = null, $limit = null, $where_extra = null){
		$_cols = ""; $_cls = "";
		if($this->connect()){
			if($columns == null) $_cols = "*";
			else {
				foreach($columns as $col){
				$_cols .= ",{$col}";
				}
				$_cols = substr($_cols, 1);
			}
			if($where != null){
				foreach($where as $key => $value){
					$_cls .= " AND `$key` LIKE :{$key}";
				}
				$_cls = " WHERE ".substr($_cls, 4);
			}
            $_cls .= $where_extra;
			if($order != null) $order = "ORDER BY {$order}";
			if($limit != null) $order = "LIMIT {$limit}";
			
			$sql = $this->con->prepare("SELECT {$_cols} FROM `{$table}`{$_cls} {$order} {$limit}");
			if($where != null){
				foreach($where as $key => $value){
					if($key[0] == "!") $key = substr($key, 1);
					$sql->bindValue(":{$key}", $value."%", PDO::PARAM_STR);
				}
			}

			do_action('before_sql_select');

			$sql->execute();
			if($result = $sql->fetchAll(PDO::FETCH_OBJ)){ 
				$result = apply_filters('sql_result', $result, CANDY_SQL_RESULT_SELECT);
				do_action('on_sql_result', $result, CANDY_SQL_RESULT_SELECT);
				return $result;
			}
		}
		return false;
	}

    /**
     *
     * Inserts a new row into a database table.
     *
     * @param $table        The table to insert into.
     * @param $data         Name=>Value array of data to be insert into the table.
     * @return int          The id of the last inserted row or 0 if insert fails.
     */
    public function insert($table, $data){
		if($this->connect()){
			$_cols = ""; $_vals = "";
			foreach($data as $key => $value){
				$_cols .= ",`{$key}`";
				$_vals .= ",:{$key}";
			}
			$_cols = substr($_cols, 1);
			$_vals = substr($_vals, 1);
			$sql = $this->con->prepare("INSERT INTO `{$table}` ({$_cols}) VALUES ({$_vals})");

			foreach($data as $key => $value){
				$sql->bindValue(":{$key}", $value, PDO::PARAM_STR);
			}

			do_action('before_sql_insert');

			$sql->execute();
			$id = $this->con->lastInsertId();

			$result = apply_filters('sql_result', $result, CANDY_SQL_RESULT_INSERT);
			do_action('on_sql_result', $result, CANDY_SQL_RESULT_INSERT);

			return $id;
		}
	}

    /**
     *
     * Inserts a new row into a database table if a duplicate row does not already exist.
     *
     * @param $table                    The table to insert data into.
     * @param $data                     Name=>Value array of data to insert into the table.
     * @param array $exception          An array listing columns that may exist as duplicates.
     * @return int|mixed                Id of the inserted row if successful, 0 if it fails or -1 if a duplicate is found.
     */
    public function insertUnique($table, $data, $exception = []){
		$data2 = [];
		foreach($data as $key => $val){
			if(!in_array($key, $exception)){
				$data2 = array_merge($data2, [$key=>$val]);
			}
		}
        if(!empty($exception))
		  $test = $this->select($table, null, $data2);
        else $test = null;
		if(empty($test)){
			return $this->insert($table, $data2);
		} else {
			return -1;
		}
		return 0;
	}

    /**
     *
     * Inserts a new row into a database table if does not exist or update it if it already exist.
     *
     * @param $table                The table to insert data into.
     * @param $id                   The column serving as identifier for duplicates.
     * @param $data                 Name=>Value array of data to insert.
     * @return bool                 True if inserted or updated, false otherwise.
     */
    public function insertUpdate($table, $id, $data){
		$keyval = null; $keyfound = false;
		foreach($data as $key => $val){
			if($key == $id){
				$keyval = $val;
				$keyfound = true;
			}
		}

		if($keyfound){
			$test = $this->select($table, null, [$id => $keyval]);
			if(empty($test)){
				$uid = $this->insert($table, $data);
				if($uid > 0)
					return true;
			} else {
				unset($data[$id]);
				return $this->update($table, $data, [$id => $keyval]);
			}
		}
		return false;
	}

    /**
     *
     * Updates a database table row.
     *
     * @param $table                Table where we want to update data.
     * @param $data                 Name=>Value array of data to update.
     * @param null $where           Name=>Value array of conditions for where clause.
     * @return bool                 True if table is updated, Otherwise, False.
     */
    public function update($table, $data, $where = null){
		if($this->connect()){
			$_cols = ""; $_cls = "";
			foreach($data as $key => $value){
				$_cols .= ",`{$key}` = :{$key}";
			}
			$_cols = substr($_cols, 1);
			if(!empty($where)){
				foreach($where as $key => $value){
					$p = "=";
					if($key[0] == "!"){
						$p = "!=";
						$key = substr($key, 1);
					}
					$_cls .= ",`$key` {$p} :_{$key}";
				}
				$_cls = " WHERE ".substr($_cls, 1);
			}
			$sql = $this->con->prepare("UPDATE `{$table}`  SET {$_cols} {$_cls}");

			foreach($data as $key => $value){
				$sql->bindValue(":{$key}", $value, PDO::PARAM_STR);
			}
			if(!empty($where)){
				foreach($where as $key => $value){
					if($key[0] == "!") $key = substr($key, 1);
					$sql->bindValue(":_{$key}", $value, PDO::PARAM_STR);
				}
			}

			do_action('before_sql_update');

			$result = $sql->execute();

			$result = apply_filters('sql_result', $result, CANDY_SQL_RESULT_UPDATE);
			do_action('on_sql_result', $result, CANDY_SQL_RESULT_UPDATE);

		}
        return false;
	}

    /**
     *
     * Deletes a row from a database table.
     * Unless you want to clear an entire database, always specify the where param.
     *
     * @param $table        Name of table from which to delete row.
     * @param $where        Name=>Value array of conditions for where clause.
     */
    public function delete($table, $where){
		if($this->connect()){
			$_cols = "";
			foreach($where as $key => $value){
				$p = "=";
				if($key[0] == "!"){
					$p = "!=";
					$key = substr($key, 1);
				}
				$_cols .= ",`{$key}` {$p} :{$key}";
			}

			// Trashing before removing row
			$sqr = $this->select($table, null, $where);
			$this->insert("trash", ["content" => @json_encode($sqr), 'trash_time' => time(), 'trash_type' => $table]);

			if(!empty($_cols)) $_cols = substr($_cols, 1);

			// Removing row.
			$sql = $this->con->prepare("DELETE FROM `{$table}` WHERE {$_cols}");
			foreach($where as $key => $value){
				$sql->bindValue(":{$key}", $value, PDO::PARAM_STR);
			}

			do_action('before_sql_delete');

			$result = $sql->execute();

			$result = apply_filters('sql_result', $result, CANDY_SQL_RESULT_DELETE);
			do_action('on_sql_result', $result, CANDY_SQL_RESULT_DELETE);
			
		}
	}

    /**
     *
     * Executes a raw sql query on the database and returns an object, boolean or int (id) as appropriate.
     *
     * @param $str              Raw query string.
     * @return object|bool      Object containing the result of the query or false if the query fails.
     */
    function query($str){
		if($this->connect()){

			$result = false;
			$act = -1;

			$sql = $this->con->prepare($str);
			// print_r($this->con->errorInfo());

			$fr = trim($str);

			if(preg_match('~^insert\s+int~sim', $str)){

                $sql->execute();
				$result = $this->con->lastInsertId();
				$act = CANDY_SQL_RESULT_INSERT;
			} elseif(preg_match('~^select~sim', $str)){

                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_OBJ);
				$act = CANDY_SQL_RESULT_SELECT;
			} else { 
				$result = $sql->execute();
				$act = CANDY_SQL_RESULT_UNKNOWN;
			}

			do_action('on_sql_query');

			$result = apply_filters('sql_result', $result, $act);
			do_action('on_sql_result', $result, $act);
		}
		return false;
	}

    /**
     *
     * Escapes an unsafe string with proper slashes for sql queries.
     * Please when using query() with insert and/or update, call this on data's.
     *
     * @param $str              The string to escape.
     * @return mixed            Escaped data.
     */
    function escape($str){
		if($this->connect()){
			$str = apply_filters('on_sql_escape', $this->con->quote($str));
		}
		return $str;
	}

    /**
     * Closes a database connection.
     */
    function close(){
		if($this->con != null){
			$this->con->close();
			$this->con = null;

			do_action('on_database_close');
		}
	}

    /**
     *
     * The connection object of the class.
     * Call this to gain raw access to the connection and perform your own operations.
     *
     * @return The database connection.
     */
    function connection(){
		return $this->con;
	}

}



