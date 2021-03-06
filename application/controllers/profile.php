<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller {

	function index()
	{

		$user_to_get = $this->uri->segment(3);

		if (isset($_COOKIE["userid"])) {
			if ($user_to_get != $_COOKIE['userid']) {
				redirect('/profile/'.$_COOKIE['fullname'] .'/' . $_COOKIE['userid'] . '?sorry=1', 'refresh');
			}
		}

		if (isset($_GET["sorry"])) {
			$data['sorry'] = 'Sorry, you can only access your own grower profile. You have been redireceted to your logged in profile.';
		}

		$data['page_title'] = "Grow for the Cure : Edit your Profile";
		$data['page_description'] = "Profile Editor";
		$data['body_class'] = "profile-page";

		$this->load->helper('form');


		$this->load->model('model_users');
		$this->load->model('model_page_data');

		$data['styles'] = $this->model_page_data->get_style_icons(26, 'ASC');


		$data['singleuser'] = $this->model_users->get_single_user($user_to_get);
		$data['userteam'] = $this->model_users->get_user_team($user_to_get);		
		$data['campaigninfo'] = $this->model_users->get_campaign_info($user_to_get);

		foreach ($data['singleuser'] as $user) {
			$data['full_name'] = $user->firstName . " " . $user->lastName;
			$data['first_name'] = $user->firstName;
			$data['last_name'] = $user->lastName;
			$data['user_id'] = $user->userID;
			$data['email'] = $user->emailAddress;
			$data['statement'] = $user->statement;
			$data['profile_pic'] = $user->profilePic;
		}

		foreach ($data['userteam'] as $team) {
			$data['team_name'] = $team->teamName;
			$data['team_id'] = $team->teamID;
		}

		foreach ($data['campaigninfo'] as $campaign) {
			$data['startDate'] = $campaign->startDate;
			$data['endDate'] = $campaign->endDate;
			$data['campaign_id'] = $campaign->campaignID;

			$ts1 = strtotime($data['startDate']);
			$ts2 = strtotime($data['endDate']);
			$ts3 = now();

			$seconds_diff = $ts2 - $ts1;
			$seconds_diff2 = $ts3 - $ts1;
			$seconds_diff3 = $ts2 - $ts3;

			$data['campaign_full'] = floor($seconds_diff/3600/24);
			$data['campaign_elapsed'] = floor($seconds_diff2/3600/24);
			$data['campaign_remaining'] = floor($seconds_diff3/3600/24);
		}	

		$data['styles_choices'] = $this->model_users->get_active_styles_and_pledges($data['campaign_id'], $data['user_id']);
		$data['pledges'] = $this->model_users->get_active_pledges($data['campaign_id'], $data['user_id']);

		$data['photos'] = $this->model_users->get_user_photos($user_to_get, $data['campaign_id']);



		$this->load->view('header', $data);
		$this->load->view('profile', $data);
		$this->load->view('footer', $data);
	}






}
?>