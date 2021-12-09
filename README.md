SQLTrees (php)
==============

This is a PHP version of the Java library [SQLTrees](https://github.com/Orange-Cyberdefense/sqltrees) aiming at annihilating (possibility of) SQL injections and at the same time keeping the whole expressiveness of SQL. You can read (in French) [this introductory article](https://connect.ed-diamond.com/MISC/misc-111/zero-sqli-malgre-les-developpeurs), or read below a summary.


Example
=======

In the MySQL system database `information_schema`, take the following SQL request containing a parameter in the WHERE provided by the application to let the user jump at some point in the list:
``lang=sql`
SELECT table_schema, table_name, engine, table_rows, create_time FROM tables WHERE table_name >= 'parameter' ORDER BY table_name, create_time LIMIT 0,10;
```

In a trivial example like this one, the request can be rewritten as a parametrised request:
```lang=sql
SELECT table_schema, table_name, engine, table_rows, create_time FROM tables WHERE table_name >= ? ORDER BY table_name, create_time LIMIT 0,10;
-- with the parameters: ('parameter')
``

But if now we want let the user choose the requested columns, we write in PHP:
```lang=php
function getParametrisedSQL( $columns, $parameter ) {
	return [
		'sql' => 'SELECT ' . implode( ', ', $columns ) . ' FROM tables WHERE tables_name >= ? ORDER BY table_name, create_time LIMIT 0,10;',
		'parameters' => [ $parameter ],
	];
}
```
And now the name of the columns could introduce a SQL injection, if the columns names are not sufficiently sanitised beforehand, even if we use a parametrised SQL request.

During an audit, all functions manipulating some parts of SQL requests must be carefully examinated to be sure no one has some escaping issues.

**With this library**, the previous parametrised function would be rewritten like this:
```lang=php
use SQLTrees;

function getCompiledSQL( $columns, $parameter ) : CompiledStatement {
	return select( tupleArray( array_map( function( $column ) { return id( $column ); }, $columns ),
		from( id( 'tables' ) ),
		where( operator( id( 'table_name' ), '>=', str( $parameter ) ) ),
		order_by( tuple( id( 'table_name' ), id( 'create_time' ) ) ),
		limit( tuple( num( 0 ), num( 10 ) ) )
	)->compile();
}

$conn = new mysqli( 'localhost', 'manager', 'password', 'information_schema' );
$stmt = getCompiledSQL( $columns, $parameter )->run_mysqli( $conn );
```

