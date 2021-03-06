<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

function index()
	{

		$this->load->helper('cookie');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

		$this->form_validation->set_rules('firstname', 'First Name', 'prep_for_form|required');
		$this->form_validation->set_rules('lastname', 'Last Name', 'prep_for_form|required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email_address');
		$this->form_validation->set_rules('password1', 'Password', 'required|md5');
		$this->form_validation->set_rules('password2', 'Password2', 'required|matches[password1]|md5');
		$this->form_validation->set_rules('enddate', 'End Date', 'required|callback_check_end_date');
		$this->form_validation->set_rules('color', 'Color', 'required|callback_color_check');
		$this->form_validation->set_message('matches', 'The Password fields do not match.');


		if ($this->form_validation->run() == FALSE)
		{
			$data['body_class'] = 'registration-page';
			$data['page_title'] = "Grow for the Cure : Register / Log In";
			$data['page_description'] = "Log in to your grower account here, or create a new one.";

			$this->load->view('header', $data);
			$this->load->view('register', $data);
			$this->load->view('footer', $data);
		}
		else
		{

            $firstname = $this->input->post('firstname');
            $lastname = $this->input->post('lastname');
            $email = $this->input->post('email');
            $password1 = $this->input->post('password1');
            $password2 = $this->input->post('password2');
            $enddate = $this->input->post('enddate');

            $secretcode = $this->input->post('secretcode');
            $teamname = $this->input->post('teamname');


            if (!$secretcode) {
            	if (!$teamname) {
					$team = $lastname;
            	} else {
					$team = $teamname;
            	}
            }


            if($secretcode) {

       			$this->load->model('model_users');
				$data['teamcheck'] = $this->model_users->get_team_from_code($secretcode);
			
				foreach ($data['teamcheck'] as $teamcheck) {
					$data['teamcheck'] = $teamcheck->teamName;
				}

				$team = $teamcheck->teamName;
				$teamID = $teamcheck->teamID;

			}


      //      print $firstname . '<br/>' . $lastname . '<br/>' . $email. '<br/>' . $password1 . '<br/>' . $password2 . '<br />' . $enddate . '<br />Team: ' . $team;


			$this->db->set('firstName', $firstname);
			$this->db->set('lastName', $lastname);
			$this->db->set('emailAddress', $email);
			$this->db->set('password', $password1);
			$this->db->set('dateJoined', date("Y-m-d"));
			$this->db->insert('tblUsers');


			$this->db->where('emailAddress', $email);
			$query = $this->db->get('tblUsers');



			if ($query->num_rows() > 0) {

				foreach ($query->result_array() as $row) {
					$userID = $row['userID'];
				}
			}

			$this->db->set('pledgeAmount', '0');
			$this->db->set('userID', $userID);
			$this->db->set('pledgeTime', Date('Y-m-d'));
			$this->db->set('pledger', 'INITIAL PLEDGE');
			$this->db->insert('tblPledges');



			if ($secretcode) {
				// TEAM ALREADY EXISTS
				$this->db->set('growerID', $userID);
				$this->db->set('teamID', $teamID);
				$this->db->insert('tblTeamMembers');
			} else {
				// NEED TO ADD TEAM
				$this->db->set('teamName', $team);
				$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
				$randomString2 = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
				$this->db->set('teamCode', time() . '-' . $randomString . '-' . $randomString2);
				$this->db->insert('tblTeams');

				$this->db->where('teamName', $team);
				$query = $this->db->get('tblTeams');

				if ($query->num_rows() > 0) {

					foreach ($query->result_array() as $row) {
						$teamID = $row['teamID'];
					}
				}

				$this->db->set('growerID', $userID);
				$this->db->set('teamID', $teamID);
				$this->db->insert('tblTeamMembers');
			}
			
			$newDate = new DateTime($enddate);
			$dateReady = $newDate->format('Y-m-d');
//			echo $dateReady;

			$this->db->set('growerID', $userID);
			$this->db->set('teamID', $teamID);
			$this->db->set('startDate', date("Y-m-d"));
			$this->db->set('endDate', $dateReady);
			$this->db->set('current', 1);
			$this->db->insert('tblCampaigns');

			$fullName = strtolower($firstname) . '-' . strtolower($lastname);


			$registration_email='<body style="background-color:#e0e0e0;margin:0;padding:0;">

<div align="center" style="background-color:white;">
	<br />
	<img src="' . base_url() . 'artwork/email_artwork/grow_header.gif" />
	<br />
</div>

<table width="600px" align="center">
	<tr>
		<td style="padding:10px;font-family:helvetica,arial,sans-serif;color:black;font-size:14px;line-height:140%;">
			<p style="font-size:16px;">Hello, [FIRSTNAME].</p>
			<p>This email is being sent to confirm your registration as a Grower on the Grow for the Cure website. Thank you for being a part of the fight against Lung Cancer. Any bit of money raised helps the cause.</p>
			<p>You can <a href="[PROFILELINK]">click here to be taken right to your profile editing page.</a></p>
			<p>Again, thank you from your friends at <a href="http://growforthecure.org">Grow for the Cure.</a></p>
			<p style="font-size:12px;">All net proceeds will be used by the Bonnie J. Addario Lung Cancer Foundation on the front lines of lung cancer research. BJALCF&apos;s Tax ID#: 20-4417327</p>
		</td>
	</tr>
</table>
	<br />
	<br />
	<br />
	<br />
</body>';

		$registration_email = str_replace('[FIRSTNAME]', $firstname, $registration_email);
		$registration_email = str_replace('[PROFILELINK]', base_url() . 'profile/' . $fullName . '/' . $userID, $registration_email);

		$message = '<html><head></head>';
		$message = $message . $registration_email;
		$message = $message . '</html>';

		$this->load->library('email');

		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'smtp.mandrillapp.com';
		$config['smtp_user'] = 'stephen@stephencollins.me';
		$config['smtp_pass'] = 'IrLx5aS2RPPLc_FcG6cWkQ';
		$config['smtp_port'] = '587';
		$config['charset'] = 'iso-8859-1';
		$config['mailtype'] = 'html';

		$this->email->initialize($config);

		$this->email->from('do_not_reply@growforthecure.org', 'Grow for the Cure');
		$this->email->to($email); 
		$this->email->subject('Thank you for registering and helping the cause.');
		$this->email->message($message);	
		$this->email->send();




			setcookie('userid', $userID, time()+60*60*24*30, '/');
			setcookie('fullname', $fullName, time()+60*60*24*30, '/');

			redirect('/profile/'. $fullName . '/' . $userID, 'refresh');
		}
	}

	public function check_end_date($d)
	{		

			$ts1 = strtotime($d);
			$ts2 = strtotime(date("Y-m-d H:i:s"));

			$seconds_diff = $ts1 - $ts2;

			$time_difference = floor($seconds_diff/3600/24);
			
			if ($time_difference > 6) {
				return TRUE;
			} else {
				$this->form_validation->set_message('check_end_date', 'You must select a date at least 7 days in the future.');
				return FALSE;	
			}
	}

	public function check_email_address($e)
	{
		$this->db->where('emailAddress', $e);
		$query = $this->db->get('tblUsers');
		
		if ($query->num_rows() > 0) {		
			$this->form_validation->set_message('check_email_address', 'There is already an account with the address of %s');
			return FALSE;	
		} else {
			return TRUE;	
		}
	}

	public function color_check($b)
	{
		if ($b == 'yellow') {
			return TRUE;
		} else {
			$this->form_validation->set_message('color_check', 'Sorry, but ' . $b . ' is not the color of a banana.');
			return FALSE;
		}
	}

	public function forgot()
	{
			$this->load->helper('form');

			$data['reason'] = $this->uri->segment(3);


			$data['body_class'] = 'forgot-page';
			$data['page_title'] = "Grow for the Cure : Forgot your password";
			$data['page_description'] = "Use this page to set a new password for your account.";

			$this->load->view('header', $data);
			$this->load->view('forgot', $data);
			$this->load->view('footer', $data);

	}

	public function send_email()
	{
		$email = $_POST['email'];

		if (!$email) {
			redirect('/register/forgot/no_email', 'refresh');
		}
		
		$this->load->model('model_users');
		$usercheck = $this->model_users->get_user_by_email($email);
		//echo $data['usercheck'];

		if ($usercheck == 0) {
			redirect('/register/forgot/no_user', 'refresh');
		} elseif ($usercheck != 0) {

			foreach ($usercheck as $user) {
				$userID = $user->userID;
				$code = $user->password;
			}

			$message = 'To reset the password for your Grow for the Cure account click on this link -> '.
			base_url() .'password/update/'. $userID . '/' . $code;

			$this->load->library('email');

			$config['protocol'] = 'smtp';
			$config['smtp_host'] = 'smtp.mandrillapp.com';
			$config['smtp_user'] = 'stephen@stephencollins.me';
			$config['smtp_pass'] = 'IrLx5aS2RPPLc_FcG6cWkQ';
			$config['smtp_port'] = '587';
			$config['charset'] = 'iso-8859-1';
			$config['mailtype'] = 'html';

			$this->email->initialize($config);

			$this->email->from('do_not_reply@growforthecure.org', 'Grow for the Cure');
			$this->email->to($email); 
			$this->email->subject('Password reset request.');
			$this->email->message($message);	
			$this->email->send();

			redirect('/register/forgot/good_user', 'refresh');
		}


	}






}

/* End of file register.php */
/* Location: ./application/controllers/register.php */

