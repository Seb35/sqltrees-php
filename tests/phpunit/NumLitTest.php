<?php

declare( strict_types=1 );

use SQLTrees\NumLit;
use PHPUnit\Framework\TestCase;

final class NumLitTest extends TestCase
{
	public function testNumLit() : void {

		$str = new NumLit( 0  );
		$stmt = $str->compile();

		$this->assertSame( '?;', $stmt->getTpl() );
		$this->assertSame( [ '0' ], $stmt->getArgs() );
		$this->assertSame( [ 'i' ], $stmt->getTypes() );
		$this->assertSame( [ 'template' => '?;', 'parameters' => [ [ 'i', '0' ] ] ], $stmt->getPreparedStatement() );
	}
}
