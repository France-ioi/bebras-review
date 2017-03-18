<?php

Class User_Auth extends CI_Controller {

public function __construct() {
	parent::__construct();

	// Load form helper library
	$this->load->helper('form');

	// Load form validation library
	$this->load->library('form_validation');

	// Load session library
	$this->load->library('session');

	// Load database
	$this->load->model('login_database');
}

// Show login page
public function index() {
	$this->load->view('login_form');
}

// Show registration page
public function user_registration_show() {
	$this->load->view('registration_form');
}

// Validate and store registration data in database
public function new_user_registration() {

	// Check validation for user input in SignUp form
	$this->form_validation->set_rules('username', 'Username', 'trim|required');
	$this->form_validation->set_rules('firstName', 'Username', 'trim|required');
	$this->form_validation->set_rules('lastName', 'Username', 'trim|required');
	$this->form_validation->set_rules('email_value', 'Email', 'trim|required');
	$this->form_validation->set_rules('password', 'Password', 'trim|required');
	if ($this->form_validation->run() == FALSE) {
		$this->load->view('registration_form');
	} 
	else 
	{
		$data = array(
			'username' => $this->input->post('username'),
			'firstName' => $this->input->post('firstName'),
			'lastName' => $this->input->post('lastName'),
			'email' => $this->input->post('email_value'),
			'password' => $this->input->post('password')
		);
		$result = $this->login_database->registration_insert($data);
		if ($result == TRUE) {
			$data['message_display'] = 'Registered successfully!';
			$this->load->view('login_form', $data);
		} 
		else 
		{
			$data['message_display'] = 'Username already exists!';
			$this->load->view('registration_form', $data);
		}
	}
}

// Check for user login process
public function user_login_process() {
	$this->form_validation->set_rules('username', 'Username', 'trim|required');
	$this->form_validation->set_rules('password', 'Password', 'trim|required');

	if ($this->form_validation->run() == FALSE) {
		if(isset($this->session->userdata['logged_in'])){
			$this->load->view('welcome_message');
		}else{
			$this->load->view('login_form');
		}
	} else {
	$data = array(
		'username' => $this->input->post('username'),
		'password' => $this->input->post('password')
	);
    error_log(md5($this->input->post('password')));
	$result = $this->login_database->login($data);
	if ($result == 'ok') {
		$username = $this->input->post('username');
		$result = $this->login_database->read_user_information($username);
		if ($result != 	false) {
			$session_data = array(
				'username' => $result[0]->username,
				'username1' => $result[0]->lastName, // TODO :: add more data
				'email' => $result[0]->email,
			);
			// Add user data in session
			$this->session->set_userdata('logged_in', $session_data);
			$this->load->view('welcome_message');
		}
	} elseif ($result == 'invalid') {
		$data = array(
			'error_message' => 'Invalid username or password'
		);
		$this->load->view('login_form', $data);
	} elseif ($result == 'unconfirmed') {
		$data = array(
			'error_message' => 'User has not been confirmed by an admin yet'
		);
		$this->load->view('login_form', $data);
	}
    }
}

// Logout from admin page
	public function logout() {
		// Removing session data
		$sess_array = array(
			'username' => ''
		);
		$this->session->unset_userdata('logged_in', $sess_array);
		$data['message_display'] = 'Successfully logged out';
		$this->load->view('login_form', $data);
	}

}

?>
