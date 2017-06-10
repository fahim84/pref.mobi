<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2015-12-09 08:46:52 --> Query error: Table 'pref_hotel.ratings_view' doesn't exist - Invalid query: SELECT COUNT(*) number_of_rating, COUNT(*) / 3 * 100 percent, ratings_view.*
				FROM ratings_view
				WHERE (staff_id IN(SELECT id FROM staffs WHERE restaurant_id=11) OR staff_id=0) 
				AND DATE(date_created) BETWEEN DATE('2015-12-01 19:10:21') AND DATE('2015-12-09')
				GROUP BY staff_id 
				ORDER BY COUNT(*) DESC LIMIT 1
ERROR - 2015-12-09 08:46:54 --> 404 Page Not Found: Faviconico/index
