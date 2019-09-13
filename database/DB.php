<?php


namespace modules\database;


use AppConfig;
use RefinerQueryBuilder\Connection;
use RefinerQueryBuilder\QueryExtension\QueryBuilderHandler;
use RefinerQueryBuilder\RefinerQueryBuilderException;


class DB
{
    /**
     * create database connection
     * default database is mysql
     * to change this head over to AppConfiguration file located in the env folder
     * @return QueryBuilderHandler
     * @throws RefinerQueryBuilderException
     */
    public static function hook ()
    {
        $config = AppConfig::get('f_db', 'Admin');
        $db_type_adapter = $config['db_type'];

        try{
            $conn = new Connection($db_type_adapter,$config);
            //throw new RefinerQueryBuilderException('Could not connect to DB');
        }
        catch (RefinerQueryBuilderException $e)
        {
            echo "could not connect to db";
        }

        return new QueryBuilderHandler($conn);
    }

}