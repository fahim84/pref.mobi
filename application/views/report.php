<?php $this->load->view('header',$this->data); ?>
<section class="header header-report">
	<?php $this->load->view('header_section',$this->data); ?>
</section>

<section class="inner-page reports-page" style="min-height:400px;">
	<div class="container">
    	<div class="content-page register-now menu-page">
        	<div class="fleft"><h2>Feedback Stats</h2></div>
        	<div class="fright"><?php client_logo(); ?></div>
        	<div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    <?php $this->load->view('report-tabs',$this->data); ?>
    <div class="charts">
    <?php
		if($count_feedbacks > 1)
		{
	?>
    	<div class="container">
        	<div align="center"><h2 class="error" id="BusinessRank"></h2></div>
        	<div class="fleft chart-box1">
            	<form name="TopLowRatedItemsForm" id="TopLowRatedItemsForm" method="post" action="<?php echo base_url(); ?>report/index">
                        <div class="fleft">From:<br />
                        <input type="text" name="start_date" id="start_date" class="TextField DateField" value="<?php echo $start_date; ?>" />
                        </div>
                        <div class="fleft">To:<br />
                        <input type="text" name="end_date" id="end_date" class="TextField DateField" value="<?php echo $end_date; ?>" />
                        </div>
                        <div class="fleft">
                         
                          <input type="submit" name="tl-btn" id="tl-btn" value="Submit" class="Button" />&nbsp;<span id="tlError">&nbsp;</span>
                          </div>
                        <div class="clear"></div>
                        </form>
                <div class="chart-box">
                	<h2>Overall customer experience</h2>
                    <iframe src="<?php echo base_url(); ?>report/column_star_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=overall_experience" frameborder="0" id="CustomerExpFrame" scrolling="auto" width="100%" height="320"></iframe>
                </div>
                <div class="chart-box">
                	<h2>What type of trip was this?</h2>
                    <iframe src="<?php echo base_url(); ?>report/column_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=sort_of_trip" width="100%" height="320" frameborder="0" scrolling="auto"></iframe>
                </div>
                <div class="chart-box">
                	<h2>How did you hear about us?</h2>
                    <iframe src="<?php echo base_url(); ?>report/column_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=booking_reference" width="100%" height="320" frameborder="0" scrolling="auto"></iframe>
                </div>
                <div class="chart-box">
                	<h2>Check-in  experience</h2>
                    <iframe src="<?php echo base_url(); ?>report/column_star_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=checkin_experience" frameborder="0" id="CustomerExpFrame" scrolling="auto" width="100%" height="320"></iframe>
                </div>
                <div class="chart-box">
                	<h2>Friendliness and attentiveness of our staff</h2>
                    <iframe src="<?php echo base_url(); ?>report/column_star_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=friendliness_of_staff" frameborder="0" id="CustomerExpFrame" scrolling="auto" width="100%" height="320"></iframe>
                </div>
                
                
            </div>
            <div class="fright chart-box2">
            	<div class="employee-month">
                	
                    <div class="chart-box">
                        <h2>Breakfast selection</h2>
                        <iframe src="<?php echo base_url(); ?>report/column_star_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=breakfast_experience" width="100%" height="320" frameborder="0" scrolling="yes"></iframe>
                    </div>
                    
                    <div class="chart-box">
                        <h2>Would you recommend us?</h2>
                        <iframe src="<?php echo base_url(); ?>report/column_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=recommend" width="100%" height="320" frameborder="0" scrolling="auto"></iframe>
                    </div>
                    <div class="chart-box">
                        <h2>Would you stay again?</h2>
                        <iframe src="<?php echo base_url(); ?>report/column_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=stay_again" width="100%" height="320" frameborder="0" scrolling="auto"></iframe>
                    </div>
                    <div class="chart-box">
                        <h2>Location and transport services?</h2>
                        <iframe src="<?php echo base_url(); ?>report/column_star_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=location_and_transport" width="100%" height="320" frameborder="0" scrolling="auto"></iframe>
                    </div>
                    
                    <div class="chart-box">
                        <h2>Room & bathroom experience</h2>
                        <iframe src="<?php echo base_url(); ?>report/column_star_rating_graph/?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&column=room_experience" frameborder="0" id="CustomerExpFrame" scrolling="auto" width="100%" height="320"></iframe>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    <?php
		}
		else
		{
			echo '<div align="center"><h2 class="error">You should have atleast 10 feedbacks to generate report</h2></div>';
		}
	?>
    </div>
</section>
<!--<link href="<?php echo base_url(); ?>css/jquery-ui-timepicker-addon.css" rel="stylesheet" media="screen">
<script src="<?php echo base_url(); ?>js/jquery-ui-timepicker-addon.js"></script>-->
<script>
$(document).ready(function(){
	
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

GetBusinessRank();
</script>
<?php
$this->load->view('footer_section',$this->data);
$this->load->view('footer',$this->data);




