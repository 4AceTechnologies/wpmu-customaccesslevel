<?php

/**
* Plugin Name: WPMU Membership Custom Access Level
* Plugin URI: https://github.com/4AceTechnologies/wpmu-customaccesslevel
* Description: This WP plugin is a basic plugin that can be customized to insert custom Access Levels to work with WPMU Membership plugin.
* Version: 1.0.0
* Author: 4 Ace Technologies
* Author URI: http://www.4acetech.com
* License:  GPL2
**/
	
define("PLUGIN_CONFIG_MNG_URL", WP_PLUGIN_DIR . "/wpmu-membership/");
	
register_activation_hook( __FILE__, 'wpmuMembershipForm' );
register_deactivation_hook( __FILE__, 'wpmuMembershipPluginDeactivate' );

/*	==========	Inlcude Constants START	==========	*/
function include_constants() {
  global $wpdb;
  define("m_levelmeta_table_name", $wpdb->prefix . "m_levelmeta");
  define("m_membership_levels_table_name", $wpdb->prefix . "m_membership_levels");
  define("posts_table_name", $wpdb->prefix . "posts");
}

/*	==========	Inlcude Constants END	==========	*/
	

/*	==========	Inlcude CSS and JS files START	==========	*/
function includes_head() {
  echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('stylesheet/style.css', __FILE__) . '">';
  echo '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>';
  echo '<script type="text/javascript" src="' . plugins_url('js/js.js', __FILE__) . '"></script>';
}

/*	==========	Inlcude CSS and JS files END	==========	*/


/*	==========	Create ShortCode for WPMU Membership START	==========	*/
function wpmuMembershipForm() {
  global $wpdb;
  include_constants();

  $m_levelmeta_table_name = $wpdb->prefix . "m_levelmeta";
  $m_membership_levels_table_name = $wpdb->prefix . "m_membership_levels";
  $posts_table_name = $wpdb->prefix . "posts";
  
  $sql = "SELECT * FROM " . m_membership_levels_table_name . " WHERE level_title = 'Test Access Level'";
  $query = $wpdb->get_results($sql);
  $number_of_records = $wpdb->num_rows;
					
  if($number_of_records == 0) {
    $wpdb->query("INSERT INTO " . m_membership_levels_table_name . " (level_title, level_slug, level_active) VALUES ('Vendor Access', 'vendor-access', '1')");
    $sql = "SELECT * FROM " . m_membership_levels_table_name . " WHERE level_title = 'Test Access Level'";
    $query = $wpdb->get_results($sql);
    
    $level_id = $query[0]->id;
    $wpdb->query("DELETE FROM " . m_levelmeta_table_name . " WHERE level_id =  '".$level_id."'");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'joining_ping', '')");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'leaving_ping', '')");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'associated_wp_role', '')");
  }
  else if($number_of_records > 1) {
    $wpdb->query("DELETE FROM " . m_membership_levels_table_name . " WHERE level_title =  'Vendor Access'");
    $wpdb->query("INSERT INTO " . m_membership_levels_table_name . " (level_title, level_slug, level_active) VALUES ('Vendor Access', 'vendor-access', '1')");
    $sql = "SELECT * FROM " . m_membership_levels_table_name . " WHERE level_title = 'Test Access Level'";
    $query = $wpdb->get_results($sql);
    $level_id = $query[0]->id;
    $wpdb->query("DELETE FROM " . m_levelmeta_table_name . " WHERE level_id =  '".$level_id."'");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'joining_ping', '')");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'leaving_ping', '')");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'associated_wp_role', '')");
  }
  else if($number_of_records == 1) {
    $level_id = $query[0]->id;
    $wpdb->query("DELETE FROM " . m_levelmeta_table_name . " WHERE level_id =  '".$level_id."'");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'joining_ping', '')");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'leaving_ping', '')");
    $wpdb->query("INSERT INTO " . m_levelmeta_table_name . " (level_id, meta_key, meta_value) VALUES ('".$level_id."', 'associated_wp_role', '')");
  }

  $wpdb->query("DELETE FROM " . posts_table_name . " WHERE post_type =  'page' && post_title = 'User Registration Form'");
  $page['post_type'] = 'page';
  $page['post_content'] = '[urf]';
  $page['post_parent'] = 0;
  $page['post_author'] = 1;
  $page['post_status'] = 'publish';
  $page['post_title'] = 'User Registration Form';
  $pageid = wp_insert_post ($page);
  
  if ($pageid == 0) {
    /* Add Page Failed */
  }

}

  function wpmuMembershipPluginDeactivate() {
    global $wpdb;
    include_constants();
    $wpdb->query("DELETE FROM " . posts_table_name . " WHERE post_type =  'page' && post_title = 'User Registration Form'");

  }


