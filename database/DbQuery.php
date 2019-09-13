<?php


namespace modules\database;


use RefinerQueryBuilder\RefinerQueryBuilderException;
/*
 * This class simplifies the way you query a database
 * the value of $data should always be an array of key value pairs
 * the key name corresponds with the name of the column
 * Refiner Query Builder Exception is always thrown if anything wrong happens during database
 * interactions
 * where_clause is an array()
 * where_clause[0] =>column ,where_clause[1]=>operator, where_clause[2]=>column identifier/value
 * example: update 'schools where school_id = 2'
 *  updateRow('schools',['school_id','=',2])*/

class DbQuery
{
 public  static  function doInsert($table,$data)
 {
     try{
         $db = DB::hook();
          $db ->table($table)  -> insert($data);

     }catch (RefinerQueryBuilderException $e)
     {
         echo '<pre>Un expected problem occurred during insert'.$e ->getMessage().'</pre>';
     }
 }

 public  static  function fetchRowAll ($table ,$where_clause,$fetch_mode)
 {
     try
     {
         $db = DB::hook();
         if(empty($where_clause)) echo 'where clause empty or null';
         else return $db ->table($table)->select('*') -> where($where_clause[0],$where_clause[1],$where_clause[2])->setFetchMode($fetch_mode)
             ->get();

     }catch (RefinerQueryBuilderException $e)
     {
         echo $e -> getMessage();
     }

 }

 public  static  function fetchTableAll ($table,$fetch_mode)
 {
     try
     {

         $db = DB::hook();
         return $db ->table($table)->select('*')->setFetchMode($fetch_mode)
             ->get();

     }catch (RefinerQueryBuilderException $e)
     {
         echo $e -> getMessage();
     }

 }

 public static function deleteRow ($table,$where_clause)
 {
     try
     {
         $db = DB::hook();
         $db ->table($table) ->where($where_clause) -> delete();

     }catch (RefinerQueryBuilderException $e)
     {
         echo $e -> getMessage();
     }

 }

 public  static  function updateRow($table,$where_clause,$data= [])
 {
     try
     {
         $db = DB::hook();
         $db ->table($table) ->where($where_clause[0],$where_clause[1],$where_clause[2]) -> update($data);

     }catch (RefinerQueryBuilderException $e)
     {
         echo $e -> getMessage();
     }

 }
}