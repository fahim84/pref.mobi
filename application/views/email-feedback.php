<?php $this->load->view('header',$this->data); ?>
<?php
// get rating
$rating_result = $this->db->query("SELECT * FROM ratings WHERE id=$rating_id");
$rating = $rating_result->row_array();
$restaurant_id = $rating['restaurant_id'];

//my_var_dump($rating);

// get restaurant
$restaurant = $_SESSION[USER_LOGIN];

?>

<section class="inner-page">
	<div class="container">
    	<div class="clear" style="height:20px;"></div>
        
		<form name="CustomerFeedback" id="CustomerFeedback" method="post" action="">
        <div class="fleft">
        	<div class="survey-box2 margin-bottom">
            	<h3>How would you rate your overall experience?</h3>
                <img src="<?php echo base_url().'images/'.$rating['overall_experience']; ?>star.png" alt="<?php echo $rating['overall_experience']; ?>" />
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>What sort of trip was this?</h3>
                <?php echo $rating['sort_of_trip']; ?>
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>How did you book this hotel?</h3>
                <?php echo $rating['booking_reference']; ?>
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>Your check-in experience?</h3>
                <img src="<?php echo base_url().'images/'.$rating['checkin_experience']; ?>star.png" alt="<?php echo $rating['checkin_experience']; ?>" />
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>The friendliness and attentiveness of our staff?</h3>
                <img src="<?php echo base_url().'images/'.$rating['friendliness_of_staff']; ?>star.png" alt="<?php echo $rating['friendliness_of_staff']; ?>" />
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>Your room & bathroom experience?</h3>
                <div><img src="<?php echo base_url().'images/'.$rating['room_experience']; ?>star.png" alt="<?php echo $rating['room_experience']; ?>" /></div>
                <div><?php echo $rating['bath_room_issue']; ?></div>
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>What did you think of the breakfast selection?</h3>
                <img src="<?php echo base_url().'images/'.$rating['breakfast_experience']; ?>star.png" alt="<?php echo $rating['breakfast_experience']; ?>" />
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>Would you recommend us to one of your friends, colleagues or relatives?</h3>
                <?php echo $rating['recommend']; ?>
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>Would you stay at this hotel again, if you were to return to this area?</h3>
                <?php echo $rating['stay_again']; ?>
            </div>
            <div class="survey-box2 margin-bottom">
            	<h3>Our location and nearby transport services?</h3>
                <img src="<?php echo base_url().'images/'.$rating['location_and_transport']; ?>star.png" alt="<?php echo $rating['location_and_transport']; ?>" />
            </div>
            <?php if($rating['how_do_better']){?>
            <div class="survey-box2 margin-bottom">
            	<h3>How can we do better?</h3>
                <?php echo $rating['how_do_better']; ?>
            </div>
            <?php } ?>
            <div class="survey-btn margin-bottom" align="right">
            	<span id="FeebackError"></span>&nbsp;
            </div>
        </div>
        </form>
		
    	<div class="clear"></div>
    </div>
</section>
<?php
$this->load->view('footer',$this->data);
?>