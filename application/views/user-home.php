<div class="container circle-options"><br><br><br><br><br>
	<ul>
    <?php if($_SESSION[USER_LOGIN]['ordering_feature']){ ?>
    	<li>
        	<h2>Take Order</h2>
            <input type="button" name="FeedbackBtn" id="FeedbackBtn" value="Enter" class="Button" onclick="window.location.href='<?php echo base_url(); ?>order/index';" />
        </li>
        <li>
        	<h2>Select Existing Order</h2>
            <input type="button" name="FeedbackBtn" id="FeedbackBtn" value="Enter" class="Button" onclick="window.location.href='<?php echo base_url(); ?>order/existing_orders';" />
        </li>
    <?php } ?>
        <li>
        	<h2>What did you think?</h2>
            <input type="button" name="FeedbackBtn" id="FeedbackBtn" value="Enter" class="Button" onclick="window.location.href='<?php echo base_url(); ?>order/start_feedback';" />
        </li>
    </ul>
    <br><br><br><br><br>
</div>

<script>
//window.location.href='<?php echo base_url(); ?>order/start_feedback';
</script>