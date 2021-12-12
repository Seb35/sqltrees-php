<?php

declare( strict_types=1 );

use SQLTrees\AST;
use SQLTrees\OperatorAST;
use PHPUnit\Framework\TestCase;

final class ASTTest extends TestCase
{
	public function testOperatorAST() : void {

		$str = new OperatorAST( 'OR', [
				new OperatorAST( 'AND', [ new AST( 'a', null, 0 ), new AST( 'b', null, 0 ) ], 150, null ),
				new AST( 'c', null, 0 ),
			], 150, null );
		$stmt = $str->compile();

		$this->assertSame( '( a AND b ) OR c;', $stmt->getTpl() );
		$this->assertSame( [], $stmt->getArgs() );
		$this->assertSame( [], $stmt->getTypes() );
	}

	public function testAST() : void {

		$str = new AST( 'SELECT', [
				new AST( '*', null, 0 ),
				new AST( 'FROM', [ new AST( 'db', null, 0 ) ], 201 ),
				new AST( 'WHERE', [ new OperatorAST( 'AND', [], 150, new AST( '1=1', null, 100 ) ) ], 201 ),
			], 1000, null );
		$stmt = $str->compile();

		$this->assertSame( 'SELECT * FROM db WHERE 1=1;', $stmt->getTpl() );
		$this->assertSame( [], $stmt->getArgs() );
		$this->assertSame( [], $stmt->getTypes() );
		$this->assertSame( [ 'template' => 'SELECT * FROM db WHERE 1=1;', 'parameters' => [] ], $stmt->getPreparedStatement() );
	}
}
