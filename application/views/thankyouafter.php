<?php $this->load->view('header',$this->data); ?>

<div class="container circle-options" style="top:100px;">
	<ul>
        <li>
        	<h2>What did you think?</h2>
            <input type="button" name="FeedbackBtn" id="FeedbackBtn" value="Enter" class="Button" onclick="window.location.href='<?php echo base_url(); ?>order/start_feedback';" />
        </li>
    </ul>
</div>

<?php
$this->load->view('footer',$this->data);
