<?php

declare( strict_types=1 );

use SQLTrees\ReservedWords;
use PHPUnit\Framework\TestCase;

final class ReservedWordsTest extends TestCase
{
	public function testSELECT() : void {

		$this->assertSame( true, ReservedWords::isReserved( 'SELECT' ) );
	}

	public function testSeLeCt2() : void {

		$this->assertSame( true, ReservedWords::isReserved( 'SeLeCt' ) );
	}

	public function testWHATEVER() : void {

		$this->assertSame( false, ReservedWords::isReserved( 'WHATEVER' ) );
	}
}
