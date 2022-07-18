<? include("modules/header.php")?>
<?php
session_start();

if(isset($_POST["login"])) {
	if($sell->check_login($_POST["username"], $_POST["password"]) == 1) {
		$_SESSION["logged"] = 1;
	} else {
		echo "<div class='alert alert-danger'>Error Login !</div><br/>";
	}
}

if(isset($_GET["logout"])) {
	session_destroy();
	header('Location: '.$sell->strright(basename($_SERVER['REQUEST_URI']), "?"));
	exit;
}

if(isset($_GET["delete"]) && is_numeric($_GET["delete"]) && (isset($_SESSION["logged"]) && $_SESSION["logged"] == 1)) {
	echo $sell->DelAdmin($sell->sqli($_GET["delete"]));
}
?>
<div class="container" id="main-container">
		
        <div class="row" id="header">
        <div class="col-lg-12">
            <a href="<?=$sell->strright(basename($_SERVER['REQUEST_URI']), "?")?>"><h1 class="logo"><img src="assets/img/logo.png" width="48px"> Admin Panel</h1></a>
        </div>
    </div>
<?php
if(isset($_SESSION["logged"]) && $_SESSION["logged"] == 1) {
?>
    <div class="row" id="content">
                <div class="col-lg-6 col-sm-9 col-lg-offset-3 col-sm-offset-1">
            <div id="promo">
			<div class="row">
				<div class="col-lg-6 col-sm-6">
					<div class="alert alert-info">Product Number<br/><h2><?=$sell->adminInfos("products")?></h2></div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="alert alert-success">Sale Number<br/><h2><?=$sell->adminInfos("paid")?></h2></div>
				</div>
			</div>
			<span style="float:right"><a href="admin.php">[Admin]</a> <a href="confirmPay.php">[Confirm Payments]</a> <a href="<?=$sell->strright(basename($_SERVER['REQUEST_URI']), "?");?>?logout=1">[Logout]</a></span>
			</div>
			<form method="post" action="">
					<div class="form-group">
						<label>Check Product</label>
						<div class="row">
						<div class="col-lg-10">
							<input type="text" class="form-control" name="product" placeholder="Product ID">
						</div>
						<div class="col-lg-2">
							<input type="submit" name="check" class="btn btn-primary" value="Check" />
						</div>
						</div>
						
					</div>	
			</form>
			
<?php
	if(isset($_POST["check"])) {
		$product = $sell->sqli($_POST["product"]);
		echo $sell->check_product($product);
	}
?>

	<table class="table">
		<tr>
			<td>Product</td>
			<td>Action</td>
		</tr>
<?php
$page = '';
if(isset($_GET["page"])) {
	$page = intval($_GET["page"]);
} else {
	$page = 1;
}
echo $sell->listProd($page);
?>
</table>
        </div>
            </div>
<? } else { ?>
    <div class="row" id="content">
				<center>
					<h3>LOGIN TO ADMIN PANEL</h3>
				</center>	
                <div class="col-lg-4 col-sm-9 col-lg-offset-4 col-sm-offset-1">
				<form action="" method="post">
					<div class="form-group">
						<label for="address" class="required">Username</label>
						<input type="text" class="form-control" name="username" value="" placeholder="Username" required="required">
					</div>
					
					<div class="form-group">
						<label for="address" class="required">Password</label>
						<input type="password" class="form-control" name="password" value="" placeholder="Password" required="required">
					</div>				
				
					<input type="submit" name="login" class="btn btn-primary btn-large" value="Login Now !">
				</form>
				</div>
	</div>
<? } ?>
    <div id="footer" class="row">
        <div class="col-lg-12">
<? include("modules/footer.php")?>						
	</div></div>	

</div>