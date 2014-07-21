<?php

date_default_timezone_set('Asia/Manila');

if ( isset($_REQUEST['action']) && !empty($_REQUEST['action']) ) {
	if ( function_exists( $_REQUEST['action'] ) ) {
		$func = $_REQUEST['action'];
		$func();
	}
}

function chat_content() {
	
  require_once("db/class.online.php");
  $online = new Online();
  
	// $filename = dirname( __FILE__ ) . '/db/db.txt';
	$filename = $online->get_chat_content();

	if ( $_GET['msg'] && !empty( $_GET['msg'] ) ) {
		// $message = file_get_contents( $filename );
		$message = $filename;
		$message .= "<p><span class='text-info' data-user='" . $_GET['name'] . "'>" . $_GET['name'] . ": </span>" 
				. strip_tags( $_GET['msg'], '<a>') . "<span class='pull-right text-muted normal'>" . date("h:i A, M. d Y") . "</span></p>" . PHP_EOL;
		// file_put_contents( $filename, $message );
        $test = $online->update('db', array('chat_content' => $message, 'timestamp' => date('Y-m-d H:i:s',time())), array('id' => 1));
	}
	
	$lastmodif = isset( $_GET['timestamp'] ) ? $_GET['timestamp'] : 0;
	// $currentmodif = filemtime( $filename );
	$currentmodif = $online->get_chat_last_modif();

	
	while( $currentmodif <= $lastmodif ):
		usleep( 10000);
		// clearstatcache();
		// $currentmodif = filemtime( $filename );
		$currentmodif = $online->get_chat_last_modif();
	endwhile;
	
	// $db_contents = explode( PHP_EOL, file_get_contents( $filename ) );
	$db_contents = explode( PHP_EOL, $online->get_chat_content() );
	$db_content = "";
	if ( is_array( $db_contents ) ) :
	foreach( $db_contents as $content ):
		$db_content .= stripslashes( $content );
	endforeach;
	else :
		$db_content = stripslashes( $content );
	endif;
	
	$response = array();
	$response['msg'] = $db_content;
	$response['timestamp'] = $currentmodif;
	echo json_encode( $response );
	die();
}

function login() {
  require_once("db/class.online.php");
	if ( !isset($_SESSION) ) {
		session_start();
	}
  $online = new Online();
	$response = array();
	$response['error'] = true;
	$response['msg'] = 'ok';
	
	if ( $_POST['nickname'] && isset($_POST['nickname']) ) {
		
		// $filename = dirname( __FILE__ ) . '/db/online.txt';
		
		// $users = json_decode( file_get_contents( $filename ) );
    
        $users = json_decode($online->get_users());
    	if ( !is_null($users) ) {
    		if ( !user_exist( $_POST['nickname'], $users) ) {
    			array_push( $users, array( 'name' => $_POST['nickname'], 'status' => 'online' ) );
    			$response['error'] = false;
    			
    		} else {
    			$response['msg'] = "Username is already used!";
    		}
    			
    	} else {
    		$users = array();
    		array_push( $users, array( 'name' => $_POST['nickname'] , 'status' => 'online' ) );
    		$response['error'] = false;
    	}
    		
    	if ( $response['error'] === false ) {
    		$user = json_encode(  $users );
            $updated = $online->update('online', array('online_data' => $user, 'date' => date('Y-m-d H:i:s',time())), array('id' => 1));
    		// file_put_contents( $filename, $user );
    
    	    if ( $updated ) {
                $_SESSION['user'] = $_POST['nickname'];
            }
    	}		
	
	}
	
	echo json_encode($response);
	die();
}

function whosonline() {
    require_once("db/class.online.php");
    $online = new Online();
    $response = array();
	
	if ( $_GET['action'] && !empty( $_GET['action'] ) ) {
	
		// $filename = dirname( __FILE__ ) . '/db/online.txt';
    
		$lastmodif = isset( $_GET['timestamp'] ) ? $_GET['timestamp'] : 0;
		// $currentmodif = filemtime( $filename );
        $currentmodif = $online->get_user_last_modif();
		
		while( $currentmodif <= $lastmodif ):
			usleep( 10000);
			// clearstatcache();
			// $currentmodif = filemtime( $filename );
			$currentmodif = $online->get_user_last_modif();
		endwhile;
	
		// $users = json_decode( file_get_contents( $filename ) );
        $users = json_decode($online->get_users());

		if ( !is_null($users) ) {
			$response['online'] = $users;
		} else {
			$response['online'] = array();
		}	
	}
	
	$response['timestamp'] = $currentmodif;
	echo json_encode( $response );
	die();
	
}

function logout() {
    require_once("db/class.online.php");
	if ( !isset($_SESSION) ) {
		session_start();
	}
	
    $online = new Online();
	$response = array();	
	$new_online = array();
	
	// $filename = dirname( __FILE__ ) . '/db/online.txt';
		
	// $users = json_decode( file_get_contents( $filename ) );
    $users = json_decode($online->get_users());
  
	if ( !is_null($users) && is_array($users) ) {
		foreach( $users as $key => $value ) {
			if ( strtolower( $value->name ) != strtolower( $_SESSION['user'] ) ) {
				$new_online[] = array( 'name' => $value->name, 'status' => $value->status );
			}
		}
	}
	
	$user = json_encode(  $new_online );
	// file_put_contents( $filename, $user );
    $updated = $online->update('online', array('online_data' => $user, 'date' => date('Y-m-d H:i:s',time())), array('id' => 1));
		
	unset($_SESSION['user']);
	session_destroy();
	
	$response['msg'] = "ok";
	echo json_encode( $response );
	die();
	
}

function user_exist( $user, $users ) {
	$found = false;
	if ( is_array( $users ) ) {
		foreach( $users as $key => $value ) {
			if ( strtolower( $value->name ) === strtolower( $user ) ) {
				$found = true;
				break;
			}
		}
	}
	return $found;
}

function change_status() {
  require_once("db/class.online.php");
	if ( !isset($_SESSION) ) {
		session_start();
	}
	
  $online = new Online();
	$response = array();	
	$new_status = array();
	
	$response['error'] = true;
	$response['msg'] = 'error';
	
	if ( isset($_SESSION['user']) && isset($_POST['status']) ) {
		// $filename = dirname( __FILE__ ) . '/db/online.txt';
			
		// $users = json_decode( file_get_contents( $filename ) );
        $users = json_decode($online->get_users());
        if ( !is_null($users) && is_array($users) ) {
    	    foreach( $users as $key => $value ) {
    		    if ( strtolower( $value->name ) === strtolower( $_SESSION['user'] ) ) {
    				$value->status = $_POST['status'];
    			}
    			$new_status[] = array( 'name' => $value->name, 'status' => $value->status );
    		}
    	}
        $user = json_encode(  $new_status );
    		// file_put_contents( $filename, $user );
        $updated = $online->update('online', array('online_data' => $user, 'date' => date('Y-m-d H:i:s',time())), array('id' => 1));
    		
        $_SESSION['status'] = $_POST['status'];
        $response['error'] = false;
        $response['msg'] = "ok";
		
	} else {
		$response['msg'] = 'Invalid request';
	}
	
	echo json_encode( $response );
	die();
}