<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller 
{
	var $data;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('login_model');
		$this->load->model('restaurant_model');
		$this->load->model('order_model');
		$this->load->model('report_model');
		
		//load our new PHPExcel library
		$this->load->library('excel');
		
		# if user is not logged in, then redirect him to login page
		if(! isset($_SESSION[USER_LOGIN]['id']) )
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$join_date = $_SESSION[USER_LOGIN]['date_created'];
		$graph_interval = $this->input->get_post('graph_interval') != '' ? $this->input->get_post('graph_interval') : 'Daily';
		
		$start_date = $this->input->get_post('start_date') != '' ? $this->input->get_post('start_date') : $join_date;
		$end_date = $this->input->get_post('end_date') != '' ? $this->input->get_post('end_date') : date('Y-m-d');
		
		$this->data['start_date'] = date('Y-m-d',strtotime($start_date));
		$this->data['end_date'] = date('Y-m-d',strtotime($end_date));
		$this->data['graph_interval'] = $graph_interval;
		
		# Count Feedbacks
		$count_feedbacks = $this->report_model->count_feedbacks($restaurant_id,$start_date,$end_date);
		$this->data['count_feedbacks'] = $count_feedbacks;
		
		$this->data['Active'] = 'Reports';
		$this->load->view('report',$this->data);
	}
	
	public function review_report()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$join_date = $_SESSION[USER_LOGIN]['date_created'];
		
		$start_date = $this->input->get_post('start_date') != '' ? $this->input->get_post('start_date') : $join_date;
		$end_date = $this->input->get_post('end_date') != '' ? $this->input->get_post('end_date') : date('Y-m-d');
		
		$this->data['start_date'] = date('Y-m-d',strtotime($start_date));
		$this->data['end_date'] = date('Y-m-d',strtotime($end_date));
		
		# Get rating
		$where_condition['restaurant_id'] = $restaurant_id;
		$where_condition['DATE(date_created) BETWEEN '] = array("DATE('$start_date') AND DATE('$end_date')",FALSE); // If you set it to FALSE, CodeIgniter will not try to protect your field or table names.
		$order_by['id'] = 'DESC';
		$this->data['rating_query'] = $this->report_model->get_ratings($where_condition, $order_by);
		
		$this->data['Active'] = 'Review';
		$this->load->view('review-report',$this->data);
	}
	
	public function change_password()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$AccountPassword = $this->input->get_post('AccountPassword');
		$ReportPassword = $this->input->get_post('ReportPassword');
		$change_password = $this->input->get_post('change_password');
		
		if($_POST)
		{
			if($change_password == 'account')
			{
				$this->login_model->update_user($restaurant_id,array('password' => md5($AccountPassword)));
				$_SESSION[USER_LOGIN]['password']=md5($AccountPassword);
				$Return['Error']=0;
				$Return['Msg']='<div class="alert alert-success">Account password changed successfully!</div>';
				echo json_encode($Return);
			}
			if($change_password == 'report')
			{
				$this->login_model->update_user($restaurant_id,array('report_password' => md5($ReportPassword)));
				$_SESSION[USER_LOGIN]['report_password']=md5($ReportPassword);
				$Return['Error']=0;
				$Return['Msg']='<div class="alert alert-success">Report password changed successfully!</div>';
				echo json_encode($Return);
			}
			exit;
		}
		$this->data['Active'] = 'Password';
		$this->load->view('change-password',$this->data);
	}
	
	public function download_report()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$join_date = $_SESSION[USER_LOGIN]['date_created'];
		
		$start_date = $this->input->get_post('start_date') != '' ? $this->input->get_post('start_date') : date('Y-m-d',strtotime($join_date));
		$end_date = $this->input->get_post('end_date') != '' ? $this->input->get_post('end_date') : date('Y-m-d');
		
		$this->data['start_date'] = date('Y-m-d',strtotime($start_date));
		$this->data['end_date'] = date('Y-m-d',strtotime($end_date));
		
		// Set the validation rules
		$this->form_validation->set_rules('start_date', 'Start Date', 'required|trim|min_length[10]|max_length[10]');
		$this->form_validation->set_rules('end_date', 'End Date', 'required|trim|min_length[10]|max_length[10]');
		
		// If the validation worked
		if ($this->form_validation->run())
		{
			$report_data = $this->report_model->generate_report($restaurant_id,$start_date,$end_date);
			//my_var_dump($report_data);
			if($report_data === false)
			{
				$_SESSION['msg_error'][] = "Not enough data for report generation.";
			}
			else
			{
				self::export_report_to_excel_file($report_data);
			}
		}
		
		$this->data['Active'] = 'Download';
		$this->load->view('download-report',$this->data);
	}
	
	public function auth()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$Password = $this->input->get_post('Password');
		if($_POST)
		{
			$sql = "SELECT * FROM restaurants WHERE id=$restaurant_id";
			if($_SESSION[USER_LOGIN]['report_password']=='')
			{
				$sql .= " AND password='".md5($Password)."'";
				$_SESSION[USER_RETURN_MSG]['Error']	= 1;
				$_SESSION[USER_RETURN_MSG]['Msg']	= 'Please create a separate report section password.';
			}
			else
				$sql .= " AND report_password='".md5($Password)."'";
			
			$query = $this->db->query($sql);
			if($query->num_rows())
			{
				$Error=0;
				$Msg='Loading Report. Please wait...';
				$ReturnPage = base_url().'report/';
			}
			else
			{
				$Error=1;
				$Msg='Wrong password. Please try again.';
				$ReturnPage = base_url().'report/auth';
			}
			$Return["Error"]= $Error;
			$Return["Msg"]	= $Msg;
			$Return['Url']	= $ReturnPage;
			echo json_encode($Return);
			exit;
		}
		$this->data['Active'] = 'report-auth';
		$this->load->view('report_auth',$this->data);
	}
	
	public function load_rank()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$business_type = $_SESSION[USER_LOGIN]['business_type'];
		
		$count_restaurant_by_business_type = $this->report_model->count_restaurant_by_business_type($business_type);
		
		$YourRank = $this->report_model->get_rank($restaurant_id,$business_type);
		
		echo "Your ranking among other $business_type competitors in Dubai is <strong>$YourRank</strong>/<span>$count_restaurant_by_business_type</span>";
	}
	
	public function column_rating_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$column = $this->input->get_post('column');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['column'] = $column;
		
		$graph_query = $this->report_model->get_column_graph_data($restaurant_id,$start_date,$end_date,$column);
		$this->data['graph_query'] = $graph_query;
		$this->load->view('report-column-rating',$this->data);
	}
	
	public function column_star_rating_graph()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$column = $this->input->get_post('column');
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->data['restaurant_id'] = $restaurant_id;
		$this->data['start_date'] = $start_date;
		$this->data['end_date'] = $end_date;
		$this->data['column'] = $column;
		
		$graph_data = $this->report_model->get_column_star_rating_graph_data($restaurant_id,$start_date,$end_date,$column);
		$this->data['graph_data'] = $graph_data;
		$this->load->view('report-column-star-rating',$this->data);
	}
	
	public function export_email_addresses()
	{
		$restaurant_id = $_SESSION[USER_LOGIN]['id'];
		$start_date = $this->input->get_post('start_date');
		$end_date = $this->input->get_post('end_date');
		
		$this->db->select('email,date_created');
		$this->db->where('restaurant_id',$restaurant_id);
		$this->db->where('email !=','');
		$this->db->where('DATE(date_created) BETWEEN ',"DATE('$start_date') AND DATE('$end_date')",false);
		
		$this->db->order_by('date_created','DESC');
		
		$query = $this->db->get('ratings');
		
		$this->load->dbutil();
		
		$csv_data = $this->dbutil->csv_from_result($query);
		
		// Load the file helper and write the file to your server
		$this->load->helper('file');
		$filename = 'email-addresses-'.$restaurant_id.'.csv';
		$filepath = './'.UPLOADS.'/'.$filename;
		write_file($filepath, $csv_data); 
		
		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		$file_contents = file_get_contents($filepath);
		force_download($filename,$file_contents);
	}
	
	public function export_report_to_excel_file($data)
	{
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle("Pref Report");
		
		# Center align
		$this->excel->getActiveSheet()->getStyle("C2:L11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle("G2:L40")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$this->excel->getActiveSheet()->setCellValue("A1", $data["report_heading"]);
		//change the font size
		$this->excel->getActiveSheet()->getStyle("A1")->getFont()->setSize(20);
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
		//merge cell A1 until L1
		$this->excel->getActiveSheet()->mergeCells("A1:L1");
		//set aligment to center for that merged cell (A1 to D1)
		$this->excel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//$this->excel->getActiveSheet()->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB("E8E5E5");
		
		$row = 3;
		$this->excel->getActiveSheet()->setCellValue("B$row", $data["total_reviews"][0]);
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setSize(16);
		$this->excel->getActiveSheet()->setCellValue("C$row", $data["total_reviews"][1]);
		$this->excel->getActiveSheet()->getStyle("C$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->getStyle("C$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$row = 5;
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data["question_header"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data["question_header"][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data["question_header"][2]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data["question_header"][3]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data["question_header"][4]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data["question_header"][5]);
		$this->excel->getActiveSheet()->getStyle("B$row:L$row")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("B$row:L$row")->getFont()->setSize(16);
		
		$row = 6;
		$column = 'overall_experience';
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data[$column."_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data[$column."_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data[$column."_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data[$column."_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data[$column."_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data[$column."_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data[$column."_data"][3][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data[$column."_data"][4][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data[$column."_data"][4][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data[$column."_data"][5][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data[$column."_data"][5][1]);
		
		$row = 7;
		$column = 'checkin_experience';
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data[$column."_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data[$column."_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data[$column."_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data[$column."_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data[$column."_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data[$column."_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data[$column."_data"][3][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data[$column."_data"][4][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data[$column."_data"][4][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data[$column."_data"][5][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data[$column."_data"][5][1]);
		
		$row = 8;
		$column = 'friendliness_of_staff';
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data[$column."_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data[$column."_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data[$column."_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data[$column."_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data[$column."_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data[$column."_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data[$column."_data"][3][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data[$column."_data"][4][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data[$column."_data"][4][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data[$column."_data"][5][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data[$column."_data"][5][1]);
		
		$row = 9;
		$column = 'room_experience';
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data[$column."_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data[$column."_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data[$column."_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data[$column."_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data[$column."_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data[$column."_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data[$column."_data"][3][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data[$column."_data"][4][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data[$column."_data"][4][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data[$column."_data"][5][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data[$column."_data"][5][1]);
		
		$row = 10;
		$column = 'breakfast_experience';
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data[$column."_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data[$column."_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data[$column."_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data[$column."_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data[$column."_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data[$column."_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data[$column."_data"][3][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data[$column."_data"][4][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data[$column."_data"][4][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data[$column."_data"][5][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data[$column."_data"][5][1]);
		
		$row = 11;
		$column = 'location_and_transport';
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data[$column."_data"][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data[$column."_data"][1][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data[$column."_data"][1][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data[$column."_data"][2][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data[$column."_data"][2][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data[$column."_data"][3][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data[$column."_data"][3][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data[$column."_data"][4][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data[$column."_data"][4][1]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data[$column."_data"][5][0]);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data[$column."_data"][5][1]);
		$row++;
		
		$row++;
		$column = 'sort_of_trip';
		$query = $data[$column.'_query'];
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, 'Type of Trip');
		foreach($query->result() as $drow)
		{
			$row++;
			$this->excel->getActiveSheet()->mergeCells("C$row:F$row");
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $drow->$column);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $drow->number_of_customers);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, number_format($drow->percentage,0).'%');
		}
		$row++;
		
		$row++;
		$column = 'booking_reference';
		$query = $data[$column.'_query'];
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, 'Booking Reference');
		foreach($query->result() as $drow)
		{
			$row++;
			$this->excel->getActiveSheet()->mergeCells("C$row:F$row");
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $drow->$column);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $drow->number_of_customers);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, number_format($drow->percentage,0).'%');
		}
		$row++;
		
		$row++;
		$column = 'recommend';
		$query = $data[$column.'_query'];
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, 'Will you recommend?');
		foreach($query->result() as $drow)
		{
			$row++;
			$this->excel->getActiveSheet()->mergeCells("C$row:F$row");
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $drow->$column);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $drow->number_of_customers);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, number_format($drow->percentage,0).'%');
		}
		$row++;
		
		$row++;
		$column = 'stay_again';
		$query = $data[$column.'_query'];
		$this->excel->getActiveSheet()->getStyle("B$row")->getFont()->setBold(true)->setSize(16);
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, 'Would you stay again?');
		foreach($query->result() as $drow)
		{
			$row++;
			$this->excel->getActiveSheet()->mergeCells("C$row:F$row");
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $drow->$column);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $drow->number_of_customers);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, number_format($drow->percentage,0).'%');
		}
		$row++;
		
		
		require_once APPPATH."/third_party/PHPExcel/Worksheet/Drawing.php";
				
		$this->excel->getActiveSheet()->setShowGridlines(false);
		$this->excel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
		
		$styleArray = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  )
		  );
		
		$this->excel->getActiveSheet()->getStyle(
			'A1:' . 
			$this->excel->getActiveSheet()->getHighestColumn() . 
			$this->excel->getActiveSheet()->getHighestRow()
		)->applyFromArray($styleArray);
		
		$filename="pref_report.xls"; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}
}











