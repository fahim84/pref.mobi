<footer>
	<div class="container">
        <div align="center">
        <?php if(!isset($_SESSION[USER_LOGIN])) { ?>
        &nbsp;<a href="<?php echo base_url(); ?>">Login</a> | <a href="<?php echo base_url(); ?>login/signup">Register</a> | <a href="mailto:contact@pref.mobi">Contact Us</a> | <a href="<?php echo base_url(); ?>welcome/about_us">About Us</a>
        <?php } else { ?>
        &nbsp;<a href="<?php echo base_url(); ?>welcome/about_us">About Us</a>&nbsp; | &nbsp;<a href="<?php echo base_url(); ?>report/auth/">Feedback Reports</a>
        <?php } ?>
        <br>&copy; Copyright <?php echo date('Y'); ?>. All rights reserved. &nbsp;
        </div>
        <div class="clear"></div>
    </div>
</footer>