/*	==========	Create ShortCode for WPMU Membership END	==========	*/



/*	==========	Create ShortCode for WPMU User Registration Form START	==========	*/
function wpmuMembershipUserRegistrationForm($atts) { 
    include_constants();
    includes_head();

    global $db_rdp;
    $curr_month = strtolower(date("F"));
    $counter_tds = 0;

    if(isset($_GET["status"])) {
      echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>";
      echo "<tr>";
      echo "<td align='center' valign='top' style='text-align:center!important;'>";
      
      if($_GET["status"] == "true") {
      	echo "<span style='font-family:Arial; font-size:11px; font-weight:bold; color:green;'>Your account has been successfully created. Kindly, check your email.</span>";
      }
      else if($_GET["status"] == "fnsp") {
      	echo "<span style='font-family:Arial; font-size:11px; font-weight:bold; color:red;'>Form not submitted properly. Please, try again.</span>";
      }
      else if($_GET["status"] == "farf") {
      	echo "<span style='font-family:Arial; font-size:11px; font-weight:bold; color:red;'>Please fill alll the required fields.</span>";
      }
      else if($_GET["status"] == "eaae") {
      	echo "<span style='font-family:Arial; font-size:11px; font-weight:bold; color:red;'>Email address already exists..</span>";
      }
      
      echo "</td>";
      echo "</tr>";
      echo "</table>";
    }
    
    echo "<form name='form_wpmu_user_registration' id='form_wpmu_user_registration' method='post' action=''>";
    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>";
    echo "<tr>";
    echo "<td><b>First Name:</b></td>";
    echo "<td><input type='text' name='txt_wpmu_user_registration_first_name' id='txt_wpmu_user_registration_first_name' style='width:250px; height:12px;' /></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Last Name:</b></td>";
    echo "<td><input type='text' name='txt_wpmu_user_registration_last_name' id='txt_wpmu_user_registration_last_name' style='width:250px; height:12px;' /></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Phone Number:</b></td>";
    echo "<td><input type='text' name='txt_wpmu_user_registration_phone_number' id='txt_wpmu_user_registration_phone_number' style='width:250px; height:12px;' /></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Address:</b></td>";
    echo "<td><input type='text' name='txt_wpmu_user_registration_address' id='txt_wpmu_user_registration_address' style='width:250px; height:12px;' /></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><b>Email Address:</b></td>";
    echo "<td><input type='text' name='txt_wpmu_user_registration_email_address' id='txt_wpmu_user_registration_email_address' style='width:250px; height:12px;' /></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>&nbsp;</td>";
    echo "<td align='center' style='text-align:center!important;'><input type='submit' name='submit_wpmu_user_registration' value='Register' style='width:100px;' /></td>";
    echo "</tr>";
    echo "</table>";
    echo "</form>";
    
}

/*	==========	Create ShortCode for WPMU User Registration Form END	==========	*/

