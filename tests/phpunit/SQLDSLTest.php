<?php

declare( strict_types=1 );

use SQLTrees\AST;
use SQLTrees\SQLDSL;
use function SQLTrees\{select};
use PHPUnit\Framework\TestCase;

final class SQLDSLTest extends TestCase
{
	public function testSELECT() : void {

		$this->assertEquals( new AST( 'SELECT', null, 1000 ), SQLDSL::SELECT() );
		$this->assertEquals( new AST( 'SELECT', null, 1000 ), select() );
	}

	public function testSELECT2() : void {

		$this->assertEquals( new AST( 'SELECT', [ new AST( '*', null, 0 ) ], 1000 ), SQLDSL::SELECT( SQLDSL::$star ) );
	}

	public function testSELECT3() : void {

		$this->assertEquals( new AST( 'SELECT', [ new AST( '*', null, 0 ), new AST( '*', null, 0 ) ], 1000 ), SQLDSL::SELECT( SQLDSL::$star, SQLDSL::$star ) );
	}
}
