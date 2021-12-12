<?php

declare( strict_types=1 );

use SQLTrees\SQLDSL;
use SQLTrees\CompiledStatement;
use function SQLTrees\{select,from,where,order_by,limit,tupleArray,tuple,eq,operator,andExpr,orExp,id,str,num};

if( PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' ) {
	exit( 1 );
}

# TODO add a MySQL user and password with access to databases mysql and information_schema
$dbuser = 'root';
$dbpass = '';

require_once __DIR__ . '/../vendor/autoload.php';

function showSQLStatement( CompiledStatement $stmt ) : string {
	$sql = $stmt->getPreparedStatement();
	$parameters = count( $sql['parameters'] )
		? ' % ( ' . implode( ', ', array_map( function( $x ) { return '(' . $x[0] . ') "' . $x[1] . '"'; }, $sql['parameters'] ) ) . ' )'
		: '';
	return '"' . $sql['template'] . '"' . $parameters;
}


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

###
# Example given in https://connect.ed-diamond.com/MISC/misc-111/zero-sqli-malgre-les-developpeurs
$a = select( SQLDSL::$star, from( id( 'songs' ) ),
	where( andExpr( eq( id( 'yearofsong' ), num( 1964 ) ),
		eq( id( 'songtitle' ), str( 'I\'m a loser' ) ),
		eq( id( 'author' ), str( 'Lennon' ) ) ) ) );

$stmt = $a->compile();
echo showSQLStatement( $stmt ) . "\n";


###
# Another example that should work on any MySQL server
# You have to adapt the credentials below
$a = select( tuple( id( 'db' ) ), from( id( 'db' ) ) );
$stmt = $a->compile();
echo showSQLStatement( $stmt ) . "\n";

# Add password for root in third argument or a MySQL account with access to mysql.db
$mysqli = new mysqli( 'localhost', $dbuser, $dbpass, 'mysql' );
$res = $stmt->run_mysqli( $mysqli );
$array = $res->get_result();
$mysqli->close();

var_dump( $res );
var_dump( $array->fetch_all() );

function getCompiledSQL( $columns, $parameter ) : CompiledStatement {
	return select( tupleArray( array_map( function( $column ) { return id( $column ); }, $columns ) ),
		from( id( 'tables' ) ),
		where( andExpr( operator( id( 'table_name' ), '>=', str( $parameter ) ) ) ),
		order_by( tuple( id( 'table_name' ), id( 'create_time' ) ) ),
		limit( tuple( num( 0 ), num( 10 ) ) )
	)->compile();
}
$mysqli = new mysqli( 'localhost', $dbuser, $dbpass, 'information_schema' );
$columns = [ 'table_schema', 'table_name', 'engine', 'table_rows', 'create_time' ];
$parameter = 'h';
$stmt = getCompiledSQL( $columns, $parameter );
echo showSQLStatement( $stmt ) . "\n";
$res = $stmt->run_mysqli( $mysqli );
$array = $res->get_result();
$mysqli->close();

var_dump( $res );
var_dump( $array->fetch_all() );

unset( $dbuser );
unset( $dbpass );
