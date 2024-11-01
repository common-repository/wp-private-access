<?php 
	/*
	Plugin Name: wp-private-access
	Plugin URI: http://www.kifulab.net/2009/wp-private-access/
	Description: Restricts blog access using http authentication.
	Author: Kifulab
	Version: 1.1
	Author URI: http://www.kifulab.net
	*/

	
	
	session_start("wpprivate_session");
	global $wpdb;
	
	// Get Options
	$wpprivate_realm = $wpdb->escape(get_bloginfo("name"));
	
	
	// functions
	function wpprivate_auth(){
		global $wpprivate_realm;
		header('WWW-Authenticate: Basic realm="'.$wpprivate_realm.'"');
		header('HTTP/1.0 401 Unauthorized');
		wpprivate_show_unauthorized_page();
		return;
	}
	
	function wpprivate_login(){
		
		global $wpdb;
		
		require_once  ABSPATH . '/wp-includes/pluggable.php';
		
		
	
		// Only frontend pages
		if(!is_admin() && !isset($_SESSION['wpprivate_has_already'])){
			
			if(empty($_SERVER['PHP_AUTH_USER'])){
				
				// Output dialog
				wpprivate_auth();
				exit;
			}
			elseif(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])){
				
				// authentication
				$user = $_SERVER['PHP_AUTH_USER'];
				$pw = $_SERVER['PHP_AUTH_PW'];
				$valid_user = wp_authenticate($user, $pw);
				
				if(is_wp_error($valid_user)){
					wpprivate_auth();
					exit;
				}
				else{
					$_SESSION['wpprivate_has_already'] = 'absolutely yes!';
				}
				
			}
			else{
				wpprivate_auth();
			}
		}
		return;
		
	}
	
	function wpprivate_show_unauthorized_page(){
	
		if(file_exists(dirname(__FILE__).'/401.html') && function_exists("file_get_contents")){
			echo file_get_contents(dirname(__FILE__).'/401.html');
		}
		else{
			echo '<h1>Authorization Required</h1>';
		}
		return;
	}
	wpprivate_login();
?>
