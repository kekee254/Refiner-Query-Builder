###ABOUT
Refiner query builder is a minimalistic database abstraction layer, build queries and interact with your DB easily. RCONN(refiner query builder) currently supports 3 database adapters i.e MYSQL, SQLLITE and POSTGRESQL

more features:
 - Query Events
 - Nested Criteria
 - Sub Queries
 - Nested Queries
 - Multiple Database Connections.


#####CLASS DB

create database connection
default database is mysql
to change this head over to AppConfiguration file located in the env folder
@return QueryBuilderHandler
@throws RefinerQueryBuilderException

 You can implement a database connection without utilizing the <b> DB CLASS </b>

```PHP
    new \database\RefinerQueryBuilder\Connection('mysql', $config, 'CONN');  

  //Connection takes 3 parameters
   //param1 database adabpter,
//param2 database configs,
//param3 class alias
-
```
MORE ABOUT DB CONNECTIONS

when creating db configs ensure you return all configs as an array.
the adminConfig.php can be edited with your db credentials then utilize the DB class to create a connection

######example
```PHP
//CREATE A CONNECTION 
DB::hook();
```
 

otherwise create an array of configs
```PHP
$config = array(
            'driver'    => 'mysql', // Db driver
            'host'      => 'localhost',
            'database'  => 'your-database',
            'username'  => 'root',
            'password'  => 'your-password',
            'charset'   => 'utf8', // Optional
            'collation' => 'utf8_unicode_ci', // Optional
            'prefix'    => 'cb_', // Table prefix, optional
            'options'   => array( // PDO constructor options, optional
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_EMULATE_PREPARES => false,
            ),
        );

// create a connection

 new \database\RefinerQueryBuilder\Connection('mysql', $config, 'CONN');
```

#### class auto-loading

To ensure classes are loaded run composer install over composer.json found in database/RefinerQueryBuilder/composer.json. 
composer will create a folder named vendor.

before creating a connection ensure to require the autoloader.
####example

```PHP
require vendor/autoload.php

//then create a connection

new RefinerQueryBuilder/Connection(//supply the correct parameters)
```


### ERRORS AND EXCEPTIONS

When queries are executed successfully data is fetched from db or changes to db occur. During this process(database interaction) errors may occure due to database misconfiguration or parameter mismatch(providing the wrong parameter to a function). In either case a php error is thrown and script halts. No changes or data will be retrieved. 
RefinerQueryBuilder may return a RefinerQueryBuilderException if one occurs


### fetching data 

