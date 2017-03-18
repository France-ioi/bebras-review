<?php

Class Login_Database extends CI_Model {
	public function __construct()
	{
		$this->load->database();
	}
	// Insert registration data in database
	public function registration_insert($data) {
		// Query to check whether username already exist or not
        $query = $this->db->get_where('users',array('username' => $data['username']));
		if ($query->num_rows() == 0)
		{
			// Query to insert data in database
			$data['salt'] = md5(time());
            $data['password'] = md5($data['password'] . $data['salt']);

			$data['svnLogin']="";
			$data['countryCode']="France";
			$data['registrationDate']=date('y-m-d');
			$data['LastLoginDate']=date('y-m-d');
			$data['role']='Unconfirmed';
			$data['groupID']='1';
			$data['groupRole']='Admin';
			$data['localCheckoutFolder']='localCheckoutFolder';
			$data['autoLoadtasks']="false";
			$this->db->insert('users', $data);
			if ($this->db->affected_rows() > 0)
			{
				return true;
			}
		} 
		return false;
	}

	// Read data using username and password
	public function login($data) {
        $query = $this->db->get_where('users',array('username' => $data['username']));

		if ($query->num_rows() == 1) {
            $result = $query->result_array()[0];
            $hashed_password = md5($data['password'] . $result['salt']);
            if($result['password'] != $hashed_password) {
                return 'invalid';
            } elseif($result['role'] == 'Unconfirmed') {
                return 'unconfirmed';
            } else {
                $this->db->update('users', array('lastLoginDate' => date('y-m-d')), array('ID' => $result['ID']));
    			return 'ok';
            }
		} else {
			return 'invalid';
		}
	}

	// Read data from database to show data in admin page
	public function read_user_information($username)
	{
        $query = $this->db->get_where('users',array('username' => $username));

		if ($query->num_rows() == 1) {
			return $query->result();
		} 
		else 
		{
			return false;
		}
	}

}

?>
