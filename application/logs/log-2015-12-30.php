<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2015-12-30 08:00:56 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-30 10:47:52 --> Severity: Warning --> mysqli::real_connect(): (28000/1045): Access denied for user 'root'@'localhost' (using password: NO) /home/ward0044/public_html/pref.mobi/system/database/drivers/mysqli/mysqli_driver.php 161
ERROR - 2015-12-30 10:47:52 --> Unable to connect to the database
ERROR - 2015-12-30 10:47:52 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /home/ward0044/public_html/pref.mobi/system/core/Exceptions.php:272) /home/ward0044/public_html/pref.mobi/system/core/Common.php 568
ERROR - 2015-12-30 10:59:39 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-30 12:32:56 --> 404 Page Not Found: Robotstxt/index
ERROR - 2015-12-30 21:50:18 --> 404 Page Not Found: Robotstxt/index
ERROR - 2015-12-30 22:32:36 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-30 22:32:36 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-30 22:37:49 --> Query error: Column 'email' cannot be null - Invalid query: INSERT INTO `ratings` (`restaurant_id`, `overall_experience`, `sort_of_trip`, `booking_reference`, `checkin_experience`, `friendliness_of_staff`, `room_experience`, `bath_room_issue`, `breakfast_experience`, `recommend`, `stay_again`, `location_and_transport`, `how_do_better`, `email`, `date_created`, `date_updated`) VALUES ('10', '3', 'Couple', 'I came directly', '4', '5', '3', '', '4', 'Yes', 'Yes', '5', 'A sdfa sfd a', NULL, '2015-12-30 22:37:49', '2015-12-30 22:37:49')
ERROR - 2015-12-30 22:45:16 --> Query error: Table 'pref_hotel.ratings_view' doesn't exist - Invalid query: SELECT `email`, `date_created`
FROM `ratings_view`
WHERE `restaurant_id` = '10'
AND `email` != ''
AND DATE(date_created) BETWEEN DATE('2015-11-30') AND DATE('2015-12-30')
ORDER BY `date_created` DESC
ERROR - 2015-12-30 22:49:49 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-30 22:51:04 --> 404 Page Not Found: Faviconico/index
