<?php
class Report_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function count_feedbacks($restaurant_id,$start_date,$end_date)
	{
		$this->db->where("restaurant_id",$restaurant_id);
		$this->db->where('DATE(date_created) BETWEEN ',"DATE('$start_date') AND DATE('$end_date')",false);
		$this->db->from('ratings');
		$count_feedbacks = $this->db->count_all_results();
		//my_var_dump($this->db->last_query());
		
		return $count_feedbacks;
	}
	
	public function count_restaurant_by_business_type($business_type)
	{
		$this->db->where('business_type' , $business_type);
		$this->db->from('restaurants');
		return $this->db->count_all_results();
	}
	
	public function get_rank($restaurant_id,$business_type)
	{
		$query = $this->db->query("SELECT *,(SELECT SUM(overall_experience + checkin_experience + friendliness_of_staff + room_experience + breakfast_experience) FROM ratings WHERE restaurant_id=restaurants.id) total_rating FROM 
		restaurants WHERE business_type='$business_type' ORDER BY total_rating DESC");
		$i = 1;
		foreach($query->result() as $row)
		{
			if($row->id == $restaurant_id)
			{
				return $i;
			}
			$i++;
		}
		return $i;
	}
	
	public function get_column_graph_data($restaurant_id,$start_date,$end_date,$column)
	{
		$query = $this->db->query("SELECT COUNT(*) number_of_customers,$column, 
			COUNT(*)*100/(SELECT COUNT(*) FROM ratings WHERE restaurant_id=$restaurant_id) percentage
			FROM ratings WHERE restaurant_id=$restaurant_id 
			AND DATE(`date_created`) BETWEEN '$start_date' AND '$end_date'
			GROUP BY $column ORDER BY number_of_customers DESC");
			//my_var_dump($this->db->last_query());
		return $query;
	}
	
	public function get_ratings($where=array(), $order_by=array('id'=>'ASC'), $count_result = false )
	{
		foreach($order_by as $column => $direction)
		{
			$this->db->order_by($column,$direction);
		}
		foreach($where as $column => $value)
		{
			!is_array($value) ? $this->db->where($column,$value) : $this->db->where($column,$value[0],$value[1]);
		}
		
		# If true, count results and return it
		if($count_result)
		{
			$this->db->from('ratings');
			$count = $this->db->count_all_results();
			//my_var_dump($this->db->last_query());
			return $count;
		}
		
		$query = $this->db->get('ratings');
		//my_var_dump($this->db->last_query());
		return $query;
	}
	
	public function get_column_star_rating_graph_data($restaurant_id,$start_date,$end_date,$column)
	{
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND $column=1 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count1 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND $column=2 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count2 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND $column=3 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count3 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND $column=4 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count4 = $query->row()->count_rows;
		$query = $this->db->query("SELECT COUNT(*) count_rows FROM ratings WHERE restaurant_id=$restaurant_id AND $column=5 AND DATE(date_created) BETWEEN '$start_date' AND '$end_date'");
		$count5 = $query->row()->count_rows;
		
		$total_count = $count1 + $count2 + $count3 + $count4 + $count5;
		
		$percent1 = ($count1 / $total_count) * 100;
		$percent2 = ($count2 / $total_count) * 100;
		$percent3 = ($count3 / $total_count) * 100;
		$percent4 = ($count4 / $total_count) * 100;
		$percent5 = ($count5 / $total_count) * 100;
		
		$return['count1'] = $count1;
		$return['count2'] = $count2;
		$return['count3'] = $count3;
		$return['count4'] = $count4;
		$return['count5'] = $count5;
		$return['percent1'] = number_format($percent1, 0);
        $return['percent2'] = number_format($percent2, 0);
        $return['percent3'] = number_format($percent3, 0);
        $return['percent4'] = number_format($percent4, 0);
        $return['percent5'] = number_format($percent5, 0);
		$return['total_count'] = $total_count;
		
		return $return;
	}
	
	public function generate_report($restaurant_id,$start_date,$end_date)
	{
		# Get rating
		$where_condition['restaurant_id'] = $restaurant_id;
		$where_condition['DATE(date_created) BETWEEN '] = array("DATE('$start_date') AND DATE('$end_date')",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
		$order_by['id'] = 'DESC';
		$total_number_of_ratings = self::get_ratings($where_condition, $order_by, true);
		//my_var_dump($total_number_of_ratings);
		if($total_number_of_ratings < 1)
		{
			return false;
		}
        //my_var_dump(array("Report Summary From ".$start_date." to ".$end_date));
		//my_var_dump(array("Total Reviews", $total_number_of_ratings));
		$return_data['report_heading'] = "Report Summary From ".$start_date." to ".$end_date;
		$return_data['total_reviews'] = array("Total Reviews", $total_number_of_ratings);
		
		$return_data['question_header'] = array('', "1 Star", "2 Star", "3 Star", "4 Star", "5 Star");
		
		# Get Star Rating Data
		$column = 'overall_experience';
		$star_rating_data = self::get_column_star_rating_graph_data($restaurant_id,$start_date,$end_date,$column);
		$star_rating_data1 = array($star_rating_data['count1'], $star_rating_data['percent1'].'%');
		$star_rating_data2 = array($star_rating_data['count2'], $star_rating_data['percent2'].'%');
		$star_rating_data3 = array($star_rating_data['count3'], $star_rating_data['percent3'].'%');
		$star_rating_data4 = array($star_rating_data['count4'], $star_rating_data['percent4'].'%');
		$star_rating_data5 = array($star_rating_data['count5'], $star_rating_data['percent5'].'%');
		
		$return_data['overall_experience_data'] = array("Overall Experience", $star_rating_data1, $star_rating_data2, $star_rating_data3, $star_rating_data4, $star_rating_data5);
		
		# Get Star Rating Data
		$column = 'checkin_experience';
		$star_rating_data = self::get_column_star_rating_graph_data($restaurant_id,$start_date,$end_date,$column);
		$star_rating_data1 = array($star_rating_data['count1'], $star_rating_data['percent1'].'%');
		$star_rating_data2 = array($star_rating_data['count2'], $star_rating_data['percent2'].'%');
		$star_rating_data3 = array($star_rating_data['count3'], $star_rating_data['percent3'].'%');
		$star_rating_data4 = array($star_rating_data['count4'], $star_rating_data['percent4'].'%');
		$star_rating_data5 = array($star_rating_data['count5'], $star_rating_data['percent5'].'%');
		
		$return_data['checkin_experience_data'] = array("Check-in Experience", $star_rating_data1, $star_rating_data2, $star_rating_data3, $star_rating_data4, $star_rating_data5);
		
		# Get Star Rating Data
		$column = 'friendliness_of_staff';
		$star_rating_data = self::get_column_star_rating_graph_data($restaurant_id,$start_date,$end_date,$column);
		$star_rating_data1 = array($star_rating_data['count1'], $star_rating_data['percent1'].'%');
		$star_rating_data2 = array($star_rating_data['count2'], $star_rating_data['percent2'].'%');
		$star_rating_data3 = array($star_rating_data['count3'], $star_rating_data['percent3'].'%');
		$star_rating_data4 = array($star_rating_data['count4'], $star_rating_data['percent4'].'%');
		$star_rating_data5 = array($star_rating_data['count5'], $star_rating_data['percent5'].'%');
		
		$return_data['friendliness_of_staff_data'] = array("Friendliness of Staff", $star_rating_data1, $star_rating_data2, $star_rating_data3, $star_rating_data4, $star_rating_data5);
		
		# Get Star Rating Data
		$column = 'room_experience';
		$star_rating_data = self::get_column_star_rating_graph_data($restaurant_id,$start_date,$end_date,$column);
		$star_rating_data1 = array($star_rating_data['count1'], $star_rating_data['percent1'].'%');
		$star_rating_data2 = array($star_rating_data['count2'], $star_rating_data['percent2'].'%');
		$star_rating_data3 = array($star_rating_data['count3'], $star_rating_data['percent3'].'%');
		$star_rating_data4 = array($star_rating_data['count4'], $star_rating_data['percent4'].'%');
		$star_rating_data5 = array($star_rating_data['count5'], $star_rating_data['percent5'].'%');
		
		$return_data['room_experience_data'] = array("Room and Bathroom Experience", $star_rating_data1, $star_rating_data2, $star_rating_data3, $star_rating_data4, $star_rating_data5);
		
		# Get Star Rating Data
		$column = 'breakfast_experience';
		$star_rating_data = self::get_column_star_rating_graph_data($restaurant_id,$start_date,$end_date,$column);
		$star_rating_data1 = array($star_rating_data['count1'], $star_rating_data['percent1'].'%');
		$star_rating_data2 = array($star_rating_data['count2'], $star_rating_data['percent2'].'%');
		$star_rating_data3 = array($star_rating_data['count3'], $star_rating_data['percent3'].'%');
		$star_rating_data4 = array($star_rating_data['count4'], $star_rating_data['percent4'].'%');
		$star_rating_data5 = array($star_rating_data['count5'], $star_rating_data['percent5'].'%');
		
		$return_data['breakfast_experience_data'] = array("Breakfast Experience", $star_rating_data1, $star_rating_data2, $star_rating_data3, $star_rating_data4, $star_rating_data5);
		
		# Get Star Rating Data
		$column = 'location_and_transport';
		$star_rating_data = self::get_column_star_rating_graph_data($restaurant_id,$start_date,$end_date,$column);
		$star_rating_data1 = array($star_rating_data['count1'], $star_rating_data['percent1'].'%');
		$star_rating_data2 = array($star_rating_data['count2'], $star_rating_data['percent2'].'%');
		$star_rating_data3 = array($star_rating_data['count3'], $star_rating_data['percent3'].'%');
		$star_rating_data4 = array($star_rating_data['count4'], $star_rating_data['percent4'].'%');
		$star_rating_data5 = array($star_rating_data['count5'], $star_rating_data['percent5'].'%');
		
		$return_data['location_and_transport_data'] = array("Location and Transport Experience", $star_rating_data1, $star_rating_data2, $star_rating_data3, $star_rating_data4, $star_rating_data5);
		
		# Get Rating Data
		$column = 'sort_of_trip';
		$query = self::get_column_graph_data($restaurant_id,$start_date,$end_date,$column);
		$return_data[$column.'_query'] = $query;
		
		# Get Rating Data
		$column = 'booking_reference';
		$query = self::get_column_graph_data($restaurant_id,$start_date,$end_date,$column);
		$return_data[$column.'_query'] = $query;
		
		# Get Rating Data
		$column = 'recommend';
		$query = self::get_column_graph_data($restaurant_id,$start_date,$end_date,$column);
		$return_data[$column.'_query'] = $query;
		
		# Get Rating Data
		$column = 'stay_again';
		$query = self::get_column_graph_data($restaurant_id,$start_date,$end_date,$column);
		$return_data[$column.'_query'] = $query;
		
		return $return_data;
	}
}


