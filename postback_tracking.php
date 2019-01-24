<?php

/*
Plugin Name: Postback URL Manager
Plugin URI: http://pjeremymalouf.com
Description: Plugin is counting bots visits and managing Postback tracking
Author: Paul Jeremy Malouf
Version: 1.0
Author URI: http://pjeremymalouf.com
*/

session_start();

function pbt_install()
{
    global $wpdb;
    
    $table = $wpdb->prefix."ipn_visits";
    $structure = "CREATE TABLE $table (
        id INT(9) NOT NULL AUTO_INCREMENT,
        order_id VARCHAR(80) NOT NULL,
        link VARCHAR(80) NOT NULL,
	UNIQUE KEY id (id)
    );";
    $wpdb->query($structure);
    
    $table = $wpdb->prefix."paypal_orders";
    $structure = "CREATE TABLE $table (
        id INT(9) NOT NULL AUTO_INCREMENT,
        order_id VARCHAR(80) NOT NULL,
        sid VARCHAR(80) NOT NULL,
	UNIQUE KEY id (id)
    );";
    $wpdb->query($structure);
    
    add_option('numberOfPages');
    update_option('numberOfPages', 1);
    add_option('paypal_trackingCode');
    add_option('paypal_sid');
}

add_action('activate_postback_tracking/postback_tracking.php', 'pbt_install');

function postback_tracking()
{
	global $post;
	
 
	//CHECK FOR PAYPAL_SID
	
	$targetSID = get_option('paypal_sid');
	
	if(isset($_GET[$targetSID])){
    	    	    $_SESSION['paypal_sid'] = $_GET[$targetSID];
    	    }else if(isset($_POST[$targetSID])){
    	    	    $_SESSION['paypal_sid'] = $_POST[$targetSID];
    	  }
	
$numberOfSIDs = 3;
	
$numberOfPages = get_option('numberOfPages');

$pageBoxCount = 0;

while($pageBoxCount<$numberOfPages){
	
    $targetPage = get_option('page-' . $pageBoxCount);
    $targetSaleAmountString = get_option('sale-amount-' . $pageBoxCount);
    $targetOrderIDString = get_option('order-id-' . $pageBoxCount);
    $targetSIDArray = array();
    $targetCode = get_option('trackingCode-' . $pageBoxCount);
    
    $sidCount = 0;
    
    while($sidCount<$numberOfSIDs){
    	    
    	    $sidLabel = '';
    	    
    	    if($sidCount>0){
    	    	$sidLabel = ($sidCount+1);    
    	    }
    	    
    	    $thisSIDLabel = 'sid' . $sidLabel . '-' . $pageBoxCount;
    	    $sidReplaceLabel = 'sid' . $sidLabel; 
    	    
    	    $thisSID = get_option($thisSIDLabel);
    	    
    	    
    	    if(isset($_GET[$thisSID])){
    	    	    $_SESSION[$thisSID] = $_GET[$thisSID];
    	    }else if(isset($_POST[$thisSID])){
    	    	    $_SESSION[$thisSID] = $_POST[$thisSID];
    	    }
    	    
    	    
    	    $targetSIDArray[$thisSID] = $_SESSION[$thisSID];
    	    
    	    $sidCount++;
    }

    $thisIsThePage = false;
    
    //CHECK THE PERMALINK TO SEE IF THIS PAGE SHOULD BE TRACKED
   if (strpos($_SERVER['REQUEST_URI'],'/' . $targetPage . '/') !== false) {
   	$thisIsThePage = true;	   
   }
   
   if(strpos($_SERVER['REQUEST_URI'], 'p=' . $targetPage) !== false){
   	   $thisIsThePage = true;	
   }
   
   if($post->post_name==$targetPage){
   	   $thisIsThePage = true;
   }
   
   if($thisIsThePage){
   	    
   	   
   	   $params = array();
   	   
   	   foreach($targetSIDArray as $key => $value){
   	   	   
   	   	   $params[$key] = $value;
   	   }
   	   
   	   if(isset($_SESSION["order_id"])&&$targetOrderIDString!=null&&$targetOrderIDString!=''){
   	   	   
   	   	   $params[$targetOrderIDString] = $_SESSION["order_id"];
   	   }
   	   
   	   if(isset($_SESSION["amount"])&&$targetSaleAmountString!=null&&$targetSaleAmountString!=''){
   	   	  
   	   	   $params[$targetSaleAmountString] = $_SESSION["amount"];
   	   }
   	   
   	   
   	   $myLink = $targetCode . '?' . http_build_query( $params );
   	   
   	   $results = wp_remote_get($myLink);
   	   //echo '<span style="display:none;">' . $myLink . ':';
   	   //print_r($results);
   	   //echo '</span>';
   	 
   }
   
   $pageBoxCount++;
}
}

