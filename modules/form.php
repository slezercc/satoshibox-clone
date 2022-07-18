<?php if(count(get_included_files()) ==1) exit(""); ?>
<style>
.form-control {
    display:initial !important;
}
</style>
<div id="form">
    <form id="real-form" action="save" method="post" enctype="multipart/form-data">
        <fieldset>

            <div class="form-group" id="desc" style="display:none">
                <label>Description</label>
                <textarea class="form-control" name="description"></textarea>
            </div>
			
            <div class="form-group" id="description-row">
                <label for="product" class="required">Upload your product</label> <span style="float:right"><a href="#!" class="adddesc"><strong>+</strong> Add description ?</a></span>

				<div class="alert alert-default addedfile">
					<input type="file" name="product[]" />
				</div>
				<h6 style="margin-top:-10px"><small>Authorized files: <?php $exts = ''; foreach($sell->extensions_show() as $ext) { $exts .= $ext.', '; } echo rtrim(trim($exts), ','); ?></small></h6>
				<a href="#!" class="addfile"><strong>+</strong> Sell more files ?</a><br/>
            </div>

            <div class="form-group">
                <label for="price" id="price-label" class="required">Price</label>
                <input type="number" class="form-control" name="price" id="price" value="0.9999" min="0.0010" step="any">
                <input type="checkbox" name="currency" class="currency" data-on-text="BTC" data-off-text="USD" data-on-color="black" data-off-color="black" checked>
            </div>

            <div class="form-group">
                <label for="address" class="required">Your Bitcoin address for payments</label>
                <input type="text" class="form-control" pattern="[a-zA-Z0-9]{25,34}" name="address" id="address" placeholder="eg. 1eMdMJU6dGCadDGmK497WapYfZCSQCQa9" required="required">
            </div>
			
			<div class="hiddenop"><!---->
			
            <div class="form-group">
                <label for="checkit">Additional options</label><br/>
                <input type="checkbox"  name="sellit" data-size="mini"> Sell the product <input type="text" class="form-control" style="width:100px" name="sellnum" id="sellnum" value="1" > time only<br/><br/>
				<input type="checkbox"  name="timeit" data-size="mini"> Product available for <input type="text" class="form-control" style="width:100px" name="timenum" id="timenum" value="1" > <select class="form-control" name="timelist" style="width:auto"><option value="hour">Hour(s)</option><option value="day">Day(s)</option><option value="week">Week(s)</option><option value="month">Month(s)</option></select>
            </div>
			
            <div class="form-group">
                <label for="passdelete" class="lb-sm">Password to delete (<a id="generate">Generate</a>)</label>
                <input type="text" class="form-control" name="passdelete" id="passdelete" placeholder="Password to delete it">
            </div>			
			</div>
			
			<a class="showop">Show more options</a>
			<a class="showop" style="display:none">Close options</a>
			
			<br/>
            <hr />

			<input type="hidden" name="do" value="addproduct">
            <input type="submit" id="upload-button" class="btn btn-primary btn-large" value="Start selling now!">
        </fieldset>
    </form>
</div>


<script>
$(function() {
	
$(".showop").click(function(){
    $(".hiddenop").slideToggle("slow");
	$(".showop").toggle();
});

$(".adddesc").click(function(){
    $("#desc").toggle();
});

$(".addfile").click(function(){
    $(".addedfile").append("<hr/><input type=\"file\" name=\"product[]\" />");
});

	$("[name='currency']").bootstrapSwitch();
	$("[name='sellit']").bootstrapSwitch();
	$("[name='timeit']").bootstrapSwitch();
	



      // Attach `switchChange` event to all switches.
      $("[name='currency']").on('switchChange.bootstrapSwitch', function(event, state) {
        console.log(this);  // DOM element
        console.log(event); // jQuery event
        console.log(state); // true | false

		if(state == false) {
			$("#price").val("0");
		} else {
			$("#price").val("0.9999");
		}

        
      });

		 
});
</script>
	<script type="text/javascript">
		$(document).ready(function () {
		    function gen_pass() {
		        var pass = "";
		        var length = 0;
		        var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789&@^.-_()[]{}#~=*+%|/";

		        while (length < 20) {
		            length++;
		            pass += chars.charAt(Math.floor(Math.random() * chars.length));
		        }

		        return pass;
		    }


		    $("#generate").click(function(){
		        $("#passdelete").val(gen_pass());
		    });

		});
	</script>