<?php

namespace SQLTrees;

class StrLit extends AST {

	function __construct( string $s ) {

		parent::__construct( $s, null, 0 );
	}

	protected function writeTo( CompiledStatement $b ) : void {

		$b->addStringParam( $this->label );
	}
}
