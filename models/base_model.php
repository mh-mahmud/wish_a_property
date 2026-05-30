<?php

abstract class BaseModel
{
    protected $pk = 'id';
    protected $table;

    function __construct()
    {
        // db property removed
    }

    public function changeCurrentTable($tableName) {
        $this->table = $tableName;
    }

    public function get($where, $fields = '*', $order_by = '', $group_by = '')
    {
        if (is_array($where)) {
            $sql = $this->generateSelectQuery($this->table, $fields, $where, $order_by, 0, 1, $group_by);
            // rebuild data from where for removing operator
            $data = $this->processWhereData($where);
            $result = KBPDO::getConn()->executeQuery($sql, $data);
            if ($result === false) {
                return null;
            }
        } else {
            $data = array();
            if (!empty($where)) {
                $data['id'] = $where;
                $sql = "SELECT " . $fields . " FROM " . $this->table . " WHERE " . $this->pk . " = :id";
            } else {
                $sql = "SELECT " . $fields . " FROM " . $this->table;
            }
            if (!empty($group_by)) {
                $sql .= " GROUP BY " . $group_by;
            }
            if (!empty($order_by)) {
                $sql .= " ORDER BY " . $order_by;
            }
            $sql .= " LIMIT 1";
            $result = KBPDO::getConn()->executeQuery($sql, $data);
            if ($result === false) {
                return null;
            }
        }

        return $result->fetch();
    }

    public function getAll($where = '', $fields = '*', $order_by = '', $offset = 0, $limit = -1, $group_by = '')
    {
        $sql = $this->generateSelectQuery($this->table, $fields, $where, $order_by, $offset, $limit, $group_by);
        // rebuild data from where for removing operator
        $data = $this->processWhereData($where);
        $result = KBPDO::getConn()->executeQuery($sql, $data);
        if ($result === false) {
            return null;
        }

        return $result->fetchAll();
    }

    public function getAggregateRow ($sql = '', $fields = 'COUNT(*) total', $where = [], $group_by = '')
    {
        $fields = empty($fields) ? 'COUNT(*) total' : $fields;
        if (!empty($sql)) {
            $sql_from_position = strpos(strtoupper($sql), 'FROM');
            $updated_sql = 'SELECT ' . $fields . ' ' . substr($sql, $sql_from_position, strlen($sql));
            $data = (is_array($where) && count($where) > 0) ? $this->processWhereData($where) : [];
            $result = KBPDO::getConn()->executeQuery($updated_sql, $data);
            if ($result === false) {
                return 0;
            }
        } else if (is_array($where) && count($where) > 0) {
            $sql = $this->generateSelectQuery($this->table, $fields, $where, '', 0, 1, $group_by);
            $data = $this->processWhereData($where);
            $result = KBPDO::getConn()->executeQuery($sql, $data);
            if ($result === false) {
                return 0;
            }
        } else {
            return 0;
        }

        if ($result->columnCount() == 1) {
            return $result->fetchColumn();
        } else {
            return $result->fetch();
        }
    }

    public function getByField($field, $value, $fields = '*', $order_by = '', $limit = 1)
    {
        $data = array();
        $data[$field] = $value;

        return $this->get($data, $fields, $order_by);
    }

    public function getAllByField($field, $value, $fields = '*', $order_by = '', $offset = 0, $limit = -1)
    {
        $data = array();
        $data[$field] = $value;

        return $this->getAll($data, $fields, $order_by, $offset, $limit);
    }

    public function save($data, $where = '', $last_id = true)
    {
        if (!empty($where)) {
            $sql = $this->generateUpdateQuery($this->table, $data, $where);
            $data = $this->processWhereData($where, $data);
            $flag = KBPDO::getConn()->executeQuery($sql, $data);
        } else {
            if (isset($data[0]) && is_array($data[0])) {
                $batch = $this->generateInsertQuery($this->table, $data, true);
                $sql = $batch['sql'];
                $data = $batch['data'];
            } else {
                $sql = $this->generateInsertQuery($this->table, $data);
            }
            $flag = KBPDO::getConn()->executeQuery($sql, $data);

            if ($last_id && $flag) {
                $flag = KBPDO::getConn()->lastInsertId();
            }
        }
        return $flag;
    }

