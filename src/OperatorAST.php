<?php

namespace SQLTrees;

final class OperatorAST extends AST {

	/** @var ?AST Neutral element, when the expressions array is empty. */
	protected $neutral;

	function __construct( string $s, array $exprs, int $plevel, ?AST $neutral ) {

		parent::__construct( $s, $exprs, $plevel );
		$this->neutral = $neutral;
	}

	protected function writeTo( CompiledStatement $b ) : void {

		$n = $this->getChildrenCount();

		if( $n === 0 ) {
			if( $this->neutral ) {
				$this->neutral->writeTo( $b );
			}
		} else {
			$this->getChild( 0 )->cpwriteTo( $b, $this->parenLevel );
			for( $i = 1; $i < $n; $i++ ) {
				$b->addToken( $this->label );
				$this->getChild( $i )->cpwriteTo( $b, $this->parenLevel );
			}
		}
	}
}
