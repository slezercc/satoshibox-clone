 <?php    
	include("../requires/sell.class.php");
	error_reporting(1);
	$sell = new sell();

    $cp_merchant_id = $sell->cp_merchant_id;
    $cp_ipn_secret = $sell->cp_ipn_secret;
    $cp_debug_email = $sell->cp_debug_email;

    function errorAndDie($error_msg) {
        global $cp_debug_email;
        if (!empty($cp_debug_email)) {
            $report = 'Error: '.$error_msg."\n\n";
            $report .= "POST Data\n\n";
            foreach ($_POST as $k => $v) {
                $report .= "|$k| = |$v|\n";
            }
            mail($cp_debug_email, 'CoinPayments IPN Error', $report);
        }
        die('IPN Error: '.$error_msg);
    }
    
    if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
        errorAndDie('IPN Mode is not HMAC');
    }
    
    if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
        errorAndDie('No HMAC signature sent.');
    }
    
    $request = file_get_contents('php://input');
    if ($request === FALSE || empty($request)) {
        errorAndDie('Error reading POST data');
    }
    
    if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($cp_merchant_id)) {
        errorAndDie('No or incorrect Merchant ID passed');
    }
        
    $hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));
    if ($hmac != $_SERVER['HTTP_HMAC']) {
        errorAndDie('HMAC signature does not match');
    }
    
    // HMAC Signature verified at this point, load some variables.

    $txn_id = cleaning($_POST['txn_id']);
    $amount1 = floatval($_POST['amount1']);
    $amount2 = floatval($_POST['amount2']);
    $currency1 = cleaning($_POST['currency1']);
    $currency2 = cleaning($_POST['currency2']);
    $status = intval($_POST['status']);
    $status_text = cleaning($_POST['status_text']);
	$net_amount = $balance = floatval($_POST['net']);

	
		
		
	$mysqli = $sell->connect();
	
	// Get Data
	$query = "SELECT
					*
				FROM
					payment
				WHERE
					transid = '$txn_id' AND status = '0'
					";
	$res = mysqli_query($mysqli, $query) or die('-2' . mysqli_error());
	$rows = mysqli_fetch_assoc($res);

    $order_total = $rows["btc_price"];
	$productId = $rows["productId"];


    //depending on the API of your system, you may want to check and see if the transaction ID $txn_id has already been handled before at this point

    // Check the original currency to make sure the buyer didn't change it.
    if (!in_array($currency1, $sell->accepted_currency)) {
        errorAndDie('Original currency mismatch!');
    }    
    
    // Check amount against order total
    if ($amount1 > 0 && $amount1 < $order_total) {
		$mysqli->query("UPDATE payment SET status = '3', received = '$balance', updated=now() WHERE transID = '$txn_id'");
        errorAndDie('Amount is less than order total!');
    }
  
    if ($status >= 100 || $status == 2) {

	
			$mysqli->query("UPDATE payment SET status='1',received='$balance', updated=now() WHERE transID='$txn_id'");
			
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
				$productId
			);
			$product->execute();
			$product->close();	

			
    } else if ($status < 0) {
        //payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent
		$mysqli->query("UPDATE payment SET status = '3', received = '$balance', updated=now() WHERE transID = '$txn_id'");
    } else {
        //payment is pending, you can optionally add a note to the order page
    }
    die('IPN OK');
