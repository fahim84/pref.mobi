<?php $this->load->view('header',$this->data); ?>
<section class="header-survey" id="PageMainHeader">
	<?php $this->load->view('header_section',$this->data); ?>
</section>
<style>
.res-nav{display:none;}
</style>
<section class="inner-page">
	<div class="container">
    	<div align="center" ><br /><br /><br /><br /><br /><br /><div><h2 style="color:#848484;">Thank you for your feedback and we wish you a pleasant day ahead.</h2></div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div>
    	<div class="clear"></div>
    </div>
</section>

<footer>
<div class="container">
    <div align="center">
    &copy; Copyright <?php echo date('Y'); ?>. All rights reserved. &nbsp;
    </div>
    <div class="clear"></div>
</div>
</footer>
<script>
$(document).ready(function(){
	var DocHeight = $(window).height();
	var HeaderHeight = $('#PageMainHeader').height();
	var FooterHeight = $('footer').height();
	
	var TotalHeight = parseInt(HeaderHeight) + parseInt(FooterHeight);
	var DiffHeight = parseInt(DocHeight) - parseInt(TotalHeight) - parseInt(90);
	//DiffHeight = parseInt(DiffHeight) / parseInt(2);
	
	$('.inner-page').css('min-height', DiffHeight+'px');
});
</script>
<?php
if(isset($_SESSION['GoBackToFeedback']))
{
	$url = base_url()."order/start_feedback";
	echo '<meta http-equiv=refresh content=3;URL='.$url.'>';
	exit;
}
else
{
	$url = base_url()."welcome/afterthankyou";
	echo '<meta http-equiv=refresh content=3;URL='.$url.'>';
	exit;
}
//$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);
