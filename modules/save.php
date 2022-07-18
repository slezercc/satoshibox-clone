<?php
include_once '../requires/sell.class.php';
$sell = new sell();
if (isset($_POST['do']) && $_POST['do'] == 'addproduct') {
	
	$detail = $sell->AddFiles("product");

	if($detail == '0') {
		header("location: ".$sell->getsite("url")."/error");
		die();
	}
	
	$bitcoin_address = $sell->sqli($_POST["address"]);
	$description = "";
	if(isset($_POST["description"])) {
		$description = $sell->sqli(htmlspecialchars($_POST["description"]));
	}
	$btc_price = $sell->sqli($_POST["price"]);
	
	// Check if BTC or USD
	$currency = '';
	if(isset($_POST['currency']))
    {
        $currency="BTC";
    }else{
        $currency="USD";
    }
	
	// Check if want sell product x time
	$sellit = '';
	if(isset($_POST['sellit']))
    {
        $sellit=$sell->sqli($_POST['sellnum']);
		if(!$sell->is_digits($sellit) || $sellit < 1) {
			header("location: ".$sell->getsite("url")."/error");
		} 

    }else{
        $sellit=0;
    }	

	
	// Check if product available x time
	$timeit = '';
	$timel = '';
	$listtime = array("hour", "day", "week", "month");
	if(isset($_POST['timeit']))
    {
        $timeit=$sell->sqli($_POST['timenum']);
		$timel=$sell->sqli($_POST['timelist']);
		
		if(!$sell->is_digits($timeit) || $timeit < 1 || !in_array($timel, $listtime)) {
			header("location: ".$sell->getsite("url")."/error");
		} 

    }else{
        $timeit=0;
		$timel=0;
    }	
	
	$passdelete = '';
	if(isset($_POST['passdelete']) && strlen(trim($_POST['passdelete'])) > 0)
    {	
		$passdelete = $sell->sqli($_POST['passdelete']);
	}
	
	if(is_numeric($btc_price)) {
		$newID = $sell->AddProduct($detail,$description,$bitcoin_address,$btc_price,$currency,$sellit,$timeit,$timel,$passdelete);
		$url          = $sell->encode_url($newID);
		header("location: ".$sell->getsite("url")."/".$url);
	} else {
		header("location: ".$sell->getsite("url"));
	}
} else {
	header("location: ".$sell->getsite("url"));
}
?>