    /*public function saveBatch($data)
    {
        return $this->save($data);
    }

    public function saveByField($data, $field, $value)
    {
        $where = array();
        $where[$field] = $value;

        return $this->save($data, $where);
    }*/

    public function saveByPk($data, $value)
    {
        $where = array();
        $where[$this->pk] = $value;

        return $this->save($data, $where);
    }

    public function remove($id, $where = [])
    {

        if (!empty($id)) {
            $where = [];
            $where[$this->pk] = $id;
        }
        if (is_array($where) && count($where) > 0) {
            $sql = $this->generateDeleteQuery($this->table, $where);
            $data = $this->processWhereData($where);

            return KBPDO::getConn()->executeQuery($sql, $data);
        }

        return false;
    }

    public function removeByField($field, $value)
    {
        $where = array();
        $where[$field] = $value;

        return $this->removeByFieldSet($where);
    }

    public function removeByFieldSet($where)
    {

        $sql = $this->generateDeleteQuery($this->table, $where);
        $data = $this->processWhereData($where);

        return KBPDO::getConn()->executeQuery($sql, $data);
    }

    public function transfer($from_table, $to_table, $from_field, $from_value)
    {

        $data = array();
        $data['value'] = $from_value;
        $sql = "INSERT INTO " . $to_table . " (SELECT * FROM " . $from_table . " WHERE " . $from_field . " = :value)";

        return KBPDO::getConn()->executeQuery($sql, $data);
    }

    public function increment($increment_field, $increment_value = 1, $where_field, $where_value, $operator = '=')
    {

        $commonOperators = array('=', '>', '<', '>=', '<=', '!=', 'LIKE');
        if (!in_array($operator, $commonOperators)) {
            $operator = '=';
        }

        $data = array();
        $data['value'] = $where_value;

        $sql = "UPDATE " . $this->table . " " .
            "SET " . $increment_field . " = " . $increment_field . " + " . $increment_value . " " .
            "WHERE " . $where_field . " " . $operator . " :value";

        return KBPDO::getConn()->executeQuery($sql, $data);
    }

    public function decrement($decrement_field, $decrement_value = 1, $where_field, $where_value, $operator = '=')
    {
        $commonOperators = array('=', '>', '<', '>=', '<=', '!=', 'LIKE');
        if (!in_array($operator, $commonOperators)) {
            $operator = '=';
        }

        $data = array();
        $data['value'] = $where_value;

        $sql = "UPDATE " . $this->table . " " .
            "SET " . $decrement_field . " = " . $decrement_field . " - " . $decrement_value . " " .
            "WHERE " . $where_field . " " . $operator . " :value";

        return KBPDO::getConn()->executeQuery($sql, $data);
    }

    public function incrementAndDecrement($data, $where, $default_action = '+')
    {
        if (!empty($data) && !empty($where)) {
            $fields = array();
            foreach ($data as $key => $value) {
                $action = $default_action != '+' ? '-' : $default_action;

                if (is_array($value) && !empty($value[1])) {
                    $action = $value[1];
                    unset($data[$key]);
                    $data[$key] = $value[0];
                }
                $fields[] = '`' . $key . '` = ' . $key . ' ' . $action . ' :' . $key;
            }

            $fields = implode(', ', $fields);
            $conditions = $this->generateWhereCondition($where, $data);

            $query = "UPDATE " . $this->table . " SET " . $fields . " WHERE " . $conditions;
            $data = $this->processWhereData($where, $data);

            return KBPDO::getConn()->executeQuery($query, $data);
        }

        return false;
    }

