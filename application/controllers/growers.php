<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Growers extends CI_Controller {

	public function index()
	{
		$this->load->model('model_users');
		$this->load->model('model_page_data');

		$page = 'growers';
		$data['pagecopy'] = $this->model_page_data->get_copy($page);

		foreach ($data['pagecopy'] as $copy) {
			$data['copy'] = $copy->introCopy;
		}
		
		$data['users'] = $this->model_users->get_all_users();

		$data['body_class'] = 'growers-page';
		$data['page_title'] = "Grow for the Cure : Meet Our Growers";
		$data['page_description'] = "Meet the growers doing their part to help fight Lung Cancer";

		$data['styles'] = $this->model_page_data->get_style_icons(11, 'RANDOM');

		$this->load->view('header', $data);
		$this->load->view('growers', $data);
		$this->load->view('footer', $data);

	}

	function get_grower_list()
	{
		$this->load->model('model_users');

		if (isset($_REQUEST['term'])) {
			$term = $_GET['term'];
		} else {
			$term = '';
		}
		
		$data['users'] = $this->model_users->search_for_users($term);
		$data['teams'] = $this->model_users->search_for_teams($term);


		$numItems = count($data['users']);
		$numTeams = count($data['teams']);
		$i = 0;
		$x = 0;

		if (isset($data['users']) || isset($data['teams'])) {
			echo '[';

			if (isset($data['users'])) {
				foreach ($data['users'] as $user) {
					echo '{ "label": "' . $user->firstName . ' ' . $user->lastName . '", "link": "' . strtolower($user->firstName) . '-' . strtolower($user->lastName) . '/' . $user->userID . '" }';
					
					$i = $i+1;
					
					if ($i != $numItems) {
						echo ',';
					}
				}
			}
			
			if (isset($data['teams'])) {
				if (isset($data['users'])){
					echo ',';
				}

				foreach ($data['teams'] as $team) {
					echo '{ "label": "Team ' . $team->teamName . '", "link": "../teams/' . strtolower($team->teamName) . '/' . $team->teamID . '" }';
				$x = $x+1;
				if ($x != $numTeams) {
					echo ',';
				}
			}
			}
			echo ']';

		}
	}



}