<?php

/**
 * @author kssl
 * Updated: 08/Nov/2017
 */
class KBPDO extends PDO
{
    public $lastQuery;
    private static $_instance;
    protected $currentDBServer;
    protected $currentDBName;
    public $debug = false;

    public function __construct()
    {
        $dbConf = $this->getDBConfiguration();

        try {
            parent::__construct('mysql:host=' . $dbConf['dbserv'] . ';dbname=' . $dbConf['dbname'] . ';charset=utf8', $dbConf['dbuser'], $dbConf['dbpass'], $dbConf['option']);
            $this->currentDBServer = $dbConf['dbserv'];
            $this->currentDBName = $dbConf['dbname'];
        } catch (PDOException $e) {
            $this->logErrorInFile($e->getMessage(), ABSLPATHROOT . 'logs/mysql/mysql_connect_error_pdo_' . date('Y_m') . '.txt');
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public static function getConn()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getDBConfiguration()
    {
        $dbConfig = array();
        $dbConfig['dbserv'] = DBSERV;
        $dbConfig['dbname'] = DBNAME;
        $dbConfig['dbuser'] = DBUSER;
        $dbConfig['dbpass'] = DBPASS;
        $dbConfig['option'] = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8' COLLATE 'utf8_general_ci'"
        ];

        return $dbConfig;
    }

    public function executeQuery($sql, $data = null, $saveLog = true)
    {
        try {
            /*if (!empty($_SESSION['debug_sql'])) {
                $this->lastQuery = $this->interpolateQuery($sql, $data);echo $this->lastQuery.";<br><br>";
                unset($_SESSION['debug_sql']);
                exit;
            }*/

            if (!$data) {
                $pdoStatement = $this->query($sql);
                $isSuccess = $pdoStatement === false ? false : true;
            } else {
                $pdoStatement = $this->prepare($sql);
                $isSuccess = $pdoStatement->execute($data);
            }
            //$this->lastQuery = $this->interpolateQuery($sql, $data);echo $this->lastQuery.";<br><br>";
            if ($isSuccess === false && $saveLog === true) {
                $this->lastQuery = $this->interpolateQuery($sql, $data);
                $errorInfo = $pdoStatement === false ? $this->errorInfo() : $pdoStatement->errorInfo();
                $data = [
                    'error_info' => json_encode($errorInfo),
                    'debug_backtrace' => json_encode(debug_backtrace()),
                    'query' => $this->lastQuery,
                    'created' => date('Y-m-d H:i:s')
                ];
                $this->logErrorInDb($data);
                $this->logErrorInFile($data);
            }
            if ($isSuccess === false) {
                $pdoStatement = false;
            }
        } catch (PDOException $e) {
            $pdoStatement = false;
            if (LIVESITE != 1) {
                echo "Error Code: " . $e->getCode() . "<br>Error Message: " . $e->getMessage();
            }
        }

        return $pdoStatement;
    }

    public function logErrorInDb($data)
    {
        $fields = array();
        $values = array();
        foreach ($data as $key => $value) {
            $fields[] = '`' . $key . '`';
            $values[] = ':' . $key;
        }
        $fields = implode(', ', $fields);
        $values = implode(', ', $values);
        $sql = "INSERT INTO " . DBNAME . ".log_query_errors (" . $fields . ") VALUES (" . $values . ")";

        $this->executeQuery($sql, $data, false);
    }

    public function logErrorInFile($data, $logfile = "")
    {
        $isQueryError = true;
        if (empty($logfile)) {
            $logfile = ABSLPATHROOT . "logs/kbpdo/queryErrorLog_" . date('Y_m') . ".txt";
            $isQueryError = false;
        }
        /*$lf = fopen($logfile, "a+");
        fwrite($lf, "\n-----------------------------------------------------------------------------------\n");
        fwrite($lf, "Log Time: " . date('Y-m-d H:i:s') . "\n");
        fwrite($lf, "Error Message: ");
        if (is_array($data)) {
            fwrite($lf, print_r($data, true));
        } else {
            fwrite($lf, $data . "\n");
        }
        if (isset($_SESSION['admin_uid'])) {
            fwrite($lf, "Admin UID: " . $_SESSION['admin_uid'] . "\n");
        }
        if (isset($_SESSION['loggedin_userid'])) {
            fwrite($lf, "UID: " . $_SESSION['loggedin_userid'] . "\n");
        }
        if ($isQueryError) {
            fwrite($lf, print_r($_SERVER, true));
        }
        fclose($lf);*/

        $logData = [
            'Log Time' => date('Y-m-d H:i:s'),
            'Error Message' => $data,
        ];

        if (isset($_SESSION['admin_uid'])) {
            $logData['Admin UID'] = $_SESSION['admin_uid'];
        }

        if (isset($_SESSION['loggedin_userid'])) {
            $logData['UID'] = $_SESSION['loggedin_userid'];
        }

        if ($isQueryError) {
            $logData['Server Data'] = $_SERVER;
        }

        require_once ABSLPATHROOT . 'library/kb_event_log_manager.php';
        $kbEventLogManager = new KBEventLogManager();
        $kbEventLogManager->generalLog($logfile, $logData);
    }

    public function interpolateQuery($query, $params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $replace_key = ":$key";
                $replace_value = "NULL";
                if (is_int($value) || is_float($value)) {
                    $replace_value = $value;
                } elseif (is_string($value)) {
                    $replace_value = "'" . $value . "'";
                } elseif (is_array($value)) {
                    $replace_value = "'" . implode("','", $value) . "'";
                }
                $query = str_replace($replace_key, $replace_value, $query);
            }
        }
        return $query;
    }


    static function closeConnection()
    {
        self::$_instance = null;
        self::$_instance = new stdClass();
        self::$_instance->currentDBName = null;
        self::$_instance->currentDBServer = null;
    }

    static function getCurrentDBServer()
    {
        return self::$_instance->currentDBServer;
    }

    static function getCurrentDBName()
    {
        return self::$_instance->currentDBName;
    }
}

?>