<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Personal extends MY_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->helpers('uploadFiles');

    }

	public function index()
	{
		if (!$this->session->userdata('access_token')) {
			
			redirect('login','refresh');
		}else {

			$user_id         = $this->session->userdata('user_id');
			$spirituality_id = $this->input->get('spirituality_id');		

			$result = doCurl(API_BASE_LINK.'personal/get_informations?user_id='.$user_id.'&spirituality_id='.$spirituality_id);
			
			$data =  $this->tq_header_info();
			
			if ($result && $result['http_status_code'] == 200) {
				$content     = json_decode($result['output']);
				$status_code = $content->status_code;
				if($status_code == 200){
					$data['informations'] = $content->results;				
				}	
			}else{
				show_404();exit;
			}
			
			$this->load->view('personal/personal_view' , isset($data) ? $data : "");
		}
	}

	public function setPersonalData()
	{	
		if (!$this->session->userdata('access_token')) {
			
			redirect('login','refresh');
		}else {

			$data =  $this->tq_header_info();

			$this->load->view('personal/setPersonalData_view' , isset($data) ? $data : "");
		}	

	}

	// public function upload_photo()
// 	{
// 		if (!$this->session->userdata('access_token')) {
			
// 			redirect('login','refresh');
// 		}else {

// 			$data =  $this->tq_header_info();
// 			$userHeadSrc = $this->input->post('userHeadSrc') ;
// 			if ( !empty($userHeadSrc) &&  $userHeadSrc == $data['userHeadSrc_info'] ) {
// 				$params['userHeadSrc'] = $userHeadSrc; 
// 			}else {
// 				$fileInfo = $_FILES['uploadphoto'];
// 				$uploadPath = "/var/www/html/church/church_user/public/uploads/userHeadsrc";
// 				$msg_return = uploadFiles($fileInfo,$uploadPath);
// 				// var_dump($msg_return);exit;
// 				if (isset($msg_return['msg']) ) {
// 					$this->session->set_flashdata('error', $msg_return['msg']);
// 					redirect('setPersonalData','refresh');	
// 				}else{
// 					$params['userHeadSrc']	= $msg_return['newName'];
// 				}	
// 				if(!empty($data['userHeadSrc_info'])){
// 					$file = '/var/www/html/church/church_user/public/uploads/userHeadsrc/'.$data['userHeadSrc_info'];
// 					if(file_exists($file)){					
// 						!unlink($file);
// 					}				 
// 				}
// 			}

// 			$params['user_nick'] 	= $this->input->post('user_nick');
// 			$params['sex'] 			= $this->input->post('sex');
// 			$params['group_id'] 	= $this->input->post('group_id');
// 			$params['user_id'] 		= $this->session->userdata('user_id');
// //			 var_dump($params);exit;
// 			$url = API_BASE_LINK.'personal/upload_photo';
// 			$result = doCurl($url, $params, 'POST');			
// //			 var_dump($result);exit();

// 			if ($result && $result['http_status_code'] == 200) {

// 					$result = json_decode($result['output']);
// 					$content = $result->results;
// 					if ($content) {

// 						$affected_id 			= 	$content->affected_id;
// 						$userHead_src_id 	= 	$content->userHead_src_id;

// 						if (isset($affected_id) && $userHead_src_id) {
// 							$data['success'] =  '资料修改成功！';
// 						}

// 					}else{

// 						$this->session->set_flashdata('error', '资料修改失败！');
// 					}
// 						$data =  $this->tq_header_info();	
// 						$this->load->view('personal/setPersonalData_view' , isset($data) ? $data : "");


// 			} else {
// 				show_404();exit;
// 			}  

			
// 		}	
		
// 	}

	public function replace_headSrc()
	{
		if (!$this->session->userdata('access_token')) {
			
			redirect('login','refresh');
		}else {
			$data =  $this->tq_header_info();

			$this->load->view('personal/replace_headSrc_view' , isset($data) ? $data : "");
		}
	}

	public function upload_headSrc()
	{	
		$stream = '';
		$streamFilename = '';
		$stream = $this->input->post("userHeadSrc");
		$oldUserHeadSrc = $this->input->post("oldUserHeadSrc");

		if(!empty($stream)){
			header('Content-type:text/html;charset=utf-8');			
			$base64_image_content = $stream; 			
			if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $pregR)){			

				$streamFileType ='.' .$pregR[2]; 
				$streamFileRand = date('YmdHis').rand(1000,9999);
				$streamFilename = $streamFileRand .$streamFileType;
				$uploadPath = "/var/www/html/church/church_user/public/uploads/userHeadsrc/";


				$new_file = $uploadPath.$streamFilename;

				if (file_put_contents($new_file, base64_decode(str_replace($pregR[1], '', $base64_image_content)))){
					if(!empty($oldUserHeadSrc)){
						$file = $uploadPath.$oldUserHeadSrc;
						if(file_exists($file)){					
							!unlink($file);
						}						
					}

					$params['userHeadSrc'] = $streamFilename;
					$params['user_id'] 		= $this->session->userdata('user_id');
					$url = API_BASE_LINK.'personal/upload_headSrc';
					$result = doCurl($url, $params, 'POST');		

					if ($result && $result['http_status_code'] == 200) {

						$content = json_decode($result['output']);
						$status_code = $content->status_code;
						if ($status_code == 200) {
							redirect('personal/setPersonalData','refresh');
						}						

					} else {
						show_404();exit;
					} 		

				}		   			 
			}							
		}

		redirect('personal/replace_headSrc','refresh');

	}

	public function modify_user_data()
	{
		$params['user_nick'] 	= $this->input->post('user_nick');
		$params['sex'] 			= $this->input->post('sex');
		$params['group_id'] 	= $this->input->post('group_id');
		$params['user_id']      = $this->session->userdata('user_id');
		$url = API_BASE_LINK.'personal/modify_user_data';
		$result = doCurl($url, $params, 'POST');			
		// var_dump($result);exit;
		if ($result && $result['http_status_code'] == 200) {

				$content = json_decode($result['output']);
				$status_code = $content->status_code;
				// var_dump($status_code);exit;
				if ($status_code == 200) {

					$this->session->set_flashdata('success', '资料修改成功！');					

				}else{

					$this->session->set_flashdata('error', '资料修改失败！');
				}					

		} else {
			show_404();exit;
		} 

		redirect('personal/setPersonalData','refresh');
	}

	// update  12-17

	public function get_honor_list()
	{
		if (!$this->session->userdata('access_token')) {
			
			redirect('login','refresh');
		}else {

			$type = $this->input->get('listType');			
			$data =  $this->tq_header_info();

			$get_honor_list = doCurl(API_BASE_LINK.'personal/get_honor_list');

			if ($get_honor_list && $get_honor_list['http_status_code'] ==200) {
				$content = json_decode($get_honor_list['output']);
				$status_code	 = $content->status_code;
				if ($status_code == 200) {
					$user_info_count_spirit_results = $content->results->user_info_count_spirit_results;
					$user_info_count_prayer_results = $content->results->user_info_count_prayer_results;
				}
			}			
			if ($type == 'prayer') {			
				$data['user_info_count_prayer_results'] = $user_info_count_prayer_results;
				$this->load->view('personal/get_honor_prayer_list_view' , isset($data) ? $data : "");				
			}else{

				$data['user_info_count_spirit_results']  =  $user_info_count_spirit_results;
				$this->load->view('personal/get_honor_list_view' , isset($data) ? $data : "");
			}

		}	
	}


}

		