<?php

/**
 * Candy-PHP - Code the simpler way.
 *
 * The open source PHP Model-View-Template framework.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2017 Onehyr Technologies Limited
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

/**
 * @see class Form.
 */


function db_select($table, $columns = null, $where = null, $order = null, $limit = null, $where_extra = null){
    global $db;
    return $db->select($table, $columns, $where, $order, $limit, $where_extra);
}

function db_select_or($table, $columns = null, $where = null, $order = null, $limit = null, $where_extra = null){
    global $db;
    return $db->orSelect($table, $columns, $where, $order, $limit, $where_extra);
}

function db_select_like($table, $columns = null, $where = null, $order = null, $limit = null, $where_extra = null){
    global $db;
    return $db->likeSelect($table, $columns, $where, $order, $limit, $where_extra);
}

function db_insert($table, $data){
    global $db;
    return $db->insert($table, $data);
}

function db_insert_unique($table, $data, $exception = []){
    global $db;
    return $db->insertUnique($table, $data, $exception);
}

function db_insert_update($table, $id, $data){
    global $db;
    return $db->insertUpdate($table, $id, $data);
}

function db_update($table, $data, $where = null){
    global $db;
    return $db->update($table, $data, $where);
}

function db_delete($table, $where){
    global $db;
    return $db->delete($table, $where);
}

function db_query($s){
    global $db;
    return $db->query($s);
}

function db_escape($s){
    global $db;
    return $db->escape($s);
}

function db_connection(){
    global $db;
    return $db->connection();
}

function db_close(){
    global $db;
    return $db->close();
}

function db_errors(){
    global $db;
    return $db->errors;
}


// When adding DB concept, use ? to represent unknown value.

/**
 *
 * Adds a database query concept.
 *
 * @param $name
 * @param $function
 */
function add_db(){
    global $__dbs__;
    
    $args = func_get_args();
    
    if(count($args) < 3){
        
        throw new Exception('Invalid Database concept.');
    }
    
    $name = $args[0];
    $type = $args[1];
    
    $args = array_delete($args, $args[0]);
    $args = array_delete($args, $args[0]);
    
    $__dbs__[$name] = ['type' => $type, 'args' => $args];
}

/**
 *
 * Call a database query concept.
 *
 * @param $name
 * @param array $unknowns       Array of replacements for '?' in the order they appear in the concept.
 * @return bool|object
 */
