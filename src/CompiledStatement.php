<?php

declare( strict_types=1 );

namespace SQLTrees;

use \mysqli;
use \mysqli_stmt;

final class CompiledStatement {

	/** @var string */
	private $tpl = '';
	/** @var string[] */
	private $args = [];
	/** @var string[] */
	private $types = [];

	public function getTpl() : string {

		return trim( $this->tpl ) . ';';
	}

	public function getArgs() : array {

		return $this->args;
	}

	public function getTypes() : array {

		return $this->types;
	}

	/**
	 * @param string $s Stringed value
	 * @param string $type Original type of the value in [ 's', 'i', 'd', 'b' ]
	 */
	public function addParam( string $s, string $type ) : void {

		$this->tpl .=  '? ';
		$this->args[] = $s;
		$this->types[] = $type;
	}

	public function addStringParam( string $s ) : void {

		$this->tpl .=  '? ';
		$this->args[] = $s;
		$this->types[] = 's';
	}

	public function addToken( string $repr ) : void {

		$this->tpl .= $repr;
		$this->tpl .= ' ';
	}

	/**
	 * Get a (compiled) prepared statement.
	 *
	 * The prepared statement was compiled from AST with one string and two arrays:
	 *   - string query: the template SQL query with ? as parameters,
	 *   - array args: the parameters of the template SQL query,
	 *   - array types: the types of the parameters.
	 *
	 * The returned array has two keys:
	 *   - template: a string representing the template SQL query,
	 *   - parameters: an array whose each value is [ (string) $type, (string) $arg ]:
	 *       - type: is a letter in [ 's', 'i', 'd', 'b' ] for the type of the parameter,
	 *       - arg: is a string for the parameter.
	 *
	 * @return array
	 */
	public function getPreparedStatement() : array {

		$n = count( $this->args );
		$parameters = [];
		for( $i = 0; $i < $n; $i++ ) {
			$parameters[] = [ $this->types[$i], $this->args[$i] ];
		}

		return [
			'template' => $this->getTpl(),
			'parameters' => $parameters,
		];
	}

	/**
	 * Get a (compiled) prepared statement.
	 *
	 * The prepared statement was compiled from AST with one string and two arrays:
	 *   - string query: the template SQL query with ? as parameters,
	 *   - array args: the parameters of the template SQL query,
	 *   - array types: the types of the parameters.
	 *
	 * @param mysqli $con MySQL connection
	 * @return mysqli_stmt MySQL prepared statement
	 */
	public function getPreparedStatement_mysqli( mysqli $con ) : mysqli_stmt {

		$stmt = new mysqli_stmt( $con, $this->getTpl() );
		$types = implode( '', $this->types );
		if( $types ) {
			$stmt->bind_param( $types, ...$this->args );
		}
		return $stmt;
	}

	/**
	 * Execute a (compiled) prepared statement against a MySQL connection.
	 *
	 * A mysqli_stmt is returned to be able to read detailled errors and warnings.
	 * Except error management, the following classical instructions could be
	 * used to obtain the result:
	 *   $stmt = $ast->execute( $con );
	 *   $mysqlResult = $stmt->get_result();
	 *   $result = $mysqlResult->fetch_all();
	 *
	 * @param mysqli $con Mysql connection
	 * @return mysqli_stmt Executed MySQL prepared statement
	 * @throws CompiledStatementError When execution failed
	 */
	public function run_mysqli( mysqli $con ) : mysqli_stmt {

		$stmt = $this->getPreparedStatement_mysqli( $con );
		if( !$stmt->execute() ) {
			throw new CompiledStatementError( 'Execution failed' );
		}
		return $stmt;
	}
}
