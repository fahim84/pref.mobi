<?php $this->load->view('header',$this->data); ?>
<section class="header-survey">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page">
	<div class="container">
    	<div class="clear" style="height:20px;"></div>
        <div class="feedback-tablet-header">
        <div class="fright"><?php client_logo(); ?></div>
<?php setcookie('googtrans', '/en/en'); // set English by default on page load
//my_var_dump($_COOKIE['googtrans']); ?>
<style>
.goog-te-banner-frame.skiptranslate {
    display: none !important;
    } 
body {
    top: 0px !important; 
    }
.res-nav{display:none;}
</style>
<div id="google_translate_element"></div>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL}, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

        
        <div class="fleft letusknow"><img src="<?php echo base_url(); ?>images/letusknow.png" alt="" /></div>
        <div class="clear" style="height:20px;"></div>
        </div>
		
        <form name="CustomerFeedback" id="CustomerFeedback" method="post" action="">
        <div class="fleft">
        	
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>How would you rate your overall experience?</h3></div>
                <div class="overall_experience padding20"></div>
                <div  id="overall_experience_error_div" tabindex="4"></div>
            </div>
            
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>What type of trip was this?</h3></div>
                <div class="padding20 sort_of_trip_div">
                	<select class="sort_of_trip" name="sort_of_trip" required id="sort_of_trip" style="width:280px;height:45px;padding:5px;font-size:20px;">
                        <option value="">Please Select</option>
                        <option value="Business">Business</option>
                        <option value="Family">Family</option>
                        <option value="Couple">Couple</option>
                        <option value="Friends">Friends</option>
                        <option value="Single">Single</option>
                        <option value="Other" style="color:#45aa68;">OTHER</option>
                    </select>
                </div>
            </div>
            
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>How did you hear about us?</h3></div>
                <div class="padding20 booking_reference_div">
                	<select class="booking_reference" name="booking_reference" required id="booking_reference" style="width:280px;height:45px;padding:5px;font-size:20px;">
                        <option value="">Please Select</option>
                        <option value="I called the hotel">I called the hotel</option>
                        <option value="The reservation centre">The reservation centre</option>
                        <option value="I came directly">I came directly</option>
                        <option value="From website">From website</option>
                        <option value="Through a travel agent">Through a travel agent</option>
                        <option value="Someone booked the hotel on my behalf">Someone booked the hotel on my behalf</option>
                        <option value="Other" style="color:#45aa68;">OTHER</option>
                    </select>
                </div>
            </div>
            
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>How was your check-in experience?</h3></div>
                <div class="checkin_experience padding20"></div>
                <div  id="checkin_experience_error_div" tabindex="7"></div>
            </div>
            
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>How was the friendliness and attentiveness of our staff during your stay?</h3></div>
                <div class="friendliness_of_staff padding20"></div>
                <div  id="friendliness_of_staff_error_div" tabindex="5"></div>
            </div>
            
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>Your room & bathroom experience?</h3></div>
                <div class="room_experience padding20"></div>
                <div style="padding-left:20px; padding-bottom:20px;"><input style="width:280px;height:45px;padding:2px;font-size:14px;" type="text" name="bath_room_issue" id="bath_room_issue" value="" placeholder="Was it clean? Any issues with the furniture?" ></div>
                <div id="room_experience_error_div" tabindex="8"></div>
            </div>
            
            
        </div>
        <div class="fright survey-fix">
        	
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>What did you think of the breakfast selection?</h3></div>
                <div class="breakfast_experience padding20"></div>
                <div  id="breakfast_experience_error_div" tabindex="9"></div>
            </div>
            
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>Would you recommend us to one of your friends, colleagues or relatives?</h3></div>
                <div class="padding20">
                	<select name="recommend" required id="recommend" style="width:280px;height:45px;padding:5px;font-size:20px;">
                    	<option value="Yes">Yes</option>
                        <option value="No">No</option>
                        <option value="May be">May be</option>
                    </select>
                </div>
            </div>
            
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>Would you stay at this hotel again, if you were to return to this area?</h3></div>
                <div class="padding20">
                	<select name="stay_again" required id="stay_again" style="width:280px;height:45px;padding:5px;font-size:20px;">
                    	<option value="Yes">Yes</option>
                        <option value="No">No</option>
                        <option value="May be">May be</option>
                    </select>
                </div>
            </div>
            
            <div class="survey-box margin-bottom">
            	<div class="item-heading"><h3>Our location and nearby transport services?</h3></div>
                <div class="location_and_transport padding20"></div>
                <div  id="location_and_transport_error_div" tabindex="6"></div>
            </div>
            
            <div class="survey-box2 margin-bottom">
            	<h3 class="fleft">How can we do better? <span class="description">(Optional)</span></h3>
                <textarea name="how_do_better" id="how_do_better" class="UserComment"></textarea>
            </div>
            
            <div class="survey-box3 margin-bottom">
            	<h3>Receive a generous discount on your next visit <span class="description">(Optional)</span></h3>
                <input type="text" autocomplete="off" name="name" id="name" class="TextField" placeholder="Enter your name" />
                <input type="email" autocomplete="off" name="email" id="Email" class="TextField" placeholder="Enter your email address" />
            </div>
            
            <div id="FeebackError"></div>
            <div class="survey-btn margin-bottom" align="right">
            	&nbsp;<input type="submit" name="SubmitBtn" id="SubmitBtn" value="Submit" class="Button" />
            </div>
        </div>
        </form>
		
    	<div class="clear"></div>
    </div>
