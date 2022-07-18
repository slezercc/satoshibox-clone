<?php
if(count(get_included_files()) ==1) exit("");

if(isset($_GET["buy"]) && $_GET["buy"] == "yesbuyitplease") {
	
		$mysqli = $sell->connect();
		
		$exist = $mysqli->query("SELECT 'X' FROM payment WHERE productId='$decrypted_url' AND bitcoinaddress='$bitcoinaddress'");
		$compte = mysqli_num_rows($exist);

		if($sell->payExist($decrypted_url,$bitcoinaddress) == 0) {

		if($currency == "USD") {
			$price_new =  number_format(($price / $sell->get_rate()), 4, '.', '');
		} else {
			$price_new = $price;
		}
		
		$genadd = $sell->receive_addr($decrypted_url, $price_new);

		$transID = trim($genadd['txn_id']);
		$bitcoinaddress = trim($genadd['address']);	
		$price_new = $genadd['amount'];
		
		
		$sell->saveCookie($array_key, $bitcoinaddress, $price_new);
		
		$sell->AddPayment($decrypted_url,$transID,$bitcoinaddress,$price,$currency,$price_new,0);
			
		}
?>
<div class="row">
    <div class="col-sm-4">
	
        <a href="bitcoin:<?=$bitcoinaddress?>?amount=0.06&amp;message=<?=$sell->getsite("title")?>%20payment">
            <img class="qr" src="https://chart.googleapis.com/chart?chs=150x150&amp;cht=qr&amp;chl=bitcoin%3A<?=$bitcoinaddress?>d%3Famount%3D<?=$price?>%26message%3D<?=$sell->getsite("title")?>%2520payment&amp;choe=UTF-8">
        </a>
    </div>

    <div class="col-sm-8 pay-form">
        <div class="form-group">
            <label for="address">Bitcoin address for <strong><?=$price_new?> BTC</strong> payment</label>
            <input id="satoshibox-address" name="address" class="form-control" autofocus="autofocus" readonly="readonly" value="<?=$bitcoinaddress?>" type="text">
        </div>

        <a class="btn btn-success" href="bitcoin:<?=$bitcoinaddress?>?amount=0.06&amp;message=<?=$sell->getsite("title")?>%20payment">Direct link for payment</a>

        <p><small>This address will be working only for short delay. Do not close browser until you download the box, after this you can't do it again.</small></p>

        <br>
        <a href="<?=$sell->getsite("url")?>/<?=$product_link?>/paid" id="satoshibox-paid-button" class="btn btn-primary btn-large" >Paid! Let me view it ?</a>
    </div>
</div>
<?php
} else if(isset($_GET["paid"]) && $_GET["paid"] == "yespaidit") {

	if($status == 1) {
	
		$result = json_decode($sell->showProduct($decrypted_url, "detail"), true);
?>
        <div class="list-group">
		<h4 class="list-group-item-heading"><small>Download</small></h4>
		<?php
		foreach($result as $download) {
		?>
            <a href="<?=$sell->getsite("url")?>/<?=$product_link?>/download/<?=$download["file"][0]?>" class="list-group-item disabled">
				<?=$download["file"][0]?>
				<span class="badge" style="background:#f1f1f1;color:#999;padding-left:10px;padding-right:10px"><?=$sell->formatBytes($download["size"][0])?></span>
            </a>
		<? } ?>
	    </div>	

<?php		
	} else {
?>

	        <div class="buy-zone" id="satoshibox-buy-zone">
                            <div class="wait">
                    <span class="loading_dots"><span></span><span></span><span></span></span>
                    Please wait, your bitcoins should be there in a minute
                </div>
                    </div>
	<? }} else { ?>

     <a href="<?=$sell->getsite("url")?>/<?=$product_link?>/buy" class="btn btn-primary btn-large">Buy for <?=$price?> <?=$currency?></a>

<?php 
}
?>