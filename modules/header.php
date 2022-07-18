<?php
	ob_start();
	if(count(get_included_files()) ==1) exit("");
	include_once 'requires/sell.class.php';
	
	$sell = new sell();
	$meta = "";
	$status = 0;
	$title = $sell->getsite("title");

	if(isset($_GET["product"])) {
		$product_link = $sell->sqli($_GET["product"]);
		$decrypted_url          = $sell->decode_url($product_link);
		$array_key = base64_encode(sha1(md5(sha1(sha1($decrypted_url)))));
		$bitcoinaddress=$sell->showCookie($array_key, "btc");
		$price_new=$sell->showCookie($array_key, "price");
		$title = "SatoshiBox Clone ~ Buy This Product";
	}
	
	if(isset($_GET["paid"]) && $_GET["paid"] == "yespaidit") {
	$receive_addr = $sell->check_rec($bitcoinaddress);

	if($receive_addr == 1) {
		$status = 1;
		$title = "SatoshiBox Clone ~ Bought Product";
		if(isset($_GET['filename']))
		{
			$filename = $_GET["filename"];
			$path = $sell->secretfolder().'/'.$filename;
			if(file_exists($path)) {
				$size = filesize($path);
				$fp = fopen($path, "rb");
				$content = fread($fp, $size);
				fclose($fp);

				header("Content-length: ".$size);
				header("Content-type: application/octet-stream");
				header("Content-disposition: attachment; filename=".$fileName.";" );
				echo $content;
				exit;
			}
		}
	
	} else {
		$meta = "<meta http-equiv=\"refresh\" content=\"10\">";
		$title = "SatoshiBox Clone ~ Waiting Payment ...";
	}
	}
	
	if(isset($_POST["delete"]) && $_POST["delete"] == "yes") {
		$pass = $sell->sqli($_POST['passdelete']);
		$sell->DelProduct($decrypted_url, $pass);
	}	
	
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="<?=$sell->getsite("url")?>/favicon.png"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?=$meta?>
	<link href="<?=$sell->getsite("url")?>/assets/css/styles.css" type="text/css" rel="stylesheet" />	
	<link href="<?=$sell->getsite("url")?>/assets/css/bootstrap-switch.min.css" type="text/css" rel="stylesheet" />	
	<script src="<?=$sell->getsite("url")?>/assets/js/jquery-3.1.0.min.js"></script>
	<script src="<?=$sell->getsite("url")?>/assets/js/bootstrap-switch.min.js"></script>
    <title><?=$title?></title>
	
</head>
<body>
<div class="alert alert-info"><center>Some functions are limited in this <strong>DEMO</strong> ! If you are interested to buy it, <strong><a href="mailto:info@progweb.info" style="color:red">contact us from here</a></strong>.
<?php
if(isset($_GET["product"])) {
?>
<br><strong><small>Please do not buy any product on this DEMO, money can't be refunded in any way !</small></strong>
<? } else { ?>
<br><small><strong><a href="https://bitcointalk.org/index.php?topic=2258851.0" target="_blank">Official thread on BitcoinTalk</a></strong> ... Please do not fall victim of scam... this is the official script and need support and update only from our developers to set it up and improve it in the future.</small><br/>
<small><strong><a href="http://www.progweb.info/satoshibox/admin.php">Check admin panel from here</a></strong> (Username: admin, password: admin)</small>
<? } ?>
	</center></div>