<?php
/**
 * Mysqli Connection
 * by Kay-Egil Hauan
 */
namespace Kyegil\MysqliConnection;

use Exception;
use mysqli;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 *
 */
class MysqliConnection extends mysqli implements MysqliConnectionInterface {

    /**
     * @var string[]
     * @see https://dev.mysql.com/doc/refman/8.0/en/logical-operators.html
     */
    public static $logicalOperators = ['and', 'or', 'xor', 'not', '&&', '||', '!'];

    /**
     * The prefix will be added to all table names, and should end with underscore '_' if used
     *
     * @var string
     */
    public $table_prefix = '';

    /**
     * MysqliConnection constructor.
     */
    public function __construct(
        $hostname = null,
        $username = null,
        $password = null,
        $database = null,
        $port = null,
        $socket = null
    ) {
        parent::__construct($hostname, $username, $password, $database, $port, $socket);
        $this->query("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
        $this->query("SET TIME_ZONE = '-0:00'"); // All time is saved as GMT and converted later
        $this->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $this->logQuery('MysqliConnection initialised');
    }

    /** @var LoggerInterface */
    protected $logger;

    /**
     * Array Fields
     *
     * Format array of field names as string list
     *
     * @param string|array $fields array of field names
     * @return string sql-syntax
     * @throws Exception
    */
    public function listFields(array $fields): string
    {
        $set = [];
        foreach($fields as $alias => $field ) {
            $field = explode('.', $field);
            $field = implode('.', $field);

            $set[] = $field . ( !is_numeric( $alias )  ? (' AS ' . $alias) : '');
        }

        return implode(', ', $set);
    }

    /**
     * @param object $config
     * @param array $bindValues
     * @return string
     * @throws Exception
     */
    public function prepareSelectQueryFromConfig(object $config, array &$bindValues = []) {
        settype($config->distinct,  'boolean');
        settype($config->flat,   'boolean');

        settype($config->fields,  'array');
        settype($config->groupfields, 'array');
        settype($config->limit,   'string');
        settype($config->source,  'string');
        settype($config->sql,   'string');

        $config->where = $config->where ?? [];
        $config->having = $config->having ?? [];
        $config->orderfields = isset($config->orderfields) && is_array($config->orderfields) ?
            implode(',', $config->orderfields)
            : ($config->orderfields ?? '');
        if (substr_count($config->limit, ',') == 1) {
            $config->limit = strstr($config->limit, ',', true)
                . ',' . (int)str_replace(',', '', strstr($config->limit, ','));
        }
        else $config->limit = $config->limit ? intval($config->limit) : '';

        if(!$config->source) {
            throw new Exception('No source parameter given');
        }

        $sql = 'SELECT '
            . ($config->distinct ? 'DISTINCT ' : '')
            . ($config->fields ? "{$this->listFields($config->fields)}\n" : "*\n")
            . "FROM {$config->source}\n"
            . ($config->where  ? "WHERE {$this->prepareWhereQuery('and', (array)$config->where, $bindValues)}\n" : "")
            . ($config->groupfields ? "GROUP BY {$this->listFields($config->groupfields)}\n" : "")
            . ($config->having ? "HAVING {$this->prepareWhereQuery('and', (array)$config->having, $bindValues)}\n" : "")
            . ($config->orderfields ? "ORDER BY {$config->orderfields}\n" : "")
            . ($config->limit ? "LIMIT {$config->limit}\n" : "")
        ;
        return $sql;
    }

    /**
     * @param array|object $config
     * * (boolean) flat Return the one-field result as a one-dimensional array
     * * (boolean) returnQuery Returns the ready-build query as the sql property
     * * (string) sql A fully prepared SQL SELECT query. This will override all of the following config options:
     * * (string[]|array[]) fields All the required field names provided as a string or as `alias` => `fieldname`
     * * (string[]) groupfields All the fields included in the GROUP BY statement
     * * (string[]) orderfields All the fields included in the ORDER BY statement
     * * (string) source The FROM statement
     * * (boolean) distinct Only return distinct rows
     * * (string) class = 'stdClass' The object class for the resulting lines
     * * (int|string) limit The LIMIT statement provided as integer or as comma-separated integers
     * * (string[]) where The WHERE statements provided as an array of [`field` => 'value'] or simply as [expression]
     * * (string[]) having The HAVING statements provided as an array of [`field` => 'value'] or simply as [expression]
     *
     * @return object
     * * (boolean) success True if the query was successful
     * * (object|string[]) data The result data. If 'flat' was set to true, only the first columns are returned as a one-dimensional array. Otherwise an array of objects will be returned
     * * (int) totalRows The number of valid results as if LIMIT was not provided
     * * (string) sql If 'returnQuery' was set to true, then the query used to create the result will be returned here.
     * * (string) msg Any error messages if the query was unsuccessful.
     */
    public function select($config): stdClass {
        /** @var object $config */
        settype($config, 'object');
        $bindValues = [];

        settype($config->flat,   'boolean');
        settype($config->returnQuery, 'boolean');

        settype($config->limit,   'string');

        if(!isset($config->class) || !class_exists($config->class)) {
            $config->class = 'stdClass';
        }

        $result = (object)array(
            'success' => true,
            'msg'=> "",
            'data' => null,
            'sql' => null
        );

        $sql = $config->sql ?? $this->prepareSelectQueryFromConfig($config, $bindValues);

        if($config->returnQuery) {
            $result->sql = $sql;
        }
        $countSql = $sql;
        if($config->limit) {
            if (strpos($countSql, 'LIMIT')) {
                $countSql = strstr($sql, 'LIMIT', true);
            }
            else {
                $sql .= "\nLIMIT " . $config->limit;
            }
        }
        $countStatement = $this->prepare($countSql);
        if (!$countStatement) {
            throw new Exception($this->error);
        }
        $statement = $this->prepare($sql);
        if (!$statement) {
            throw new Exception($this->error);
        }
        $types = str_repeat('s', count($bindValues));
        if($bindValues) {
            $countStatement->bind_param($types, ...$bindValues);
            $statement->bind_param($types, ...$bindValues);
        }
        if ($config->limit) {
            $countStatement->execute();
            $countResult = $countStatement->get_result();
            if (!$countResult) {
                throw new Exception($this->error);
            }
            $result->totalRows = $countResult->num_rows;
            $countResult->close();
        }
        $countStatement->close();

        $statement->execute();
        $resultSet = $statement->get_result();
        if (!$resultSet) {
            throw new Exception($this->error);
        }
        $result->data = [];
        $result->success = true;
        $this->logQuery($sql, $bindValues);

        if($config->flat) {
            while($arr = $resultSet->fetch_row()) {
                $result->data[] = $arr[0];
            }
        }
        else {
            while($arr = $resultSet->fetch_object($config->class)) {
                $result->data[] = ($arr);
            }
        }
        $result->totalRows = $result->totalRows ?? count($result->data);
        $resultSet->close();
        $statement->close();
        return $result;
    }

    /**
     * Escape string
     *
     * @param mixed $value value to be escaped
     * @return mixed escaped if value is string, unescaped otherwise
     * @throws Exception thrown if array provided as value
     */
    public function escape($value)
    {
        if(is_string($value)) {
            return $this->real_escape_string($value);
        }

        return $value;
    }

    /**
     * @param array $config
     * * (boolean) insert True to use the INSERT statement
     * * (boolean) replace True to use the REPLACE statement
     * * (boolean) update True to use the UPDATE statement
     * * (boolean) updateOnDuplicateKey True to use UPDATE ON DUPLICATE KEY
     * * (boolean) returnQuery Returns the ready-build query as the sql property
     * * (int|string) id. the resulting primary key if known in advance. This will be returned for reference. If not provided, the increment id of an INSERT query will be returned
     * * (string) set The SET statement
     * * (string) table The table that we are updating or inserting to
     * * (int|string) limit The LIMIT statement for an update, provided as integer or as comma-separated integers
     * * (string[]|array[]) fields The fields to insert or update provided as `field` => 'value'. Values will be escaped
     * * (string[]) where The WHERE statements provided as an array of [`field` => 'value'] or simply as [expression]
     *
     * @return object
     * * (boolean) success True if the query was successful
     * * (string) sql If 'returnQuery' was set to true, then the query used to create the result will be returned here.
     * * (string) msg Any error messages if the query was unsuccessful.
     */
    public function save($config): stdClass
    {
        $result = (object)array(
            'success' => true,
            'msg'=> ""
        );
        $bindValues = [];

        /** @var object $config */
        settype($config, 'object');

        settype($config->insert,              'boolean');
        settype($config->replace,              'boolean');
        settype($config->update,              'boolean');
        settype($config->updateOnDuplicateKey,'boolean');
        settype($config->returnQuery,         'boolean');

        settype($config->test,                'string');
        settype($config->id,                  'string');
        settype($config->set,                 'string');
        settype($config->table,               'string');
        settype($config->limit,               'string');

        settype($config->fields,               'array');

        $config->where = $config->where ?? [];

        if (!($config->table)) {
            throw new Exception("No target table given");
        }
        if (!$config->fields && !$config->insert) {
            throw new Exception("No fields nor data to save");
        }
        if (!$config->update && !$config->insert && !$config->replace) {
            throw new Exception("Asked neither to insert, replace nor update");
        }
        if ($config->update && !$config->where) {
            throw new Exception("'WHERE' limitations required when updating table");
        }
        foreach ($config->fields as $value) {
            if(is_array($value)) {
                throw new Exception("Value can not be array: " . var_export($value, true));
            }
        }

        $update = "UPDATE $config->table\n";
        $insert = "INSERT INTO $config->table\n";
        $replace = "REPLACE INTO $config->table\n";
        $fields = [];
        foreach ($config->fields as $key => $value) {
            if (is_null($value)) {
                $fields[] = "$key = NULL\n";
            }
            else if (is_numeric($key)) {
                $fields[] = $value;
            }
            else {
                $fields[] = "$key = ?\n";
                $bindValues[] = $value;
            }
        }
        $set = '';
        $updateOnDuplicateKey = '';
        if ($config->updateOnDuplicateKey) {
            $updateOnDuplicateKey = "ON DUPLICATE KEY UPDATE\n" . implode(', ', $fields);
        }
        else {
            $set = "SET\n" . implode(', ', $fields);
        }

        if ($config->insert && $updateOnDuplicateKey) {
            $sql = $insert . $updateOnDuplicateKey;
        }
        else if ($config->insert) {
            $sql = $insert . $set;
        }
        else if ($config->replace) {
            $sql = $replace . $set;
        }
        else {
            $where = $config->where ? "WHERE\n" . $this->prepareWhereQuery('and', $config->where, $bindValues) . "\n" : '';
            $limit = $config->limit ? "LIMIT {$config->limit}" : '';
            $sql = $update . $set . $where . $limit;
        }

        if($config->returnQuery) {
            $result->sql = $sql;
        }
        $this->logQuery($sql, $bindValues);

        $statement = $this->prepare($sql);
        if (!$statement) {
            throw new Exception($this->error);
        }
        if($bindValues) {
            $types = str_repeat('s', count($bindValues));
            $statement->bind_param($types, ...$bindValues);
        }

        if (!$statement->execute()) {
            throw new Exception($statement->error);
        }
        $statement->close();
        $result->success = true;
        if ($config->update) {
            $result->id = $config->id;
        }
        else {
            $result->id = $this->insert_id ?: $config->id;
        }
        return $result;
    }

    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return MysqliConnection
     */
    public function setLogger(LoggerInterface $logger): MysqliConnection
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Log the query for debugging
     *
     * @param string $sql
     * @param array $context
     * @return $this
     */
    public function logQuery(string $sql, array $context = []): MysqliConnection
    {
        $logger = $this->getLogger();
        if ($logger) {
            $logger->debug($sql, $context);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTablePrefix(): string
    {
        return $this->table_prefix;
    }

    /**
     * @param string $table_prefix
     * @return MysqliConnectionInterface
     */
    public function setTablePrefix(string $table_prefix): MysqliConnectionInterface
    {
        $this->table_prefix = $table_prefix;
        return $this;
    }

    /**
     * Prepare Where Element
     *
     * Preparing a combination of predicates for use in a 'where' or 'having'-condition
     * Each element can be a ready-made string predicate,
     * or an array consisting of expression => value.
     * The expression can be a prepared string using ? as placeholders,
     * or end with a comparison operator:
     * one of '=', '>', '<', '<>', '<=', '>=', 'IS', 'NOT', 'LIKE' or 'IN'
     * If no comparison operator is given, '=' or 'IS' will be added.
     *
     * Expressions can be combined logically
     * using 'and', 'or', 'xor', 'not', '&&', '||' or '!'
     * as the element key
     *
     * @param string $expression
     * @param string|string[]|array[] $value
     * @param array $bindValues
     * @return string
     * @throws Exception
     */
    public function prepareWhereQuery(
        string $expression,
               $value,
        array &$bindValues
    ): string
    {
        /*
         * String expressions are returned as they are
         */
        if (is_numeric($expression) && is_string($value)) {
            return $value;
        }

        /*
         * Numeric arrays are treated as 'and' combinations
         */
        if (is_numeric($expression)) {
            return $this->prepareWhereQuery('and', $value, $bindValues);
        }

        /*
         * Combinations
         */
        if(in_array(trim(strtolower($expression)), self::$logicalOperators)) {
            settype($value, 'array');
            /** @var string[] $predicatesCombination */
            $predicatesCombination = [];
            foreach ($value as $index => $subValue) {
                if (is_array($subValue) && count($subValue) == 1 && is_numeric($index)) {
                    /* We can skip one bracket level */
                    $predicatesCombination[] = $this->prepareWhereQuery(key($subValue), current($subValue), $bindValues);
                }
                else {
                    $predicatesCombination[] = $this->prepareWhereQuery($index, $subValue, $bindValues);
                }
            }
            return "(" . implode(")\n" . strtoupper($expression) . " (", $predicatesCombination) . ")\n";
        }

        $predicate = $this->preparePredicate($expression, $value);
        if (is_null($value)) {
            $value = [];
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        if (substr_count($predicate, '?') != count($value)) {
            throw new Exception('Number of values (' . count($value) . ") don't match number of placeholders (" . substr_count($predicate, '?'). "): " . $expression . '. ');
        }
        $bindValues = array_merge($bindValues, array_values($value));
        return $predicate;
    }

    /**
     * Prepare predicate
     *
     * Prepares a Sql statement
     *
     * @param string $expression left part of the statement
     * @param mixed|null $value value that may need escaping
     * @return string Sql predicate
     * @throws Exception
     */
    protected function preparePredicate(string $expression = '', $value = null): string
    {
        if(
            strpos($expression, '?') === false
            && !in_array(mb_substr($expression, -1), ['=', '<', '>'])
            && !in_array(mb_substr($expression, -2), ['<=', '>=', '<>'])
            && strtoupper( mb_substr($expression, -3) ) != ' IS'
            && strtoupper( mb_substr($expression, -4) ) != ' NOT'
            && strtoupper( mb_substr($expression, -5) ) != ' LIKE'
            && strtoupper( mb_substr($expression, -3) ) != ' IN'
        ) {
            switch (gettype($value)) {
                case 'NULL':
                    $expression .= ' IS';
                    break;
                case 'array':
                    $expression .= ' IN';
                    break;
                default:
                    $expression .= ' =';
            }
        }

        if (strtoupper( mb_substr($expression, -3) ) == ' IN') {
            $value = is_array($value) ? $value : [$value];
            if (count($value)) {
                $expression .= '(' . str_repeat('?,', count($value)-1) . '?)';
        }
            else {
                // This subquery should always return an empty set,
                // so that the IN() condition will evaluate to false
                $expression .= '(SELECT NULL FROM dual WHERE FALSE)';
            }
        }
        else if (strpos($expression, '?') === false){
            $expression .= is_null($value) ? ' NULL' : ' ?';
        }
        return $expression;
    }

    /**
     * @param array|object $config
     * @return stdClass
     * @throws Exception
     * @deprecated
     * @see MysqliConnection::select()
     */
    public function arrayData($config): object
    {
        return $this->select($config);
    }

    /**
     * @param array|object $config
     *  ->insert bool
     *  ->update bool
     *  ->updateOnDuplicateKey bool
     *  ->returnQuery bool
     *  ->test string
     *  ->id int|string
     *  ->set string
     *  ->table string
     *  ->groupfields string
     *  ->fields array
     *  ->where string
     * @return stdClass
     * @throws Exception
     * @deprecated
     * @see MysqliConnection::save()
     */
    public function saveToDb($config): object
    {
        return $this->save($config);
    }
}
