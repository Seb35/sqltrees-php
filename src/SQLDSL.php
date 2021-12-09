<?php

namespace SQLTrees;

final class SQLDSL {

	/** Allowed SQL identifiers */
	const allowedIdent = '/^[a-zA-Z][a-zA-Z0-9_]*$/';

	private function __construct() {}
	
	public static function num( int $n ) : AST {

		return new AST( (string) $n, null, 0 );
	}

	public static function str( string $s ) : AST {

		return new StrLit( $s );
	}

	public static function id( string $s ) : AST {

		if( !preg_match( self::allowedIdent, $s ) || ReservedWords::isReserved( $s ) ) {
			throw new \InvalidArgumentException( 'Invalid ident: ' . $s );
		}
		return new AST( $s, null, 0 );
	}

	public static $star;

	public static function eq( AST $e0, AST $e1 ) : AST {

		return new OperatorAST( '=', [ $e0, $e1 ], 100, null );
	}

	public static function operator( AST $e0, string $op, AST $e1 ) : AST {

		if( !in_array( $op, [ '=', '>', '>=', '<', '<=', 'LIKE' ] ) ) {
			throw new \InvalidArgumentException( 'Invalid operator: ' . $op );
		}
		return new OperatorAST( $op, [ $e0, $e1 ], 100, null );
	}

	public static function sum( AST ...$exprs ) : AST {

		return new OperatorAST( '+', $exprs, 50, null );
	}

	/**
	 * Tuple
	 *
	 * @param AST[] $elements
	 * @return AST
	 */
	public static function tupleArray( array $elements ) : AST {

		return new OperatorAST( ',', $elements, 200, null );
	}

	/**
	 * Tuple
	 *
	 * @param AST ...$elements
	 * @return AST
	 */
	public static function tuple( AST ...$elements ) : AST {

		return new OperatorAST( ',', $elements, 200, null );
	}

	public static function select( AST ...$elements ) : AST {

		return new AST( 'SELECT', $elements, 1000 );
	}

	public static function from( AST $e ) : AST {

		return new AST( 'FROM', [ $e ], 201 );
	}

	public static function where( AST $c ) : AST {

		return new AST( 'WHERE', [ $c ], 201 );
	}

	public static function order_by( AST $o ) : AST {

		return new AST( 'ORDER BY', [ $o ], 201 );
	}

	public static function limit( AST $l ) : AST {

		return new AST( 'LIMIT', [ $l ], 201 );
	}

	public static $trueExpr;
	public static $falseExpr;

	/**
	 * Operator AND
	 *
	 * @param AST[] $exprs
	 * @return AST
	 */
	public static function andArray( array $exprs ) : AST {

		return new OperatorAST( 'AND', $exprs, 150, self::$trueExpr );
	}

	/**
	 * Operator AND
	 *
	 * @param AST ...$exprs
	 * @return AST
	 */
	public static function and( AST ...$exprs ) : AST {

		return new OperatorAST( 'AND', $exprs, 150, self::$trueExpr );
	}

	/**
	 * Operator OR
	 *
	 * @param AST ...$exprs
	 * @return AST
	 */
	public static function orArray( array $exprs ) : AST {

		return new OperatorAST( 'OR', $exprs, 150, self::$falseExpr );
	}

	/**
	 * Operator OR
	 *
	 * @param AST ...$exprs
	 * @return AST
	 */
	public static function or( AST ...$exprs ) : AST {

		return new OperatorAST( 'OR', $exprs, 150, self::$falseExpr );
	}
}

SQLDSL::$star = new AST( '*', null, 0 );
SQLDSL::$trueExpr = SQLDSL::eq( SQLDSL::num( 1 ), SQLDSL::num( 1 ) );
SQLDSL::$falseExpr = SQLDSL::eq( SQLDSL::num( 0 ), SQLDSL::num( 1 ) );
