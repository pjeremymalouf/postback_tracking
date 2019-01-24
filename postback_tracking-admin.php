<style>

.optionsDiv{
	padding:10px; 
	margin:10px;
	float:left;
	
}

.optionDivMain{
	clear:left;
	
}


</style>

<div class="wrap">
<h2>Postback URL Manager</h2>

<div class="wrap" style="width:300px; clear:both;">
<br/>
<form method='post' action='?page=<?php echo $_GET['page']; ?>'>
<?php

	
	
	if(isset($_POST['numberOfPages'])){
		update_option('numberOfPages', $_POST['numberOfPages']);
	}
	
	$optionValue = get_option('numberOfPages');

echo '<label style="display: block; width:200px;float:left;" >Number of Pages: </label><select style="width:100px;" name="numberOfPages">';

$numOptionCounter = 1;

while ($numOptionCounter<11){
	echo '<option value="' . $numOptionCounter . '"  ';
	
	if($optionValue==$numOptionCounter){
		echo 'selected';
	}
	
	echo ' >' . $numOptionCounter . '</option>';
	$numOptionCounter++;
}

echo '</select><br/><br/><br/>';


?>
<br/>
<input type="submit"/>

</form>
<br/>
</div>

<?php

$numberOfPages = get_option('numberOfPages');

$pageBoxCount = 0;

while($pageBoxCount<$numberOfPages){
?>
<div class="widgets-holder-wrap optionsDiv" style="width:300px;">
<h3>Page Tracking - <?php echo $pageBoxCount; ?></h3>
<form method='post' action='?page=<?php echo $_GET['page']; ?>'>
<?php

$optionList = array(('trackingCode-' . $pageBoxCount) => 'Postback URL', ('page-' . $pageBoxCount)=> 'Page name or ID (from permalink)', ('sid-' . $pageBoxCount) => 'Sub ID 1', ('sid2-' . $pageBoxCount) => 'Sub ID 2', ('sid3-' . $pageBoxCount) => 'Sub ID 3', ('sale-amount-' . $pageBoxCount) => 'Sale Amount', ('order-id-' . $pageBoxCount) => 'Order ID');

foreach($optionList as $option => $title){
	
	
		if(isset($_POST[$option])){
			update_option($option, $_POST[$option]);
		}
	
		$optionValue = get_option($option);

echo '<label style="display: block; width:200px;float:left;" >' . $title . ': </label><input style="float:left;width:300px;" type="text" name="' . $option . '" value="' . $optionValue . '"/><br/><br/><br/>';

}

?>
<br/>
<input type="submit"/>

</form>
<br/>
</div>

<?php

$pageBoxCount++;
}

?>

<div class="widgets-holder-wrap optionsDiv" style="width:300px;">
<h3>WooCommerce PayPal</h3>
<form method='post' action='?page=<?php echo $_GET['page']; ?>'>

<?php

$optionList = array('paypal_trackingCode' => 'Postback URL', 'paypal_sid'=> 'Sub ID', ('sale-amount-paypal') => 'Sale Amount', ('order-id-paypal') => 'Order ID', ('currency-paypal') => 'Currency');

foreach($optionList as $option => $title){
	
	if(isset($_POST[$option])){
		update_option($option, $_POST[$option]);
	}
	
$optionValue = get_option($option);

echo '<label style="display: block; width:200px;float:left;" >' . $title . ': </label><input style="float:left;width:300px;" type="text" name="' . $option . '" value="' . $optionValue . '"/><br/><br/><br/>';

}

?>
<br/>
<input type="submit"/>

</form>
<br/>
</div>

<br/><br/>
<div class="widgets-holder-wrap optionsDiv optionDivMain" style="display:none;">
<b>PayPal IPN responses:</b>
<br/>
<?php

$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ipn_visits");

foreach($results as $result)
{
    echo "<br/>SID: " . $result->order_id . " Server response: " . $result->link;
}

?>
</div>
<div class="widgets-holder-wrap optionsDiv" style="display:none;">
<b>PayPal transactions initiated:</b>
<br/>
<?php

$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."paypal_orders");

foreach($results as $result)
{
    echo "<br/>Order ID: " . $result->order_id . " SID: " . $result->sid;
}

?>
</div>