if(isset($_POST["submit_wpmu_user_registration"])) {
  global $wpdb;
  $txt_wpmu_user_registration_first_name = $_POST["txt_wpmu_user_registration_first_name"];
  $txt_wpmu_user_registration_last_name = $_POST["txt_wpmu_user_registration_last_name"];
  $txt_wpmu_user_registration_phone_number = $_POST["txt_wpmu_user_registration_phone_number"];
  $txt_wpmu_user_registration_address = $_POST["txt_wpmu_user_registration_address"];
  $txt_wpmu_user_registration_email_address = $_POST["txt_wpmu_user_registration_email_address"];
  $txt_wpmu_user_registration_password = substr(str_shuffle(MD5(microtime())), 0, 8);
  
  if($txt_wpmu_user_registration_first_name == "" || $txt_wpmu_user_registration_last_name == "" || $txt_wpmu_user_registration_phone_number == "" || $txt_wpmu_user_registration_email_address == "") {
    if(strpos($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], "status=") == false) {
      echo "<script type='text/javascript'>";
      echo  "window.location = 'http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ."&status=farf'";
      echo "</script>";
    }
    else {
      echo "<script type='text/javascript'>";
      echo  "window.location = 'http://".str_replace(substr($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], strpos($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], "status=")-1), "", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ."&status=farf'";
      echo "</script>";
    }
  }
  else {
    if (null == email_exists($txt_wpmu_user_registration_email_address)) {
      if (null == username_exists($txt_wpmu_user_registration_email_address)) {
      	//Create worpdress user
	$user_id = wp_create_user($txt_wpmu_user_registration_email_address, $txt_wpmu_user_registration_password, $txt_wpmu_user_registration_email_address);
	
	
	// Set the nickname
	wp_update_user(
	array(
	'ID' => $user_id,
	'nickname' => $txt_wpmu_user_registration_email_address
	)
	);
	
	// Set the role
	/*$user = new WP_User($user_id);*/
	/*$user->set_role('charity');	*/
	
	add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
	
	$content  = "Dear User,<br /><br />";
	$content .= "Your account has been successfully created.<br />";
	$content .= "Below are the login details.<br />";
	$content .= "<b>Username:</b> ".$txt_wpmu_user_registration_email_address."<br />";
	$content .= "<b>Passowrd:</b> ".$txt_wpmu_user_registration_password."<br /><br />";
	$content .= "Thank you.";
	
	$headers = "MIME-Version: 1.0" . "\r\n";
	/*$headers .= "Content-type: text/html; charset=".get_bloginfo('charset')."" . "\r\n";*/
	$headers .= 'From: WPMU Membership <wpmu@membership.com>' . "\r\n";
	wp_mail($txt_wpmu_user_registration_email_address, 'WP Registration Details', $content, $headers);
	
	$sql = "SELECT * FROM " . m_membership_levels_table_name . " WHERE level_title = 'Test Access Level'";
	$query = $wpdb->get_results($sql);
	$level_id = $query[0]->id;
	$wpdb->query("INSERT INTO wp_m_membership_relationships (user_id, sub_id, level_id, startdate, updateddate, order_instance, usinggateway) VALUES ('".$user_id."', '0', '".$level_id."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', '0' , 'admin')");
	
	echo "<script type='text/javascript'>";
	echo  "window.location = 'http://".str_replace(substr($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], strpos($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], "status=")-1), "", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ."&status=true'";
	echo "</script>";
      }
    } else {
    	echo "<script type='text/javascript'>";
	echo  "window.location = 'http://".str_replace(substr($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], strpos($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], "status=")-1), "", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ."&status=eaae'";
	echo "</script>";
    }
  }
}
	
	
	
	
/*	==========	Declaring ShortCodes START	==========	*/
add_shortcode( 'ncdsf', 'wpmuMembershipForm' );
add_shortcode( 'urf', 'wpmuMembershipUserRegistrationForm' );
/*	==========	Declaring ShortCodes END	==========	*/

/*add_action("init", "nameComDomainSearchForm");*/

?>
