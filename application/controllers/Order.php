<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller 
{
	var $data;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('login_model');
		$this->load->model('restaurant_model');
		$this->load->model('order_model');
		
		# if user is not logged in, then redirect him to login page
		if(! isset($_SESSION[USER_LOGIN]['id']) )
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Msg = $this->restaurant_model->check_menu_staff($restaurant_id);
		if($Msg != '')
		{
			$_SESSION[USER_RETURN_MSG]['Msg']=$Msg;
			redirect(base_url());
		}
		reset_survey();
		$order_timestamp = time();
		
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		$this->data['Table'] = $this->input->get_post('Table');
		$this->data['LoadCheckbox'] = 1;
		$this->data['order_timestamp'] = $order_timestamp;
		$this->data['Active'] = 'home';
		$this->load->view('take-order',$this->data);
	}
	
	public function take_order()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$MyItems = $this->input->get_post('selecteditems');
		$Url='';
		$action = '';
		$temporary_pending_orders_count = 0;
		$table_number = 0;
		
		$order_timestamp = $this->input->get_post('order_timestamp');
		$overwrite_table_number = $this->input->get_post('overwrite_table_number');
		$Table = $this->input->get_post('Table');
		$customerid = $this->input->get_post('customerid');
		$selecteditems = $this->input->get_post('selecteditems');
		$temporary = $this->input->get_post('temporary');
		$redirect_file = $this->input->get_post('redirect_file');
		$selecteditemsQuantity = $this->input->get_post('selecteditemsQuantity');
		$request_comment = $this->input->get_post('request_comment');
		
		if($overwrite_table_number > 0) // If user want to overwrite orders
		{
			$where_condition['DATE(date_created)'] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
			$where_condition['order_timestamp !='] = $order_timestamp;
			$where_condition['review_done'] = 0;
			$where_condition['restaurant_id'] = $restaurant_id;
			$where_condition['table_number'] = $overwrite_table_number;
			$where_condition['deleted'] = 0;
			$where_condition['temporary'] = $temporary;
			// delete existing orders
			$this->order_model->update_orders($where_condition, array('deleted' => 1) );
		}
		if($Table=='')
		{
			$Error=1;
			$Msg='<div class="alert alert-error">Please select table</div>';
		}
		else if($customerid == '' or $customerid < 1)
		{
			$Error=1;
			$Msg='<div class="alert alert-error">Please enter customer id in the range of 1 to 100</div>';
		}
		else if(!is_array($selecteditems))
		{
			$Error=1;
			$Msg='<div class="alert alert-error">Please select order items</div>';
		}
		elseif($this->order_model->check_existing_order_on_same_table($restaurant_id,$Table,$temporary,$order_timestamp))
		{
			$Error=1;
			$action = 'hide confirm order button';
			$Msg="<div class='alert alert-warning'>There is already an existing order for Table $Table</div><div class='alert alert-warning'>Would you like to replace the existing one?</div>";
			$Msg.='<div><input type="hidden" name="overwrite_table_number" value="'.$Table.'" >
			<input type="submit" name="overwrite_button" id="overwrite_button" value="Yes Replace It" class="Button" style="background-color:#C30003;" />
			<input type="button" name="overwrite_cancel" id="overwrite_cancel" value="No" class="Button" style="background-color:#C30003;" /></div><br>';
		}
		else
		{
			
			$Error=0;
			$Msg='';
			$table_number = $Table;
			
			$insert_array = array();
			$insert_array['restaurant_id'] = $restaurant_id;
			$insert_array['table_number'] = $Table;
			$insert_array['customer_number'] = $customerid;
			$insert_array['temporary'] = $temporary;
			$insert_array['order_timestamp'] = $order_timestamp;
			
			$order_id = $this->order_model->add_order($insert_array);
			
			# count temporary orders
			if($temporary > 0)
			{
				$temporary_pending_orders_count = $this->order_model->count_temporary_pending_orders($restaurant_id,$Table);
			}
			
			$Count=0;
			foreach($MyItems as $ItemID)
			{
				$ItemID = str_replace("Item_", "", $ItemID);
				$Quantity = $selecteditemsQuantity[$Count];
				$req_comment = $request_comment[$ItemID];
				
				$insert_array = array();
				$insert_array['order_id'] = $order_id;
				$insert_array['menu_id'] = $ItemID;
				$insert_array['quantity'] = $Quantity;
				$insert_array['request_comment'] = $req_comment;
				$order_item_id = $this->order_model->add_order_item($insert_array);
				
				$Count++;
			}
			//$_SESSION[USER_RETURN_MSG]['Msg']='Order(s) saved successfully!';
			$Msg="Customer $customerid order saved";
			
			$Url=base_url().$redirect_file."?Table=$Table";
		}
		
		$Return["Error"]=$Error;
		$Return["Msg"]=$Msg;
		$Return["Url"]=$Url;
		$Return["action"] = $action;
		$Return['temporary_pending_orders_count'] = $temporary_pending_orders_count;
		$Return['table_number'] = $table_number;
		echo json_encode($Return);
	}
	
	public function existing_orders()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Msg = $this->restaurant_model->check_menu_staff($restaurant_id);
		if($Msg != '')
		{
			$_SESSION[USER_RETURN_MSG]['Msg']=$Msg;
			redirect(base_url());
		}
		reset_survey();
		
		$where_condition['DATE(date_created) >='] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
		$where_condition['review_done'] = 0;
		$where_condition['restaurant_id'] = $restaurant_id;
		$where_condition['deleted'] = 0;
		$where_condition['temporary'] = 0;
		$order_by['id'] = 'ASC';
		
		$this->data['orders_query'] = $this->order_model->get_orders($where_condition, $order_by);
		$this->data['Table'] = $this->input->get_post('Table');
		$this->data['LoadCheckbox'] = 0;
		$this->data['Active'] = 'home';
		$this->load->view('existing_orders',$this->data);
	}
	
	public function edit_order()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$order_id = $this->input->get_post('order_id');
		$Msg = $this->restaurant_model->check_menu_staff($restaurant_id);
		if($Msg != '')
		{
			$_SESSION[USER_RETURN_MSG]['Msg']=$Msg;
			redirect(base_url());
		}
		reset_survey();
		
		$customerid = $this->input->get_post('customerid');
		$Table = $this->input->get_post('Table');
		$selecteditems = $this->input->get_post('selecteditems');
		$selecteditemsQuantity = $this->input->get_post('selecteditemsQuantity');
		$request_comment = $this->input->get_post('request_comment');
		
		if($_POST)
		{
			$MyItems = $this->input->get_post('Items');
	
			if($Table=='')
			{
				$_SESSION['msg_error'][] = 'Please select table';
			}
			
			if($customerid == '' or $customerid < 1)
			{
				$_SESSION['msg_error'][] = 'Please enter customer id in the range of 1 to 100';
			}
			
			if(!is_array($selecteditems))
			{
				$_SESSION['msg_error'][] = 'Please select order items';
			}
			
			if( ! isset($_SESSION['msg_error']) )
			{
				$this->order_model->update_order($order_id, array('table_number' => $Table,'customer_number' => $customerid) );
				
				# Delete previous all records for this order
				$this->order_model->delete_all_items_of_this_order($order_id);
				
				$Count=0;
				foreach($MyItems as $ItemID)
				{
					$Quantity = $selecteditemsQuantity[$Count];
					$req_comment = $request_comment[$ItemID];
					
					$insert_array = array();
					$insert_array['order_id'] = $order_id;
					$insert_array['menu_id'] = $ItemID;
					$insert_array['quantity'] = $Quantity;
					$insert_array['request_comment'] = $req_comment;
					$order_item_id = $this->order_model->add_order_item($insert_array);
					
					$Count++;
				}
				$_SESSION['msg_success'][] = 'Order updated successfully!';
				
				redirect('order/existing_orders');
				exit;
			}
			
			redirect("order/edit_order/?order_id=$order_id");
			exit;
		}
		
		
		
		$order_items_query = $this->order_model->get_order_items($order_id);
		$items = array();
		foreach($order_items_query->result() as $row)
		{
			$items[$row->menu_id] = $row;
		}
		
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		$this->data['order_id'] = $order_id;
		$this->data['order'] = $this->order_model->get_order_by_id($order_id);
		$this->data['order_items_query'] = $order_items_query;
		$this->data['items'] = $items;
		$this->data['LoadCheckbox'] = 1;
		$this->data['Active'] = 'home';
		$this->load->view('edit-order',$this->data);
	}
	public function menu_list()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Msg = $this->restaurant_model->check_menu_staff($restaurant_id);
		if($Msg != '')
		{
			$_SESSION[USER_RETURN_MSG]['Msg']=$Msg;
			redirect(base_url());
		}
		reset_survey();
		$order_timestamp = time();
		
		# Delete all existing temporary pending orders of today date
		$this->order_model->delete_all_temporary_pending_orders($restaurant_id);
		
		$where_condition['menus.restaurant_id'] = $restaurant_id;
		$order_by['category_id'] = 'ASC';
		$order_by['menu_number'] = 'ASC';
		
		$this->data['menu_query'] = $this->restaurant_model->get_menus($where_condition, $order_by);
		$this->data['Table'] = $this->input->get_post('Table');
		$this->data['LoadCheckbox'] = 1;
		$this->data['order_timestamp'] = $order_timestamp;
		$this->data['Active'] = 'home';
		$this->load->view('menu_list',$this->data);
	}
	
	# Check and count temporary pending orders for feedback
	public function count_temporary_pending_orders()
	{
		$table_number = $this->input->get_post('table_number');
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		
		$return['total_orders_pending'] = $this->order_model->count_temporary_pending_orders($restaurant_id,$table_number);
		$return["Error"]=0;
		$return["Msg"]='';
		echo json_encode($return);
	}
	
	public function make_feedback_for_single_table()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		
		$Table = $this->input->get_post('Table');
		$temporary = $this->input->get_post('temporary');
		
		if($Table=='')
		{
			$Error=1;
			$Msg='<div class="alert alert-error">Please select table</div>';
		}
		else
		{
			$where_condition['DATE(date_created)'] = array("'".date('Y-m-d')."'",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
			$where_condition['review_done'] = 0;
			$where_condition['restaurant_id'] = $restaurant_id;
			$where_condition['table_number'] = $Table;
			$where_condition['deleted'] = 0;
			$where_condition['temporary'] = $temporary;
			$order_by['customer_number'] = 'ASC';
			
			$orders_query = $this->order_model->get_orders($where_condition, $order_by);
			
			$total_orders = $orders_query->num_rows();
			if($total_orders == 0)
			{
				$Error=1;
				$Msg='<div class="alert alert-error">No recent order found for this table</div>';
			}
			else
			{
				reset_survey();
				$_SESSION[SURVEY_COUNT_TABLE]=$total_orders;
				$_SESSION[SURVEY_LOOP]	= $total_orders;
				$_SESSION[SURVEY_TABLE]	= $Table;
				
				$i = 0;
				foreach($orders_query->result() as $row)
				{
					$i++;
					$order_id = $row->id;
					$customer_number = $row->customer_number;
					
					$orders[$i] = $row;
					
					$orders_items_query = $this->order_model->get_order_items($order_id);
					
					$Items = array();
					foreach($orders_items_query->result() as $row2)
					{
						$Items[] = $row2->menu_id;
					}
					
					$_SESSION[SURVEY_ITEMS][$i]	= $Items;
				}
				$_SESSION[SURVEY_ORDER] = $orders;
				$Error=0;
				$Msg = 'Order is ready for feedback.';
			}
		}
		
		$Return["Error"]=$Error;
		$Return["Msg"]=$Msg;
		echo json_encode($Return);
	}
	
	public function make_feedback_for_single_order()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		
		$order_id = $this->input->get_post('SelectedOrder');
		$temporary = $this->input->get_post('temporary');
		
		$order_row = $this->order_model->get_order_by_id($order_id);
		
		$customer_number = $order_row->customer_number;
		$Table = $order_row->table_number;
		
		$orders_items_query = $this->order_model->get_order_items($order_id);
		foreach($orders_items_query->result() as $row2)
		{
			$Items[] = $row2->menu_id;
		}
		
		reset_survey();
		$_SESSION[SURVEY_COUNT_TABLE]=1;
		$_SESSION[SURVEY_ORDER]	= array('1'=> $order_row);
		$_SESSION[SURVEY_LOOP]	= 1;
		$_SESSION[SURVEY_TABLE]	= $Table;
		$_SESSION[SURVEY_ITEMS][1]	= $Items;
		$Error=0;
		$Msg = 'Order is ready for feedback.';
		
		$Return["Error"]=$Error;
		$Return["Msg"]=$Msg;
		echo json_encode($Return);
	}
	
	public function start_feedback()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		
		$this->data['Active'] = 'menu';
		$this->load->view('start-feedback',$this->data);
	}
	
	public function submit_feedback()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$overall_experience = $this->input->get_post('overall_experience');
		$sort_of_trip = $this->input->get_post('sort_of_trip');
		$booking_reference = $this->input->get_post('booking_reference');
		$checkin_experience = $this->input->get_post('checkin_experience');
		$friendliness_of_staff = $this->input->get_post('friendliness_of_staff');
		$room_experience = $this->input->get_post('room_experience');
		$bath_room_issue = $this->input->get_post('bath_room_issue');
		$breakfast_experience = $this->input->get_post('breakfast_experience');
		$recommend = $this->input->get_post('recommend');
		$stay_again = $this->input->get_post('stay_again');
		$location_and_transport = $this->input->get_post('location_and_transport');
		$how_do_better = $this->input->get_post('how_do_better');
		$name = $this->input->get_post('name');
		$email = $this->input->get_post('email');
		
		$focus_element = '';
		$Error = 0;
		$negative_feedback = false; // set false by default
		
		if($overall_experience == 0)
		{
			$Error=1;
			$Msg = '<div class="alert alert-error">Please rate this question.</div>';
			$focus_element = 'overall_experience'.'_error_div';
		}
		elseif($checkin_experience == 0)
		{
			$Error=1;
			$Msg = '<div class="alert alert-error">Please rate this question.</div>';
			$focus_element = 'checkin_experience'.'_error_div';
		}
		elseif($friendliness_of_staff == 0)
		{
			$Error=1;
			$Msg = '<div class="alert alert-error">Please rate this question.</div>';
			$focus_element = 'friendliness_of_staff'.'_error_div';
		}
		elseif($room_experience == 0)
		{
			$Error=1;
			$Msg = '<div class="alert alert-error">Please rate this question.</div>';
			$focus_element = 'room_experience'.'_error_div';
		}
		elseif($breakfast_experience == 0)
		{
			$Error=1;
			$Msg = '<div class="alert alert-error">Please rate this question.</div>';
			$focus_element = 'breakfast_experience'.'_error_div';
		}
		elseif($location_and_transport == 0)
		{
			$Error=1;
			$Msg = '<div class="alert alert-error">Please rate this question.</div>';
			$focus_element = 'location_and_transport'.'_error_div';
		}
		
		if($Error == 0)
		{
			if($overall_experience < 3 
			or $checkin_experience < 3 
			or $friendliness_of_staff < 3 
			or $room_experience < 3
			or $breakfast_experience < 3 
			or $location_and_transport < 3 
			or $stay_again == 'No'
			or $recommend == 'No') // if true, its mean negative feedback provided by customer
			{
				$negative_feedback = true;
			}
			
			$insert_data = array();
			$insert_data['restaurant_id'] = $restaurant_id;
			$insert_data['overall_experience'] = $overall_experience;
			$insert_data['sort_of_trip'] = ucfirst($sort_of_trip);
			$insert_data['booking_reference'] = ucfirst($booking_reference);
			$insert_data['checkin_experience'] = $checkin_experience;
			$insert_data['friendliness_of_staff'] = $friendliness_of_staff;
			$insert_data['room_experience'] = $room_experience;
			$insert_data['bath_room_issue'] = $bath_room_issue;
			$insert_data['breakfast_experience'] = $breakfast_experience;
			$insert_data['recommend'] = ucfirst($recommend);
			$insert_data['stay_again'] = ucfirst($stay_again);
			$insert_data['location_and_transport'] = $location_and_transport;
			$insert_data['how_do_better'] = ucfirst($how_do_better);
			$insert_data['name'] = $name;
			$insert_data['email'] = $email;
			
			$rating_id = $this->order_model->add_rating($insert_data);
			
			if($negative_feedback) // if Negative Feedback
			{
				// Send negative feedback report to Restaurant Owner $_SESSION[USER_LOGIN]['Email']
				$to = $_SESSION[USER_LOGIN]['email'];
				$Subject = 'Negative Feedback Alert';
				ob_start();
				
				$this->data['rating_id'] = $rating_id;
				$this->load->view('email-feedback',$this->data);
				
				$message = ob_get_contents();
				ob_end_clean();
				
				# Send email to Signup User
				$this->email->clear(TRUE);
				$this->email->set_mailtype("html");
				$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
				$this->email->to($to);
				$this->email->subject($Subject);
				$this->email->message(get_email_message_with_wrapper($message));
				$this->email->send();
			}
			
			
			$_SESSION[USER_RETURN_MSG]['Msg']='Reviews saved successfully.';
			
		}
		
		$Return["Error"]		= $Error;
		$Return["Msg"]			= @$Msg;
		$Return['focus_element'] = $focus_element;
		echo json_encode($Return);
	}
	
	public function delete_selected_orders()
	{
		$selected_ids = $this->input->get_post('selected_ids');
		if( is_array($selected_ids) and count($selected_ids) )
		{
			foreach($selected_ids as $order_id)
			{
				if($this->input->get_post('hard_delete') == 1)
				{
					$this->order_model->delete_order($order_id);
				}
				else
				{
					$this->order_model->update_order($order_id, array('deleted' => 1) );
				}
			}
			$_SESSION['msg_error'][] = count($selected_ids)." Record deleted...";
		}
		else
		{
			$_SESSION['msg_error'][] = "Please select some checkboxes";
		}
		
		exit;
	}
}