add_action('wp_footer', 'postback_tracking');

function turnStringIntoAArray($string){
	
	$newString = get_string_between($string,'{','}');
	$array = explode(';',$newString);
	$new_array = array();
	foreach($array as $val){
		
		$nums = explode(':',$val);
		if(count($nums)>1){
		array_push($new_array,$nums[1]);
		}

    	}
	
	return $new_array;

}

function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}


function pbt_woocommerce_ipn($posted){
	
	global $wpdb;
	
	$order_id = 'NO_ORDER_ID';
	
	
	if(isset($posted['custom'])){
	
	$custom = turnStringIntoAArray($posted['custom'] );
	
	//$order_id  = $posted['custom'];
	$order_id  = $custom[1];
	
	}
	
	
	$table = $wpdb->prefix."ipn_visits";
	
        
        //GET THE LINK TO USE:
        
        $results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."paypal_orders WHERE order_id = '$order_id'");
        
        $mySID = 'NO_SID';
        
        foreach($results as $result)
        {
        	$mySID = $result->sid;
    	}
    	
    	$targetCode = get_option('paypal_trackingCode');
    	$targetSaleAmountString = get_option('sale-amount-paypal');
    	$targetOrderIDString = get_option('order-id-paypal');
    	$targetSIDString = get_option('paypal_sid');
    	$targetCurrencyString = get_option('currency-paypal');
    	
    	$params = array();
    	
    	if($targetSIDString!=null&&$targetSIDString!=''){
   	   	  
   	   	   $params[$targetSIDString] = $mySID;
   	   }
    	
    	if($targetOrderIDString!=null&&$targetOrderIDString!=''){
   	   	   
   	   	   $params[$targetOrderIDString] = $order_id;
   	   }
   	   
   	if($targetSaleAmountString!=null&&$targetSaleAmountString!=''){
   	   	  
   	   	   $params[$targetSaleAmountString] = $posted['mc_gross_1'];
   	   }
   	   
   	if($targetCurrencyString!=null&&$targetCurrencyString!=''){
   	   	  
   	   	   $params[$targetCurrencyString] = $posted['mc_currency'];
   	   }
   	   
   	   
   	$myLink = $targetCode . '?' . http_build_query( $params );
    	
    	$results = wp_remote_get($myLink);
    	
    	$wpdb->query("INSERT INTO $table(order_id, link)
        VALUES('$order_id', '$results')");
        
        
}

function pbt_woocommerce_paypal($order_id){
	
	global $wpdb;
	
	$table = $wpdb->prefix."paypal_orders";
	
    	$mySID = 'NO_SID';
    	
    	if(isset($_SESSION['paypal_sid'])){
 
   	   	   $mySID = $_SESSION['paypal_sid'];
   	   }
   	   
   	if($order_id==null||$order_id==''){
		$order_id  = 'NO_ORDER_ID';
	}
   	   
    	
	$wpdb->query("INSERT INTO $table(order_id, sid)
        VALUES('$order_id', '$mySID')");
	
}

function woocommerce_get_sale_amount(){
	
	global $woocommerce;
	
	$order_total = $woocommerce->cart->get_total();

	session_start();
	
        if(isset($order_total)){
             $order_total = preg_replace("/&.*?;/","",strip_tags($order_total));
             $_SESSION["amount"] = $order_total;
        }
        
	
}

function woocommerce_get_order_id($order_id){
	session_start();
	
        if(isset($order_id)){
             $_SESSION["order_id"] = $order_id;
        }
}


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_action( 'woocommerce_new_order', 'pbt_woocommerce_paypal' );
	add_action( 'valid-paypal-standard-ipn-request', 'pbt_woocommerce_ipn' );
	add_action( 'woocommerce_review_order_after_order_total', 'woocommerce_get_sale_amount' );
	add_action( 'woocommerce_thankyou', 'woocommerce_get_order_id' );
}



function postback_tracking_menu()
{
    global $wpdb;
    include 'postback_tracking-admin.php';
}
 
function postback_tracking_admin_actions()
{
    add_options_page("Postback URL Manager", "Postback URL Manager", 1,
"Postback-Tracking", "postback_tracking_menu");
}
 
add_action('admin_menu', 'postback_tracking_admin_actions');


?>