function call_db($name, $unknowns = []){
    global $__dbs__;
    
    if(isset($__dbs__[$name])){
        
        try{
            
            $type = $__dbs__[$name]['type'];
            $args = $__dbs__[$name]['args'];

            $unknown_state = -1;

            if(in_array($type, ['select', 'select_or', 'select_like'])){

                $table = $args[0];
                $columns = isset($args[1]) ? $args[1] : null;
                $where = isset($args[2]) ? $args[2] : null;
                $order = isset($args[3]) ? $args[3] : null;
                $limit = isset($args[4]) ? $args[4] : null;
                $where_extra = isset($args[5]) ? $args[5] : null;

                if(!empty($columns)){
                    foreach($columns as $x => $column){

                        if(strpos($column, '?') > -1){
                            $unknown_state++;
                            $columns[$x] = str_replace('?', $unknowns[$unknown_state], $column);
                        }
                    }
                }

                if(!empty($where)){
                    foreach($where as $key => $value){

                        if(strpos($value, '?') > -1){
                            $unknown_state++;
                            $where[$key] = str_replace('?', $unknowns[$unknown_state], $value);
                        }
                    }
                }

                if(strpos($order, '?') > -1){
                    $unknown_state++;
                    $order = str_replace('?', $unknowns[$unknown_state], $order);
                }

                if(strpos($limit, '?') > -1){
                    $unknown_state++;
                    $limit = str_replace('?', $unknowns[$unknown_state], $limit);
                }

                if(strpos($where_extra, '?') > -1){
                    $unknown_state++;
                    $where_extra = str_replace('?', $unknowns[$unknown_state], $where_extra);
                }

                switch($type){

                    case 'select': return db_select($table, $columns, $where, $order, $limit, $where_extra);
                    case 'select_or': return db_select_or($table, $columns, $where, $order, $limit, $where_extra);
                    case 'select_like': return db_select_like($table, $columns, $where, $order, $limit, $where_extra);
                }
            }

            switch($type){

                case 'insert':

                    $table = $args[0];
                    $data = isset($args[1]) ? $args[1] : null;

                    if(!empty($data)){
                        foreach($data as $key => $value){

                            if(strpos($value, '?') > -1){
                                $unknown_state++;
                                $data[$key] = str_replace('?', $unknowns[$unknown_state], $value);
                            }
                        }
                    }

                    return db_insert($table, $data);

                    break;

                case 'insert_unique':

                    $table = $args[0];
                    $data = isset($args[1]) ? $args[1] : null;
                    $exception = isset($args[2]) ? $args[2] : null;

                    if(!empty($data)){
                        foreach($data as $key => $value){

                            if(strpos($value, '?') > -1){
                                $unknown_state++;
                                $data[$key] = str_replace('?', $unknowns[$unknown_state], $value);
                            }
                        }
                    }

                    if(!empty($exception)){
                        foreach($exception as $x => $column){

                            if(strpos($column, '?') > -1){
                                $unknown_state++;
                                $exception[$x] = str_replace('?', $unknowns[$unknown_state], $column);
                            }
                        }
                    }

                    return db_insert_unique($table, $data, $exception);

                    break;

                case 'insert_update':

                    $table = $args[0];
                    $id = isset($args[1]) ? $args[1] : null;
                    $data = isset($args[2]) ? $args[2] : null;

                    if(strpos($id, '?') > -1){
                        $unknown_state++;
                        $id = str_replace('?', $unknowns[$unknown_state], $id);
                    }

                    if(!empty($data)){
                        foreach($data as $key => $value){

                            if(strpos($value, '?') > -1){
                                $unknown_state++;
                                $data[$key] = str_replace('?', $unknowns[$unknown_state], $value);
                            }
                        }
                    }

                    return db_insert_update($table, $id, $data);

                    break;

                case 'update':

                    $table = $args[0];
                    $data = isset($args[1]) ? $args[1] : null;
                    $where = isset($args[2]) ? $args[2] : null;

                    if(!empty($data)){
                        foreach($data as $key => $value){

                            if(strpos($value, '?') > -1){
                                $unknown_state++;
                                $data[$key] = str_replace('?', $unknowns[$unknown_state], $value);
                            }
                        }
                    }

                    if(!empty($where)){
                        foreach($where as $key => $value){

                            if(strpos($value, '?') > -1){
                                $unknown_state++;
                                $where[$key] = str_replace('?', $unknowns[$unknown_state], $value);
                            }
                        }
                    }

                    return db_update($table, $data, $where);

                    break;

                case 'delete':

                    $table = $args[0];
                    $where = isset($args[1]) ? $args[1] : null;

                    if(!empty($where)){
                        foreach($where as $key => $value){

                            if(strpos($value, '?') > -1){
                                $unknown_state++;
                                $where[$key] = str_replace('?', $unknowns[$unknown_state], $value);
                            }
                        }
                    }

                    return db_delete($table, $where);

                case 'query':

                    $query = $args[0];
                    $final = '';

                    $queries = explode('?', $query);
                
                    if(count($queries) > count($unknowns) + 1){

                         bad_model_error('&quot;' . $name . '&quot; database concept.');
                    }

                    for($i = 1; $i < count($queries); $i++){

                        $s = $queries[$i - 1];

                        if(preg_match('/=\s*$/sim', $s)){

                            $ur = db_escape($unknowns[$i - 1]);
                        } else {

                            $ur = $unknowns[$i - 1];
                        }

                        $final .= $s . $ur;
                    }

                    return db_query($final);
            }
        } catch(Exception $e){
            
            bad_model_error('&quot;' . $name . '&quot; database concept.');
        }
        
    } else {
        
        model_error('Database &quot;' . $name . '&quot;');
    }
}


