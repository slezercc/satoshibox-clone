<?php
if(count(get_included_files()) ==1) exit("");
if(isset($_GET['product']) && $sell->productExist($decrypted_url) == 0) {
	header('location: '.$sell->getsite("url").'/error');
}
ob_end_flush();	
$infos = $sell->showProduct($decrypted_url);
$price = $sell->showProduct($decrypted_url, "btc_price");
$description = $sell->showProduct($decrypted_url, "description");
$description = str_replace(array('\r\n', '\r', '\n'), "<br />", $description);
$currency = $sell->showProduct($decrypted_url, "currency");

if($currency == "USD") {
	$price =  number_format($price, 2, '.', '');
} 

?>