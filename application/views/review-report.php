<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page reports-page">
	<div class="container">
    	<div class="content-page register-now menu-page">
        	<div class="fleft"><h2>Reviews</h2></div>
        	<div class="fright"><?php client_logo(); ?></div>
        	<div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    <?php $this->load->view('report-tabs',$this->data); ?>
    <div class="charts">
    	<div class="container" align="center">
        
        <div class="review-container" >
			<br><br>
        	<form name="SearchReviewsForm" id="SearchReviewsForm" method="post" action="<?php echo base_url(); ?>report/review_report">
            
            <div align="center">
            From Date:<br />
            <input type="text" name="start_date" id="start_date" class="TextField" value="<?php echo $start_date; ?>" />
            <div class="clear height10">&nbsp;</div>
            
            </div>
            <div align="center">
            To Date:<br />
            <input type="text" name="end_date" id="end_date" class="TextField" value="<?php echo $end_date; ?>" />
            <br><br>
            <input type="submit" name="SubmitBtn" id="SubmitBtn" class="Button" value="Submit" >
            <div class="clear height10">&nbsp;</div>
            
            </div>
            <div class="clear height10">&nbsp;</div>
            <div align="center" >
            
            </div>
            <div align="center"><?php echo $rating_query->num_rows(); ?> total records found</div>
            <div class="clear height10">&nbsp;</div>
            </form>
        <?php
			if($rating_query->num_rows())
			{
				foreach($rating_query->result() as $row )
				{
					$rating_id = $row->id;
					$PostedBy='User';
					
					if($row->email != '')	$Emails[] = $row->email;
					
					$Comments = $row->how_do_better;
					if($Comments=='')
						$Comments='No comments';
					
					
		?>
        <div class="review-box">
        	<div><h2>Posted by: <?php echo $PostedBy; ?></h2></div>
            <div class="clear border-bottom"></div>
            <div class="clear height10"></div>
            <div>Posted Date: <?php echo date('F d Y',strtotime($row->date_created))." at ".date('h:i a',strtotime($row->date_created)); ?></div>
            <div class="clear height10"></div>
            
            <div class="other-stars">
            	<h3 data-id="cs<?php echo $rating_id; ?>">Customer service</h3>
                <div class="services-accordian" style="display:none;" id="rating-accordian-cs<?php echo $rating_id; ?>">
                    <div class="comments"><strong>Overall customer experience<br /><span class="showStarRating" data-rate="<?php echo $row->overall_experience; ?>"></span></strong></div>
                    <div class="comments"><strong>Type of trip</strong><div><?php echo $row->sort_of_trip; ?></div></div>
                    <div class="comments"><strong>Hear about us</strong><div><?php echo $row->booking_reference; ?></div></div>
                    <div class="comments"><strong>Check-in experience<br /><span class="showStarRating" data-rate="<?php echo $row->checkin_experience; ?>"></span></strong></div>
                    <div class="comments"><strong>Friendliness and attentiveness<br /><span class="showStarRating" data-rate="<?php echo $row->friendliness_of_staff; ?>"></span></strong></div>
                    <div class="comments"><strong>Room & bathroom experience<br /><span class="showStarRating" data-rate="<?php echo $row->room_experience; ?>"></span></strong></div>
                    <div class="comments"><strong>Breakfast selection<br /><span class="showStarRating" data-rate="<?php echo $row->breakfast_experience; ?>"></span></strong></div>
                    
                    <div class="comments"><strong>Would you recommend us?</strong><div><?php echo $row->recommend; ?></div></div>
                    <div class="comments"><strong>Would you stay again?</strong><div><?php echo $row->stay_again; ?></div></div>
                    <div class="comments"><strong>Location and transport services?<br /><span class="showStarRating" data-rate="<?php echo $row->location_and_transport; ?>"></span></strong></div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="comments"><strong>How can we do better?</strong><div><?php echo nl2br($Comments); ?></div></div>
            <div class="clear"></div>
        </div>
        <?php			
				}
			}
			else
			{
				echo '<div align="center">No reviews found</div>';
			}
		?>
        <div >
        	<div class="review-box email-addresses">
            	<div class="fleft"><h2>Email Addresses</h2></div>
                <div class="fright"><?php if(is_array(@$Emails)) { ?><a href="<?php echo base_url(); ?>report/export_email_addresses/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">Export</a><?php } ?></div>
                <div class="clear"></div>
                <?php
					if(is_array(@$Emails))
					{
						echo '<ul>';
						foreach($Emails as $Email)
						{
							echo '<li>'.$Email.'</li>';
						}
						echo '</ul>';
					}
					else
						echo '<div allign="center">No email address found</div>';
				?>
            </div>
        </div>
        </div>
        
        <div class="clear"></div>
        </div>
    </div>
</section>
<script>
$(document).ready(function(){
	
	$('.showStarRating').raty({ readOnly: true, score: function(){ return $(this).attr('data-rate'); }, path : '<?php echo base_url(); ?>images/' });
	
	$("#start_date").datepicker({dateFormat: 'yy-mm-dd',
        //minDate: 0,
        //maxDate: "+60D",
        numberOfMonths: 1,
        onSelect: function(selected) {
          $("#end_date").datepicker("option","minDate", selected)
        }
    });
    $("#end_date").datepicker({ dateFormat: 'yy-mm-dd',
        //minDate: 0,
        //maxDate:"+60D",
        numberOfMonths: 1,
        onSelect: function(selected) {
           $("#start_date").datepicker("option","maxDate", selected)
        }
    });
	
});

$(document).on('click', '.menu-stars h3, .other-stars h3', function(){
	var id = $(this).attr('data-id');
	var ShowID = 'rating-accordian-'+id;
	if($(this).hasClass('active'))
	{
		$(this).removeClass('active');
		$('#'+ShowID).slideUp();
	}
	else
	{
		$('.menu-stars h3, .other-stars h3').removeClass('active');
		$(this).addClass('active');
		$('.ratings-accordian, .services-accordian').slideUp();
		$('#'+ShowID).slideDown();
	}
});

</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