    public function fetch($sql, $data = null)
    {
        $result = null;
        if (!empty($sql)) {
            $result = KBPDO::getConn()->executeQuery($sql, $data);
            if ($result === false) {
                return null;
            }
            $result = $result->fetch();
        }

        return $result;
    }

    public function fetchAll($sql, $data = null, $require_total_count = false, $is_search_manager = false)
    {
        $result = null;
        if (!empty($sql)) {
            $data_object = KBPDO::getConn()->executeQuery($sql, $data);

            if ($data_object === false) {
                return null;
            }

            if ($is_search_manager) {
                $result = [];
                $rows = $data_object->fetchAll();
                $result['rows'] = $rows;

                if (strpos($sql, 'SQL_CALC_FOUND_ROWS') !== false) {
                    $total_sql = "SELECT FOUND_ROWS()";
                    $total = KBPDO::getConn()->executeQuery($total_sql)->fetchAll();
                    $result['total'] = $total[0]['FOUND_ROWS()'];
                } elseif ($require_total_count) {
                    $result['total'] = count($rows);
                }
            } else {
                $result = $data_object->fetchAll();
            }
        }

        return $result;
    }

    public function getPDOStatement($sql, $data = null)
    {
        $result = null;
        if (!empty($sql)) {
            $switchToMaster = false;
            if (!empty($sql)) {
                $current_query = strtoupper($sql);
                $operationKeywords = array('INSERT', 'UPDATE', 'DELETE', 'ALTER');

                foreach ($operationKeywords as $keyword) {
                    if (strpos($current_query, $keyword) !== false) {
                        $switchToMaster = true;
                        break;
                    }
                }
            }

            $result = KBPDO::getConn()->executeQuery($sql, $data);
        }

        return $result;
    }

    protected function generateInsertQuery($table, $data, $is_batch = false)
    {
        if ($is_batch) {
            $fields = array_keys(current($data));
            $str = 'INSERT INTO ' . $table . ' (' . implode(',', $fields) . ') VALUES ';

            $insert_query_data = array();
            $i = 0;
            $data_size = count($data);
            foreach ($data as $value) {
                $str .= '(';
                $x = 0;
                $value_size = count($value);

                foreach ($value as $k => $v) {
                    $str .= ':' . $k . $i;
                    $insert_query_data[$k . $i] = $v;

                    if (++$x < $value_size) {
                        $str .= ',';
                    }
                }

                $str .= ')';
                if (++$i < $data_size) {
                    $str .= ',';
                }
            }

            return ['sql' => $str, 'data' => $insert_query_data];
        } else {
            $fields = array();
            $values = array();
            foreach ($data as $key => $value) {
                $fields[] = '`' . $key . '`';
                $values[] = ':' . $key;
            }
            $fields = implode(', ', $fields);
            $values = implode(', ', $values);
            $query = "INSERT INTO " . $table . " (" . $fields . ") VALUES (" . $values . ")";

            return $query;
        }
    }

    protected function generateUpdateQuery($table, $data, &$where)
    {
        $query = '';
        if (!empty($data) && !empty($where)) {
            $fields = array();
            foreach ($data as $key => $value) {
                $fields[] = '`' . $key . '` = :' . $key;
            }

            $fields = implode(', ', $fields);
            $conditions = $this->generateWhereCondition($where, $data);
            $query = "UPDATE " . $table . " SET " . $fields . " WHERE " . $conditions;
        }

        return $query;
    }

    public function generateSelectQuery($table, $fields, &$where, $order_by, $offset, $limit, $group_by = '')
    {
        if ($fields == '') {
            $fields = '*';
        }

        $query = "SELECT " . $fields . " FROM " . $table;
        if ($where) {
            $conditions = $this->generateWhereCondition($where);
            $query .= " WHERE " . $conditions;
        }

        if (!empty($group_by)) {
            $query .= " GROUP BY " . $group_by;
        }

        if ($order_by) {
            $query .= " ORDER BY " . $order_by;
        }

        if ($limit != -1) {
            $query .= " LIMIT " . $offset . ", " . $limit;
        }

        return $query;
    }

