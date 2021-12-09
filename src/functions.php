<?php

namespace SQLTrees;

function select( AST ...$elements ) : AST {
	return SQLDSL::select( ...$elements );
}

function from( AST ...$elements ) : AST {
	return SQLDSL::from( ...$elements );
}

function where( AST $c ) : AST {
	return SQLDSL::where( $c );
}

function order_by( AST $o ) : AST {
	return SQLDSL::order_by( $o );
}

function limit( AST $l ) : AST {
	return SQLDSL::limit( $l );
}

function eq( AST $e0, AST $e1 ) : AST {
	return SQLDSL::eq( $e0, $e1 );
}

function operator( AST $e0, string $op, AST $e1 ) : AST {
	return SQLDSL::operator( $e0, $op, $e1 );
}

function id( string $s ) : AST {
	return SQLDSL::id( $s );
}

function str( string $s ) : AST {
	return SQLDSL::str( $s );
}

function num( int $n ) : AST {
	return SQLDSL::num( $n );
}

function sum( AST ...$elements ) : AST {
	return SQLDSL::sum( ...$elements );
}

function tupleArray( array $elements ) : AST {
	return SQLDSL::tupleArray( $elements );
}

function tuple( AST ...$elements ) : AST {
	return SQLDSL::tuple( ...$elements );
}

function andExpr( AST ...$elements ) : AST {
	return SQLDSL::and( ...$elements );
}

function andArray( array $elements ) : AST {
	return SQLDSL::andArray( $elements );
}

function orArray( array $elements ) : AST {
	return SQLDSL::or( $elements );
}
