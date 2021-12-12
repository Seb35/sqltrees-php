<?php

declare( strict_types=1 );

use SQLTrees\StrLit;
use PHPUnit\Framework\TestCase;

final class StrLitTest extends TestCase
{
	public function testStrLit() : void {

		$str = new StrLit( 'a' );
		$stmt = $str->compile();

		$this->assertSame( '?;', $stmt->getTpl() );
		$this->assertSame( [ 'a' ], $stmt->getArgs() );
		$this->assertSame( [ 's' ], $stmt->getTypes() );
		$this->assertSame( [ 'template' => '?;', 'parameters' => [ [ 's', 'a' ] ] ], $stmt->getPreparedStatement() );
	}
}