    protected function generateDeleteQuery($table, &$where)
    {
        $query = '';
        if (!empty($where)) {
            $conditions = $this->generateWhereCondition($where);
            $query = "DELETE FROM " . $table . " WHERE " . $conditions;
        }

        return $query;
    }

    protected function processWhereData($where, $dataSet = [])
    {
        $data = [];
        if (is_array($dataSet) && count($dataSet) > 0) {
            foreach ($dataSet as $key => $value) {
                //when same field again
                if (array_key_exists($key, $data)) {
                    $key = 'n_' . $key;
                }
                if (is_array($value)) {
                    $data[$key] = $value[0];
                } else {
                    $data[$key] = $value;
                }
            }
        }
        if (is_array($where) && count($where) > 0) {
            foreach ($where as $key => $value) {
                //when same field again
                if (array_key_exists($key, $data)) {
                    $key = 'n_' . $key;
                }
                if (is_array($value)) {
                    $data[$key] = $value[0];
                } else {
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }

    protected function generateWhereCondition(&$where, $data = [], $alias = '')
    {
        $conditions = '';
        if (is_array($where)) {
            $table_alias = !empty($alias) ? '`' . $alias . '`.' : '';
            $and_or_array = array();
            $condition_array = array();
            $where_backup = $where;
            $arr_index = 0;
            foreach ($where_backup as $key => $value) {
                $column_key = $key;
                if (array_key_exists($key, $data) || !empty($alias)) {
                    unset($where[$key]);
                    $key = !empty($alias) ? $alias . '_' . $key : 'n_' . $key;
                    $where[$key] = $value;
                }
                if (is_array($value)) {
                    $operator = strtoupper($value[1]);

                    if (!empty($value[2])) {
                        $column_key = $value[2];
                    }

                    if ($operator == 'IN' || $operator == 'NOT IN') {
                        unset($where[$key]);
                        $condition_array[$arr_index] = $table_alias . '`' . $column_key . '` ' . $operator . ' (' . $value[0] . ')';
                    } elseif ($operator == 'IS') {
                        unset($where[$key]);
                        $is_value = !empty($value[0]) ? $value[0] : 'NULL';
                        $condition_array[$arr_index] = $table_alias . '`' . $column_key . '` ' . $operator . ' ' . $is_value;
                    } elseif (strpos(strtoupper($key), 'DATE') !== false) {
                        $column_key = str_replace("(", "(" . $table_alias . "`", $column_key);
                        $column_key = str_replace(")", "`)", $column_key);
                        unset($where[$key]);
                        $key = preg_replace('/[^0-9a-zA-Z_]/', '', $key);
                        $where[$key] = $value[0];
                        $condition_array[$arr_index] = $column_key . ' ' . $operator . ' :' . $key;
                    } else {
                        $condition_array[$arr_index] = $table_alias . '`' . $column_key . '` ' . $operator . ' :' . $key;
                    }

                    if (!empty($value[3]) && $value[3] == 'OR') {
                        $and_or_array[$arr_index] = 'OR';
                    } else {
                        $and_or_array[$arr_index] = 'AND';
                    }
                } else {
                    $condition_array[$arr_index] = $table_alias . '`' . $column_key . '` = :' . $key;
                    $and_or_array[$arr_index] = 'AND';
                }
                $arr_index++;
            }
            unset($where_backup);

            if (!empty($condition_array)) {
                foreach ($condition_array as $con_key => $con_value) {
                    if (empty($conditions)) {
                        $conditions = $con_value;
                    } else {
                        $conditions .= ' ' . $and_or_array[$con_key] . ' ' . $con_value;
                    }
                }
            }
        }

        return $conditions;
    }

    protected function processFields($fields, $alias)
    {
        $fieldList = '';
        $sqlKeywords = array('COUNT', 'MAX', 'MIN', 'SUM', 'AVG', 'DISTINCT', 'GROUP_CONCAT', 'TRIM', 'CONCAT', 'IFNULL');

        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $columnKey) {
                $containKeyword = false;
                foreach ($sqlKeywords as $keyword) {
                    if (strpos(strtoupper($columnKey), $keyword) !== false) {
                        $containKeyword = true;
                        break;
                    }
                }
                if ($containKeyword) {
                    $pos = strpos($columnKey, '(');
                    if ($pos !== false) {
                        $fieldList .= substr($columnKey, 0, ($pos + 1)) . $alias . '.' . substr($columnKey, ($pos + 1), strlen($columnKey)) . ', ';
                    } else {
                        $fieldList .= $alias . '.' . $columnKey . ', ';
                    }
                } else {
                    $fieldList .= $alias . '.' . $columnKey . ', ';
                }
            }
        } else {
            $fieldList .= $alias . '.*, ';
        }
        if (!empty($fieldList)) {
            $fieldList = substr($fieldList, 0, -2);
        }
        return $fieldList;
    }

    public function getJoinData($tables = [], $order_by = '', $offset = 0, $limit = -1, $group_by = '', $multi_array = 0)
    {
        $primary_tables = '';
        $fields = '';
        $joined = '';
        $where = [];
        $where_condition = '';
        $inner_condition = '';
        $table_count = 0;

        if (count($tables) > 0) {
            foreach ($tables as $table => $table_param) {
                if ($table_count == 0) {
                    $primary_tables = $table . ' ' . $table_param['alias'];
                } else {
                    if (empty($table_param['join'])) {
                        $primary_tables .= ', ' . $table . ' ' . $table_param['alias'];
                        if (isset($table_param['join_on'])) {
                            $inner_condition .= $table_param['join_on'][0] . ' ' . $table_param['join_on'][1] . ' ' . $table_param['join_on'][2] . ' AND ';
                        }
                    } elseif (isset($table_param['join']) && isset($table_param['join_on'])) {
                        $joined .= $table_param['join'] . ' JOIN ' . $table . ' ' . $table_param['alias'] . ' ON ';
                        $joined .= $table_param['join_on'][0] . ' ' . $table_param['join_on'][1] . ' ' . $table_param['join_on'][2] . ' ';
                    }
                }
                if (isset($table_param['fields']) && count($table_param['fields']) > 0) {
                    if (!empty($fields)) {
                        $fields .= ', ';
                    }
                    if ($table_param['fields'][0] == '*') {
                        $fields .= $table_param['alias'] . '.*';
                    } else {
                        $fields .= $this->processFields($table_param['fields'], $table_param['alias']);
                    }
                }
                if (isset($table_param['where']) && count($table_param['where']) > 0) {
                    $where_condition .= $this->generateWhereCondition($table_param['where'], [], $table_param['alias']) . ' AND ';
                    if (count($where) > 0) {
                        $where = array_merge($where, $table_param['where']);
                    } else {
                        $where = $table_param['where'];
                    }
                }
                $table_count++;
            }
        }

        if (empty($fields)) {
            $fields = '*';
        }

        $query = "SELECT " . $fields . " FROM " . $primary_tables . " " . $joined;

        if (!empty($inner_condition) || !empty($where_condition)) {
            $query .= " WHERE ";
        }
        if (empty($where_condition)) {
            $inner_condition = substr($inner_condition, 0, -5);
        } else {
            $where_condition = substr($where_condition, 0, -5);
        }
        $query .= $inner_condition . ' ' . $where_condition;

        if (!empty($group_by)) {
            $query .= " GROUP BY " . $group_by;
        }

        if (!empty($order_by)) {
            $query .= " ORDER BY " . $order_by;
        }

        if ($limit != -1) {
            $query .= " LIMIT " . $offset . ', ' . $limit;
        }

        $data = $this->processWhereData($where);
        $result = KBPDO::getConn()->executeQuery($query, $data);
        if ($result === false) {
            return null;
        }
        if ($limit == 1 && $multi_array == 0) {
            return $result->fetch();
        }

        return $result->fetchAll();
    }

    public function getSQLWithConfig($tables = [], $order_by = '', $group_by = '')
    {
        $primary_tables = '';
        $fields = '';
        $joined = '';
        $where = [];
        $where_condition = '';
        $inner_condition = '';
        $table_count = 0;

        if (count($tables) > 0) {
            foreach ($tables as $table => $table_param) {
                if ($table_count == 0) {
                    $primary_tables = $table . ' ' . $table_param['alias'];
                } else {
                    if (empty($table_param['join'])) {
                        $primary_tables .= ', ' . $table . ' ' . $table_param['alias'];
                        if (isset($table_param['join_on'])) {
                            $inner_condition .= $table_param['join_on'][0] . ' ' . $table_param['join_on'][1] . ' ' . $table_param['join_on'][2] . ' AND ';
                        }
                    } elseif (isset($table_param['join']) && isset($table_param['join_on'])) {
                        $joined .= $table_param['join'] . ' JOIN ' . $table . ' ' . $table_param['alias'] . ' ON ';
                        $joined .= $table_param['join_on'][0] . ' ' . $table_param['join_on'][1] . ' ' . $table_param['join_on'][2] . ' ';
                    }
                }
                if (isset($table_param['fields']) && count($table_param['fields']) > 0) {
                    if (!empty($fields)) {
                        $fields .= ', ';
                    }
                    if ($table_param['fields'][0] == '*') {
                        $fields .= $table_param['alias'] . '.*';
                    } else {
                        $fields .= $this->processFields($table_param['fields'], $table_param['alias']);
                    }
                }
                if (isset($table_param['where']) && count($table_param['where']) > 0) {
                    $where_condition .= $this->generateWhereCondition($table_param['where'], [], $table_param['alias']) . ' AND ';
                    if (count($where) > 0) {
                        $where = array_merge($where, $table_param['where']);
                    } else {
                        $where = $table_param['where'];
                    }
                }
                $table_count++;
            }
        }

        if (empty($fields)) {
            $fields = '*';
        }

        $query = "SELECT " . $fields . " FROM " . $primary_tables . " " . $joined;
        $count_sql = "SELECT COUNT(*) AS total FROM " . $primary_tables . " " . $joined;

        if (!empty($inner_condition) || !empty($where_condition)) {
            $query .= " WHERE ";
            $count_sql .= " WHERE ";
        }
        if (empty($where_condition)) {
            $inner_condition = substr($inner_condition, 0, -5);
        } else {
            $where_condition = substr($where_condition, 0, -5);
        }
        $query .= $inner_condition . ' ' . $where_condition;
        $count_sql .= $inner_condition . ' ' . $where_condition;

        if (!empty($group_by)) {
            $query .= " GROUP BY " . $group_by;
            $count_sql .= " GROUP BY " . $group_by;
        }

        if (!empty($order_by)) {
            $query .= " ORDER BY " . $order_by;
        }

        $data = $this->processWhereData($where);

        $return_result = array();
        $return_result['sql'] = $query;
        $return_result['count_sql'] = $count_sql;
        $return_result['data'] = $data;

        return $return_result;
    }


    public function closeConnection()
    {
        KBPDO::closeConnection();
    }

    public function beginTransaction()
    {
        KBPDO::getConn()->beginTransaction();
    }

    public function commit()
    {
        KBPDO::getConn()->commit();
    }

    public function rollBack()
    {
        KBPDO::getConn()->rollBack();
    }
}
