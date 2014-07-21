<?php
require_once("class.mysqli.connect.php");

class Online extends MY_DB {
  public function __construct() {
    parent:: __construct();
    
  }
  
  public function get_users() {
    $sql = sprintf( "SELECT `online_data` FROM `online`" );
    $row = $this->get_row($sql);
    return ($row) ? $row['online_data'] : "";
  }
  
  public function get_chat_content() {
    $sql = sprintf( "SELECT `chat_content` FROM `db`" );
    $row = $this->get_row($sql);
    return ($row) ? $row['chat_content'] : "";
  }
  
  public function get_chat_last_modif() {
    $sql = sprintf( "SELECT UNIX_TIMESTAMP(`timestamp`) as `timestamp` FROM `db`" );
    $row = $this->get_row($sql);
    return ($row) ? $row['timestamp'] : "";
  }
  
  public function get_user_last_modif() {
    $sql = sprintf( "SELECT UNIX_TIMESTAMP(`date`) as `date` FROM `online`" );
    $row = $this->get_row($sql);
    return ($row) ? $row['date'] : "";
  }
  
  
}