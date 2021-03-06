<?php
if (!defined('BASEPATH'))  exit('No direct script access allowed');


class Register extends MY_Controller {
	 /**
     * Constructor function
     */
    public function __construct() {
        parent::__construct();
        // $this->load->library('upload');
        // $this->load->helpers('uploadfiles');

    }

	public function index() {

		$op = $this->input->get('op');
		$id = $this->input->get('id');
		$token = $this->input->get('token');
		// var_dump($token);exit;

		if (!empty($op) && !empty($id) &&  !empty($token)) {

			$result = doCurl(API_BASE_LINK.'register/findReUserName?op='.$op."&id=".$id."&token=".$token);
			if ($result && $result['http_status_code'] == 200) {

				$content   = json_decode($result['output']);
				$status_code  = $content->status_code;

				$get_op  = $content->op;

				if ($status_code == 200 && ($get_op == 'active')) {
					$find_re_user_by_token          = $content->find_re_user_by_token;
					$data['id'] 					= $find_re_user_by_token->id;
					$data['user_name']  			= $find_re_user_by_token->user_name;
					$data['created_by_admin_id']  	= $find_re_user_by_token->created_by_admin_id;

					$this->load->view('register_view' , isset($data) ? $data : NULL);

				} else if ( $status_code == 200 && ($get_op = 'resetpwd') ){

						$find_re_user_by_token 	= $content->find_re_user_by_token;
						$find_user_name 				= $content->find_user_name;
						$data['user_name'] = $find_user_name->email;
						$data['nick'] = $find_user_name->nick;

						$data['id']  = $find_re_user_by_token->re_user_id;
						$data['re_user_id']  = $find_re_user_by_token->re_user_id;
						$data['get_op'] 		=	$get_op;	  

						$this->load->view('register_view' , isset($data) ? $data : NULL);
				}else{

					exit('无效链接！');
				}
				
			}
			

		}else {
			show_404();exit();
		}	

	}

	public function improveInformation()
	{
		// $fileInfo = $_FILES['uploadphoto'];		
		// $uploadPath = "/var/www/html/church/church_user/public/uploads/userHeadsrc";
		// $params['userHeadSrc']	= uploadFiles( $fileInfo,$uploadPath)['newName'];		
		$params['sex'] 	  		 = $this->input->post('sex');
		$params['group_id'] 	 = $this->input->post('group_id');
		$params['user_id'] 		 = $this->session->userdata('user_id');
		$url = API_BASE_LINK.'register/improveInformation';
		$result = doCurl($url, $params, 'POST');
		redirect('login','refresh');


	}

	public function sbumit_register()
	{
		$temp_post = $this->input->post();
		if (!empty($temp_post)) {

			$params['re_user_id']           = $this->input->post('re_user_id'); 
			$params['created_by_admin_id'] 	= $this->input->post('created_by_admin_id'); 
			$params['user_name']  			= $this->input->post('user_name');
			$temp_pwd2 = $this->input->post('pwd2');
			$temp_pwd2 = $temp_pwd2 ? $temp_pwd2 : "";
			$params['password'] 	        = md5(md5($temp_pwd2)); 
			$params['nick'] 	  	        = $this->input->post('nick'); 
			$get_op 	  	                = $this->input->post('get_op'); 

			if (!empty($get_op)) {

				$url = API_BASE_LINK.'register/resetpwd_for_forgetpwd';
				$result = doCurl($url, $params, 'POST');
				if ($result && $result['http_status_code']== 200) {

					$content     	= json_decode($result['output']);
					$status_code 	= $content->status_code;

					if ($status_code == 200 ) {
						redirect('login','refresh');
					}else{
						exit('提交失败,请重试！');
					}
					
				}else {
					show_404();exit();
				}

			}else {

				$url = API_BASE_LINK.'register/register';
				$result = doCurl($url, $params, 'POST');

				if ($result && $result['http_status_code'] == 200) {

					$content     				= json_decode($result['output']);
					$status_code 				= $content->status_code;
					$message     				= $content->message;
					$user_id     				= $content->user_id;
					$get_all_group_name     	= $content->get_all_group_name;
					
					if ($status_code == 200 ) {

						$data['nick'] = $params['nick'];
						$data['groupName'] = $get_all_group_name;

						$this->session->set_userdata('user_id', $user_id);
						$this->session->set_flashdata('success', $message);
						$this->load->view('improveInformation_view' ,  isset($data) ? $data : NULL);

					}else{

						$this->session->set_flashdata('error', $message);
						redirect('register','refresh');
					}

				}else{
					show_404();exit;
				}
				
			}
				
		} else {
			show_404();exit();
		}

	}
}