</section>
<script>
$(document).ready(function(){
	
	$('.overall_experience').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'overall_experience' });
	$('.checkin_experience').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'checkin_experience' });
	$('.friendliness_of_staff').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'friendliness_of_staff' });
	$('.room_experience').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'room_experience' });
	$('.breakfast_experience').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'breakfast_experience' });
	$('.location_and_transport').raty({ number: 5, path : '<?php echo base_url(); ?>images/', scoreName:'location_and_transport' });
	
});

$(document).on('submit', '#CustomerFeedback', function(){
	var Email = $('#Email').val();
	var name = $('#name').val();
	
	if(Email != '' || name != '')
	{
		console.log(Email);
		console.log(name);
		if(Email == '')
		{
			$('#FeebackError').html('Please enter a valid email address.');
			$('#Email').focus();
			console.log('Please enter a valid email address.');
			return false;
		}
		else if(name == '')
		{
			$('#FeebackError').html('Please enter your name.');
			$('#name').focus();
			console.log('Please enter your name.');
			return false;
		}
	}
	
		var Form = $('#CustomerFeedback').serialize();
		
		$('#FeebackError').html('Please wait...');
		$('#CustomerFeedback input').attr('disabled', 'disabled');
		
		$.post(WEB_URL+"order/submit_feedback/", Form, function(Data, textStatus){
			
			$('#CustomerFeedback input').removeAttr('disabled');
			
			if(Data.focus_element != '')
			{
				$('#overall_experience_error_div').html('');
				$('#friendliness_of_staff_error_div').html('');
				$('#quality_of_accommodation_error_div').html('');
				$('#checkin_experience_error_div').html('');
				$('#room_experience_error_div').html('');
				$('#breakfast_experience_error_div').html('');
				$('#FeebackError').html('');
				
				$('#'+Data.focus_element).html(Data.Msg);
				$('#'+Data.focus_element).focus();
				
			}
			else
			{
				$('#FeebackError').html(Data.Msg);
			}
			
			if(Data.Error==0)
				window.location.href=WEB_URL+'welcome/thankyou';
			
		}, 'json');
	
	return false;
});


$(document).on('change', '.sort_of_trip', function(){
	if($(this).val() == 'Other')
	{
		$('.sort_of_trip_div').html('<input name="sort_of_trip" required="required" autocomplete="off" type="text" id="sort_of_trip" placeholder="Please type here" class="TextField" style="width:280px;text-transform:capitalize;" >');
	}
});

$(document).on('change', '.booking_reference', function(){
	if($(this).val() == 'Other')
	{
		$('.booking_reference_div').html('<input name="booking_reference" required="required" autocomplete="off" type="text" id="booking_reference" placeholder="Please type here" class="TextField" style="width:280px;text-transform:capitalize;" >');
	}
});
</script>

<footer>
	<div class="container">
        <div align="center">
        <?php if(!isset($_SESSION[USER_LOGIN])) { ?>
        &nbsp;<a href="<?php echo base_url(); ?>">Home</a>&nbsp;&nbsp;<a href="<?php echo base_url(); ?>">Login</a>.&nbsp;&nbsp;<a href="<?php echo base_url(); ?>login/signup">Register</a>.
        <?php } ?>
        <br>&copy; Copyright <?php echo date('Y'); ?>. All rights reserved. &nbsp;
        </div>
        <div class="clear"></div>
    </div>
</footer>

<?php
$this->load->view('footer',$this->data);




