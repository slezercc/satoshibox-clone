<? 
ob_start();
include("modules/header.php")?>
<? include("modules/product.php")?>
<div class="container" id="main-container">

        <div class="row" id="content">
    <div class="col-lg-6 col-sm-9 col-lg-offset-3 col-sm-offset-1">
        <br />
		<?
		if(isset($description) && trim($description) !== "" ) {
		?>
        <div class="list-group">
            <a href="#" class="list-group-item disabled">
				<h4 class="list-group-item-heading"><small>Description</small></h4>
				<p>
					<?=nl2br($description)?>
				</p>
            </a>
	    </div>		
		<? } ?>
        <div class="list-group">
            <a href="#" class="list-group-item disabled">
                <span class="pull-right download-link"><svg xmlns:svg="http://www.w3.org/2000/svg" version="1.1" width="40" height="37.471004"><path d="m 20,19.984534 c 3.333334,-3.330755 6.666667,-6.66151 9.999999,-9.9922668 -2.499999,0 -4.999999,0 -7.499999,0 0,-3.3307558 0,-6.6615111 0,-9.9922672 -1.666667,0 -3.333334,0 -4.999999,0 0,3.3307561 0,6.6615114 0,9.9922672 -2.500001,0 -5,0 -7.500001,0 3.333333,3.3307568 6.666666,6.6615118 10,9.9922668 z m 9.090859,-4.087773 c -0.934114,0.933418 -1.868228,1.866836 -2.802343,2.800255 3.386458,1.261861 6.772916,2.523724 10.159375,3.785586 C 30.96526,24.525526 25.48263,26.56845 20,28.611375 14.51737,26.56845 9.0347398,24.525526 3.5521094,22.482602 6.9385678,21.22074 10.325026,19.958877 13.711484,18.697016 12.777343,17.763597 11.843203,16.830179 10.909062,15.896761 7.2727084,17.259352 3.6363541,18.621943 0,19.984534 c 0,3.330757 0,6.661512 0,9.992268 6.6666666,2.498067 13.333333,4.996134 20,7.494201 6.666667,-2.498067 13.333333,-4.996134 20,-7.494201 0,-3.330756 0,-6.661511 0,-9.992268 -3.636381,-1.362591 -7.272761,-2.725182 -10.909141,-4.087773 z" style="fill:#c0c0c0" /></svg></span>
				<h4 class="list-group-item-heading"><small>Infos about this product</small></h4>
					<?=$infos?>
				<p>
				<div class="alert alert-info">
					#~ Send exact amount (Not refundable) <br/>
					#~ Once you paid, you have to download your product directly to don't lose it <br/>
					#~ Once your browser close, you can't download it again !
				</div>			
				</p>
            </a>
	    </div>

<? include("modules/buy.php")?>
	
        
        <br />


    </div>
</div>
    <div id="footer" class="row">
        <div class="col-lg-12">
    	    	    <div style="color: gray">We are not responsible for file content. Bitcoins goes directly<br /> to uploader, we can't provide any refunds.<br/><br/>
						The person that sent to you this link must be trusted</div>
<br />
<center><a href="#" class="showdel">Delete this product ?</a></center>
			<div class="hiddenop"><!---->
			<form action="" method="post">
            <div class="form-group">
                <label for="passdelete" class="lb-sm">Enter your password to delete it</label>
				<center>
					<input type="text" class="form-control" name="passdelete" id="passdelete" placeholder="Password" style="max-width:600px;width:auto">
				</center>
				<input type="hidden" name="delete" value="yes">
            </div>
			<input type="submit" value="Delete it" class="btn btn-primary btn-large" >
			</form>
			</div>
<br />
<script>
$(function() {
	
$(".showdel").click(function(){
    $(".hiddenop").slideToggle("slow");
});
		 
});
</script>
<? include("modules/footer.php")?>						
	</div></div>

</div>

</body>
</html>
<?php
ob_end_flush();
?>