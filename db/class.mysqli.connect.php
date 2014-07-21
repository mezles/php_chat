<?php
/**
 * Class for msqli
 *
 * Author: John Jason Taladro
 * Date: 05/23/2013
 * Version: 1.0.0
 *
 * For Programmers who will edit this code, please log your name below :)
 * - ex. john Doe
 */
class MY_DB {
	/**
	 * Sets _mysqli to null by default
	 */
	var $_mysqli = null;
	
	/**
	 * Sets database hostname
	 */
	protected $_host = "localhost";
	
	/**
	 * Sets database user
	 */
	protected $_user = "root";
	
	/**
	 * Sets database password
	 */
	protected $_password = "yKbAt8mcTGxcnUC";
	
	/**
	 * Sets database name
	 */
	protected $_database = "jason_chat";
	
	/**
	 * Class constructor
	 */
	public function __construct() {
		/* Connects to mysql */
		$this->_mysqli = new mysqli( $this->_host , $this->_user , $this->_password , $this->_database );
		/* if unable to connect, outputs error */
		if ($this->_mysqli->connect_errno) {
			die( "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		}
	}
  
  public function get_results( $sql ) {
    $return = array();
    
    $results = $this->_mysqli->query( $sql );
    while ($row = $results->fetch_assoc()) {
      $return[] = $row;
    }
    return $return;
  }
  
  public function get_row( $sql ) {
    $row = $this->_mysqli->query( $sql );
    return $row->fetch_assoc();  
  }
  
  public function num_rows( $sql ) {
    $results = $this->_mysqli->query( $sql );
    return $results->num_rows;
  }
  
  public function array_map_callback( $param ) {
    return $this->_mysqli->real_escape_string($param);
  }
  
  public function update( $table, $data = array(), $where = array(), $condition = "AND" ) {
    $data = array_map(array($this, 'array_map_callback'), $data);
    $where = array_map(array($this, 'array_map_callback'), $where);
    
    $set = array();
		foreach ( $data as $key => $value ) {
			$set[] = "`$key`=\"$value\"";
		}    
    
    $_where = array();
    foreach ( $where as $key => $value ) {
			$_where[] = "`$key`=\"$value\"";
		}
    
		$sql = "UPDATE `$table` SET " . implode(", ", $set) . " WHERE " . implode(" " . $condition . " ", $_where);
		
		$result = $this->_mysqli->query( $sql );
		
    return ($this->_mysqli->affected_rows > 0) ? true : false;
		
	}
  
}

$db = new MY_DB();
$db = $db->_mysqli;