<?php

namespace SQLTrees;

use mysqli;

class AST {

	protected $lp = '(';
	protected $rp = ')';
	protected $label;
	protected $parenLevel;

	private $children;

	function __construct( string $label, ?array $a, int $plevel ) {

		$this->parenLevel = $plevel;
		$this->label = $label;
		$this->children = ( $a === null ) ? [] : $a;
	}

	public function getChildrenCount() : int {

		return count( $this->children );
	}

	public function getChild( int $i ) : AST {

		return $this->children[$i];
	}

	protected function writeTo( CompiledStatement $b ) : void {

		$b->addToken( $this->label );
		$n = $this->getChildrenCount();
		for( $i = 0; $i < $n; $i++ ) {
			$this->getChild( $i )->cpwriteTo( $b, $this->parenLevel );
		}
	}

	protected function pwriteTo( CompiledStatement $b ) : void {

		$b->addToken( $this->lp );
		$this->writeTo( $b );
		$b->addToken( $this->rp );
	}

	protected function cpwriteTo( CompiledStatement $b, int $p ) : void {

		if( $this->parenLevel < $p ) {
			$this->writeTo( $b );
		} else {
			$this->pwriteTo( $b );
		}
	}

	/**
	 * Compile an AST and return a CompiledStatement ready for execution.
	 *
	 * @return CompiledStatement
	 */
	public function compile() : CompiledStatement {

		$b = new CompiledStatement();
		$this->writeTo( $b );
		return $b;
	}

	public function execute_mysqli( mysqli $con ) {
		return $this->compile()->run_mysqli( $con );
	}
}
