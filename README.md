# SatoshiBox Clone
PHP Website script that allows users to sell files for Bitcoin. Admin panel included.

# Setup

You will need a MySQL database, an operating Bitcoin Node and a CoinPayments Account.

1. Navigate to requires/sell.class.php and edit the following lines:

```json
{
private $fee_service = 3; // Pourcent you want to take for each transaction
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
}
```
2. Head over to PHPMyAdmin and upload database.sql to setup the database.
3. Create a coinpayments.net account, generate a new API and merchant ID and set your IPN password. Then, fill in the info for the coinpayment config lines.
4. Go on https://example.com/admin.php and customize everything to your liking.
