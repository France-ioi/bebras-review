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

			$data['countryCode'] = '';
			$data['registrationDate']=date('y-m-d');
			$data['LastLoginDate']=date('y-m-d');
			$data['groupID']='1';
			$data['groupRole']='Member';
			$data['localCheckoutFolder']='localCheckoutFolder';
			$data['autoLoadtasks']="false";
			$data['nbReviewsDesired'] = '0';
            if(!isset($data['role'])) {
			    $data['role']='Unconfirmed';
            }
            if(!isset($data['svnLogin'])) {
			    $data['svnLogin']="";
            }
            if(!isset($data['fromSvn'])) {
                $data['fromSvn'] = false;
            }
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
            if($result['password'] == $hashed_password) {
                if($result['role'] == 'Unconfirmed') {
                    return 'unconfirmed';
                } else {
                    $this->db->update('users', array('lastLoginDate' => date('y-m-d')), array('ID' => $result['ID']));
                    return 'ok';
                }
            }
        }

        // User not found or password incorrect, try to log into SVN
        svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME,             $data['username']);
        svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD,             $data['password']);
        svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true); // <--- Important for certificate issues!
        svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE,              true);
        svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE,                true);

        if(@svn_ls($this->config->item('svn_remote'))) {
            if($query->num_rows() == 1) {
                // Update password
                $salt = md5(time());
                $this->db->update('users', array('salt' => $salt, 'password' => md5($data['password'] . $data['salt'])), array('username' => $username));
                return 'ok';
            } else {
                // Create new user automatically
                $data['firstName'] = $data['username'];
                $data['lastName'] = '';
                $data['email'] = '';
                $data['role'] = 'Member';
                $data['svnLogin'] = $data['username'];
                $data['fromSvn'] = true;
                if($this->registration_insert($data)) {
                    // User automatically created
                    return 'ok';
                } else {
                    // Error while creating
                    return 'create_error';
                }
            }
        } else {
            // User doesn't exist in database nor on SVN
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
