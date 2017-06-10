<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2015-12-01 18:37:36 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-01 18:37:36 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-01 18:38:53 --> Query error: Unknown column 'room_experience' in 'field list' - Invalid query: INSERT INTO `ratings` (`restaurant_id`, `overall_experience`, `friendliness_of_staff`, `quality_of_accommodation`, `checkin_experience`, `breakfast_experience`, `room_experience`, `booking_reference`, `sort_of_trip`, `stay_again`, `how_do_better`, `specail_offer_email`, `date_created`, `date_updated`) VALUES ('10', '3', '4', '2', '4', '5', '3', 'The reservation centre', 'Friends', 'No', 'Make it more productive.', 'fahim@blazebuddies.com', '2015-12-01 18:38:53', '2015-12-01 18:38:53')
ERROR - 2015-12-01 19:10:22 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-01 19:12:41 --> 404 Page Not Found: Faviconico/index
ERROR - 2015-12-01 19:28:43 --> Query error: View 'pref_hotel.ratings_view' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them - Invalid query: SELECT COUNT(*) number_of_rating, COUNT(*) / 1 * 100 percent, ratings_view.*
				FROM ratings_view
				WHERE (staff_id IN(SELECT id FROM staffs WHERE restaurant_id=11) OR staff_id=0) 
				AND DATE(date_created) BETWEEN DATE('2015-12-01 19:10:21') AND DATE('2015-12-01')
				GROUP BY staff_id 
				ORDER BY COUNT(*) DESC LIMIT 1
