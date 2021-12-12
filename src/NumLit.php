<?php

declare( strict_types=1 );

namespace SQLTrees;

class NumLit extends AST {

	function __construct( int $s ) {

		parent::__construct( strval( $s ), null, 0 );
	}

	protected function writeTo( CompiledStatement $b ) : void {

		$b->addParam( $this->label, 'i' );
	}
}
