<?php
    /*
        * Cancel Order page
    */

    if (session_id() == "")
        session_start();

    include('header.php');
	
	$arr = $_SESSION["error"];
	
?>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
			<div class="alert alert-danger" role="alert">                    
				<p class="text-center"><strong>The payment could not be completed.</strong></p>
			</div>

			<br />
			<strong>Reason: </strong> <?php echo $arr["json"]["name"]; ?> <br />
			<br />
			<strong>Message: </strong> <?php echo $arr["json"]["message"]; ?> <br />
			
			<br />
			<br />

            Return to <a href="index.php">home page</a>.
        </div>
        <div class="col-md-4"></div>
    </div>
<?php
    include('footer.php');
?>