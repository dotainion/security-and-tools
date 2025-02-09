<?php

namespace Kyegil\MysqliConnection;

interface MysqliConnectionInterface
{
    /**
     * @return string
     */
    public function getTablePrefix(): string;

    /**
     * @param string $table_prefix
     * @return MysqliConnectionInterface
     */
    public function setTablePrefix(string $table_prefix): MysqliConnectionInterface;

    /**
     * @param array $config
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
    public function select($config): object;

    /**
     * @param array $config
     * * (boolean) insert True to use the INSERT statement
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
    public function save($config): object;

    /**
     * @param string $query
     * @param int $result_mode
     * @return \mysqli_result|bool
     */
    public function query(string $query, int $result_mode = MYSQLI_STORE_RESULT);

}