By default data is fetched a object of standard class but you have an option to change the fetch mode attribute by using the  setFetchMode(//fetchmode)


### Table of Contents

 - [Connection](#connection)
    - [Alias](#alias)
    - [Multiple Connection](#alias)
    - [SQLite and PostgreSQL Config Sample](#sqlite-and-postgresql-config-sample)
 - [Query](#query)
 - [**Select**](#select)
    - [Get Easily](#get-easily)
    - [Multiple Selects](#multiple-selects)
    - [Select Distinct](#select-distinct)
    - [Get All](#get-all)
    - [Get First Row](#get-first-row)
    - [Get Rows Count](#get-rows-count)
 - [**Where**](#where)
    - [Where In](#where-in)
    - [Where Between](#where-between)
    - [Where Null](#where-null)
    - [Grouped Where](#grouped-where)
 - [Group By and Order By](#group-by-and-order-by)
 - [Having](#having)
 - [Limit and Offset](#limit-and-offset)
 - [Join](#join)
    - [Multiple Join Criteria](#multiple-join-criteria)
 - [Raw Query](#raw-query)
    - [Raw Expressions](#raw-expressions)
 - [**Insert**](#insert)
    - [Batch Insert](#batch-insert)
    - [Insert with ON DUPLICATE KEY statement](#insert-with-on-duplicate-key-statement)
 - [**Update**](#update)
 - [**Delete**](#delete)
 - [Transactions](#transactions)
 - [Get Built Query](#get-built-query)
 - [Sub Queries and Nested Queries](#sub-queries-and-nested-queries)
 - [Get PDO Instance](#get-pdo-instance)
 - [Fetch results as objects of specified class](#fetch-results-as-objects-of-specified-class)
 - [Query Events](#query-events)
    - [Available Events](#available-events)
    - [Registering Events](#registering-events)
    - [Removing Events](#removing-events)
    - [Some Use Cases](#some-use-cases)
    - [Notes](#notes)

___

## Connection
Pixie supports three database drivers, MySQL, SQLite and PostgreSQL. You can specify the driver during connection and the associated configuration when creating a new connection. You can also create multiple connections, but you can use alias for only one connection at a time.;
```PHP
// Make sure you have Composer's autoload file included
require 'vendor/autoload.php';

$config = array(
            'driver'    => 'mysql', // Db driver
            'host'      => 'localhost',
            'database'  => 'your-database',
            'username'  => 'root',
            'password'  => 'your-password',
            'charset'   => 'utf8', // Optional
            'collation' => 'utf8_unicode_ci', // Optional
            'prefix'    => 'cb_', // Table prefix, optional
        );

new \Pixie\Connection('mysql', $config, 'CONN');

// Run query
$query = CONN::table('my_table')->where('name', '=', 'Sana');
```

### Alias
When you create a connection:
```PHP
new \Pixie\Connection('mysql', $config, 'MyAlias');
```
`MyAlias` is the name for the class alias you want to use (like `MyAlias::table(...)`), you can use whatever name (with Namespace also, `MyNamespace\\MyClass`) you like or you may skip it if you don't need an alias. Alias gives you the ability to easily access the QueryBuilder class across your application.

When not using an alias you can instantiate the QueryBuilder handler separately, helpful for Dependency Injection and Testing.

```PHP
$connection = new \Pixie\Connection('mysql', $config);
$CONN = new \Pixie\QueryBuilder\QueryBuilderHandler($connection);

$query = $CONN->table('my_table')->where('name', '=', 'Sana');

var_dump($query->get());
```

`$connection` here is optional, if not given it will always associate itself to the first connection, but it can be useful when you have multiple database connections.

### SQLite and PostgreSQL Config Sample
```PHP
new \Pixie\Connection('sqlite', array(
                'driver'   => 'sqlite',
			    'database' => 'your-file.sqlite',
			    'prefix'   => 'cb_',
		    ), 'CONN');
```

```PHP
new \Pixie\Connection('pgsql', array(
                    'driver'   => 'pgsql',
                    'host'     => 'localhost',
                    'database' => 'your-database',
                    'username' => 'postgres',
                    'password' => 'your-password',
                    'charset'  => 'utf8',
                    'prefix'   => 'cb_',
                    'schema'   => 'public',
                ), 'CONN');
```

## Query
You **must** use `table()` method before every query, except raw `query()`.
To select from multiple tables just pass an array.
```PHP
CONN::table(array('mytable1', 'mytable2'));
```


### Get Easily
The query below returns the (first) row where id = 3, null if no rows.
```PHP
$row = CONN::table('my_table')->find(3);
```
Access your row like, `echo $row->name`. If your field name is not `id` then pass the field name as second parameter `CONN::table('my_table')->find(3, 'person_id');`.

The query below returns the all rows where name = 'Sana', null if no rows.
```PHP
$result = CONN::table('my_table')->findAll('name', 'Sana');
```


### Select
```PHP
$query = CONN::table('my_table')->select('*');
```

#### Multiple Selects
```PHP
->select(array('mytable.myfield1', 'mytable.myfield2', 'another_table.myfield3'));
```

Using select method multiple times `select('a')->select('b')` will also select `a` and `b`. Can be useful if you want to do conditional selects (within a PHP `if`).


#### Select Distinct
```PHP
->selectDistinct(array('mytable.myfield1', 'mytable.myfield2'));
```


#### Get All
Return an array.
```PHP
$query = CONN::table('my_table')->where('name', '=', 'Sana');
$result = $query->get();
```
You can loop through it like:
```PHP
foreach ($result as $row) {
    echo $row->name;
}
```

#### Get First Row
```PHP
$query = CONN::table('my_table')->where('name', '=', 'Sana');
$row = $query->first();
```
Returns the first row, or null if there is no record. Using this method you can also make sure if a record exists. Access these like `echo $row->name`.


#### Get Rows Count
```PHP
$query = CONN::table('my_table')->where('name', '=', 'Sana');
$query->count();
```

### Where
Basic syntax is `(fieldname, operator, value)`, if you give two parameters then `=` operator is assumed. So `where('name', 'usman')` and `where('name', '=', 'usman')` is the same.

```PHP
CONN::table('my_table')
    ->where('name', '=', 'usman')
    ->whereNot('age', '>', 25)
    ->orWhere('type', '=', 'admin')
    ->orWhereNot('description', 'LIKE', '%query%')
    ;
```


#### Where In
```PHP
CONN::table('my_table')
    ->whereIn('name', array('usman', 'sana'))
    ->orWhereIn('name', array('heera', 'dalim'))
    ;

CONN::table('my_table')
    ->whereNotIn('name', array('heera', 'dalim'))
    ->orWhereNotIn('name', array('usman', 'sana'))
    ;
```

#### Where Between
```PHP
CONN::table('my_table')
    ->whereBetween('id', 10, 100)
    ->orWhereBetween('status', 5, 8);
```

#### Where Null
```PHP
CONN::table('my_table')
    ->whereNull('modified')
    ->orWhereNull('field2')
    ->whereNotNull('field3')
    ->orWhereNotNull('field4');
```

#### Grouped Where
Sometimes queries get complex, where you need grouped criteria, for example `WHERE age = 10 and (name like '%usman%' or description LIKE '%usman%')`.

Pixie allows you to do so, you can nest as many closures as you need, like below.
```PHP
CONN::table('my_table')
            ->where('my_table.age', 10)
            ->where(function($q)
                {
                    $q->where('name', 'LIKE', '%usman%');
                    // You can provide a closure on these wheres too, to nest further.
                    $q->orWhere('description', 'LIKE', '%usman%');
                });
```

### Group By and Order By
```PHP
$query = CONN::table('my_table')->groupBy('age')->orderBy('created_at', 'ASC');
```

#### Multiple Group By
```PHP
->groupBy(array('mytable.myfield1', 'mytable.myfield2', 'another_table.myfield3'));

->orderBy(array('mytable.myfield1', 'mytable.myfield2', 'another_table.myfield3'));
```

Using `groupBy()` or `orderBy()` methods multiple times `groupBy('a')->groupBy('b')` will also group by first `a` and than `b`. Can be useful if you want to do conditional grouping (within a PHP `if`). Same applies to `orderBy()`.

### Having
```PHP
->having('total_count', '>', 2)
->orHaving('type', '=', 'admin');
```

### Limit and Offset
```PHP
->limit(30);

->offset(10);
```

### Join
```PHP
CONN::table('my_table')
    ->join('another_table', 'another_table.person_id', '=', 'my_table.id')

```

Available methods,

 - join() or innerJoin
 - leftJoin()
 - rightJoin()

If you need `FULL OUTER` join or any other join, just pass it as 5th parameter of `join` method.
```PHP
->join('another_table', 'another_table.person_id', '=', 'my_table.id', 'FULL OUTER')
```

#### Multiple Join Criteria
If you need more than one criterion to join a table then pass a closure as second parameter.

```PHP
->join('another_table', function($table)
    {
        $table->on('another_table.person_id', '=', 'my_table.id');
        $table->on('another_table.person_id2', '=', 'my_table.id2');
        $table->orOn('another_table.age', '>', CONN::raw(1));
    })
```

### Raw Query
You can always use raw queries if you need,
```PHP
$query = CONN::query('select * from cb_my_table where age = 12');

var_dump($query->get());
```

You can also pass your bindings
```PHP
CONN::query('select * from cb_my_table where age = ? and name = ?', array(10, 'usman'));
```

#### Raw Expressions

When you wrap an expression with `raw()` method, Pixie doesn't try to sanitize these.
```PHP
CONN::table('my_table')
            ->select(CONN::raw('count(cb_my_table.id) as tot'))
            ->where('value', '=', 'Ifrah')
            ->where(CONN::raw('DATE(?)', 'now'))
```


___
**NOTE:** Queries that run through `query()` method are not sanitized until you pass all values through bindings. Queries that run through `raw()` method are not sanitized either, you have to do it yourself. And of course these don't add table prefix too, but you can use the `addTablePrefix()` method.

### Insert
```PHP
$data = array(
    'name' => 'Sana',
    'description' => 'Blah'
);
$insertId = CONN::table('my_table')->insert($data);
```

`insert()` method returns the insert id.

#### Batch Insert
```PHP
$data = array(
    array(
        'name'        => 'Sana',
        'description' => 'Blah'
    ),
    array(
        'name'        => 'Usman',
        'description' => 'Blah'
    ),
);
$insertIds = CONN::table('my_table')->insert($data);
```

In case of batch insert, it will return an array of insert ids.

#### Insert with ON DUPLICATE KEY statement
```PHP
$data = array(
    'name'    => 'Sana',
    'counter' => 1
);
$dataUpdate = array(
    'name'    => 'Sana',
    'counter' => 2
);
$insertId = CONN::table('my_table')->onDuplicateKeyUpdate($dataUpdate)->insert($data);
```

### Update
```PHP
$data = array(
    'name'        => 'Sana',
    'description' => 'Blah'
);

CONN::table('my_table')->where('id', 5)->update($data);
```

Will update the name field to Sana and description field to Blah where id = 5.

### Delete
```PHP
CONN::table('my_table')->where('id', '>', 5)->delete();
```
Will delete all the rows where id is greater than 5.

### Transactions

Pixie has the ability to run database "transactions", in which all database
changes are not saved until committed. That way, if something goes wrong or
differently then you intend, the database changes are not saved and no changes
are made.

Here's a basic transaction:

```PHP
CONN::transaction(function ($CONN) {
    $CONN->table('my_table')->insert(array(
        'name' => 'Test',
        'url' => 'example.com'
    ));

    $CONN->table('my_table')->insert(array(
        'name' => 'Test2',
        'url' => 'example.com'
    ));
});
```

If this were to cause any errors (such as a duplicate name or some other such
error), neither data set would show up in the database. If not, the changes would
be successfully saved.

If you wish to manually commit or rollback your changes, you can use the
`commit()` and `rollback()` methods accordingly:

```PHP
CONN::transaction(function ($CONN) {
    $CONN->table('my_table')->insert(array(/* data... */));

    $CONN->commit(); // to commit the changes (data would be saved)
    $CONN->rollback(); // to rollback the changes (data would be rejected)
});
```

### Get Built Query
Sometimes you may need to get the query string, its possible.
```PHP
$query = CONN::table('my_table')->where('id', '=', 3);
$queryObj = $query->getQuery();
```
`getQuery()` will return a query object, from this you can get sql, bindings or raw sql.


```PHP
$queryObj->getSql();
// Returns: SELECT * FROM my_table where `id` = ?
```
```PHP
$queryObj->getBindings();
// Returns: array(3)
```

```PHP
$queryObj->getRawSql();
// Returns: SELECT * FROM my_table where `id` = 3
```

### Sub Queries and Nested Queries
Rarely but you may need to do sub queries or nested queries. Pixie is powerful enough to do this for you. You can create different query objects and use the `CONN::subQuery()` method.

```PHP
$subQuery = CONN::table('person_details')->select('details')->where('person_id', '=', 3);


$query = CONN::table('my_table')
            ->select('my_table.*')
            ->select(CONN::subQuery($subQuery, 'table_alias1'));

$nestedQuery = CONN::table(CONN::subQuery($query, 'table_alias2'))->select('*');
$nestedQuery->get();
```

This will produce a query like this:

    SELECT * FROM (SELECT `cb_my_table`.*, (SELECT `details` FROM `cb_person_details` WHERE `person_id` = 3) as table_alias1 FROM `cb_my_table`) as table_alias2

**NOTE:** Pixie doesn't use bindings for sub queries and nested queries. It quotes values with PDO's `quote()` method.

### Get PDO Instance
If you need to get the PDO instance you can do so.

```PHP
CONN::pdo();
```

### Fetch results as objects of specified class
Simply call `asObject` query's method.

```PHP
CONN::table('my_table')->asObject('SomeClass', array('ctor', 'args'))->first();
```

Furthermore, you may fine-tune fetching mode by calling `setFetchMode` method.

```PHP
CONN::table('my_table')->setFetchMode(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE)->get();
```

### Query Events
Pixie comes with powerful query events to supercharge your application. These events are like database triggers, you can perform some actions when an event occurs, for example you can hook `after-delete` event of a table and delete related data from another table.

#### Available Events

 - before-select
 - after-select
 - before-insert
 - after-insert
 - before-update
 - after-update
 - before-delete
 - after-delete

#### Registering Events

```PHP
CONN::registerEvent('before-select', 'users', function($CONN)
{
    $CONN->where('status', '!=', 'banned');
});
```
Now every time a select query occurs on `users` table, it will add this where criteria, so banned users don't get access.

The syntax is `registerEvent('event type', 'table name', action in a closure)`.

If you want the event to be performed when **any table is being queried**, provide `':any'` as table name.

**Other examples:**

After inserting data into `my_table`, details will be inserted into another table
```PHP
CONN::registerEvent('after-insert', 'my_table', function($queryBuilder, $insertId)
{
    $data = array('person_id' => $insertId, 'details' => 'Meh', 'age' => 5);
    $queryBuilder->table('person_details')->insert($data);
});
```

Whenever data is inserted into `person_details` table, set the timestamp field `created_at`, so we don't have to specify it everywhere:
```PHP
CONN::registerEvent('after-insert', 'person_details', function($queryBuilder, $insertId)
{
    $queryBuilder->table('person_details')->where('id', $insertId)->update(array('created_at' => date('Y-m-d H:i:s')));
});
```

After deleting from `my_table` delete the relations:
```PHP
CONN::registerEvent('after-delete', 'my_table', function($queryBuilder, $queryObject)
{
    $bindings = $queryObject->getBindings();
    $queryBuilder->table('person_details')->where('person_id', $binding[0])->delete();
});
```



Pixie passes the current instance of query builder as first parameter of your closure so you can build queries with this object, you can do anything like usual query builder (`CONN`).

If something other than `null` is returned from the `before-*` query handler, the value will be result of execution and DB will not be actually queried (and thus, corresponding `after-*` handler will not be called either).

Only on `after-*` events you get three parameters: **first** is the query builder, **third** is the execution time as float and **the second** varies:

 - On `after-select` you get the `results` obtained from `select`.
 - On `after-insert` you get the insert id (or array of ids in case of batch insert)
 - On `after-delete` you get the [query object](#get-built-query) (same as what you get from `getQuery()`), from it you can get SQL and Bindings.
 - On `after-update` you get the [query object](#get-built-query) like `after-delete`.

#### Removing Events
```PHP
CONN::removeEvent('event-name', 'table-name');
```

#### Some Use Cases

Here are some cases where Query Events can be extremely helpful:

 - Restrict banned users.
 - Get only `deleted = 0` records.
 - Implement caching of all queries.
 - Trigger user notification after every entry.
 - Delete relationship data after a delete query.
 - Insert relationship data after an insert query.
 - Keep records of modification after each update query.
 - Add/edit created_at and updated _at data after each entry.