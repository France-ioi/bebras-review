<?php
// TODO :: Rewrite all requests to use actual JOINs
// TODO :: clean up code

class presentation_model extends CI_Model {
	public function __construct()
	{
		$this->load->database();
	}
	public function getdata()
	{
        // TODO :: temporary until i have time to figure out how this db object
        // works
		$users = $this->db->get('users');
		$users_array = $users->result_array();
		for($i=0;$i<$users->num_rows();$i++)
		{
            $userID_to_name[$users_array[$i]['ID']] = $users_array[$i]['firstName'] . ' ' . $users_array[$i]['lastName'];
        }

		$username = ($this->session->userdata['logged_in']['username']);
		$selfuser = $this->db->get_where('users',array('username'=>$username))->result_array()[0];

		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();
		for($i=0;$i<$tasks->num_rows();$i++)
		{
            if($result[$i]['ownerID'] == -1) {
    			$result[$i]['ownerName'] = $result[$i]['svnLogin'];
            } else {
    			$result[$i]['ownerName'] = $userID_to_name[$result[$i]['ownerID']];
            }
            if(isset($this->countries[$result[$i]['countryCode']])) {
                $result[$i]['country'] = $result[$i]['countryCode'] . ' - ' . $this->countries[$result[$i]['countryCode']];
            } else {
                $result[$i]['country'] = $result[$i]['countryCode'];
            }
			if($result[$i]['assignedGroupID']!="0")
			{
				$group = $this->db->get_where('groups',array('ID'=>$result[$i]['assignedGroupID']))->result_array();
				$result[$i]['Group']=$group[0]['name'];
			} else {
				$result[$i]['Group']="No Group";
            }

            if($selfuser['groupID'] == $result[$i]['assignedGroupID'] && $selfuser['groupRole'] == 'Admin') {
                $result[$i]['groupadminflag'] = true;
            }

			$review = $this->db->get_where('reviews', 'taskID = '.$result[$i]['ID'].' AND currentRating > 0 AND potentialRating > 0');
			$result[$i]['Reviews']=$review->num_rows();
			$reviewresult=$review->result_array();
			$sum1=0;
			$sum2=0;

            $result[$i]['reviewers'] = Array();
			for($j=0;$j<$review->num_rows();$j++)
			{
                if($reviewresult[$j]['isAssigned'] == 1) {
                    $result[$i]['reviewers'][$reviewresult[$j]['userID']] = $userID_to_name[$reviewresult[$j]['userID']];
                }
				$sum1+=$reviewresult[$j]['currentRating'];
				$sum2+=$reviewresult[$j]['potentialRating'];
			}
			if($review->num_rows()>0)
				$result[$i]['ar']=number_format($sum1/$review->num_rows(),1);
			else
				$result[$i]['ar']=0;
			if($review->num_rows()>0)
				$result[$i]['p']=number_format($sum2/$review->num_rows(),1);
			else
				$result[$i]['p']=0;

			if($result[$i]['htmlFileName']) {
			    $result[$i]['htmlLink'] = $this->config->item('svn_reldir') . $result[$i]['folderPath'] . '/' . $result[$i]['folderName'] . '/' . $result[$i]['htmlFileName'];
                $result[$i]['otherHtmlLink']=$selfuser['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['htmlFileName'];
            } else {
				$result[$i]['htmlLink']="";
				$result[$i]['otherHtmlLink']="";
			}
			if($result[$i]['odtFileName']) {
			    $result[$i]['odtLink'] = $this->config->item('svn_reldir') . $result[$i]['folderPath'] . '/' . $result[$i]['folderName'] . '/' . $result[$i]['odtFileName'];
                $result[$i]['otherOdtLink']=$selfuser['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['odtFileName'];
            } else {
				$result[$i]['odtLink']="";
				$result[$i]['otherOdtLink']="";
			}
			if($result[$i]['pdfFileName']) {
			    $result[$i]['pdfLink'] = $this->config->item('svn_reldir') . $result[$i]['folderPath'] . '/' . $result[$i]['folderName'] . '/' . $result[$i]['pdfFileName'];
                $result[$i]['otherPdfLink']=$selfuser['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['pdfFileName'];
            } else {
				$result[$i]['pdfLink']="";
				$result[$i]['otherPdfLink']="";
			}
		}

        $response = array();
        $response['tasksList'] = $result;
		$response['autoloadTasks']=$selfuser['autoLoadTasks'];
		$response['localCheckoutFolder']=$selfuser['localCheckoutFolder'];
        $response['isAdmin'] = ($selfuser['role'] == 'Admin');

        // Load groups
		$group = $this->db->get('groups')->result_array();
        $response['groupsList'] = $group;

        // Load reviews
		$reviews = $this->db->get('reviews');
		$result = $reviews->result_array();
		for($i=0;$i<$reviews->num_rows();$i++)
		{
			$tasks = $this->db->get_where('tasks',array('ID'=>$result[$i]['taskID']))->result_array();
			$result[$i]['folderName']=$tasks[0]['folderName'];
			$result[$i]['year']=$tasks[0]['year'];
			$result[$i]['countryCode']=$tasks[0]['countryCode'];
            if(isset($this->countries[$result[$i]['countryCode']])) {
                $result[$i]['country'] = $result[$i]['countryCode'] . ' - ' . $this->countries[$result[$i]['countryCode']];
            } else {
                $result[$i]['country'] = $result[$i]['countryCode'];
            }
			$result[$i]['folderName']=$tasks[0]['folderName'];
			if($tasks[0]['assignedGroupID']!="0")
			{
				$group = $this->db->get_where('groups',array('ID'=>$tasks[0]['assignedGroupID']))->result_array();
				$result[$i]['Group']=$group[0]['name'];
			}
			else
				$result[$i]['Group']="No Group";
			$user = $this->db->get_where('users',array('ID'=>$result[$i]['userID']))->result_array();
			$result[$i]['author']=$user[0]['firstName'] . ' ' . $user[0]['lastName'];
		}

        $response['reviewsList'] = $result;
        $response['messagesList'] = $this->getmessage();
	
		return $response;
	}
	public function getreviews()
	{
		$reviews = $this->db->get_where('reviews', 'currentRating > 0 AND potentialRating > 0');
		$result = $reviews->result_array();
		for($i=0;$i<$reviews->num_rows();$i++)
		{
			$tasks = $this->db->get_where('tasks',array('ID'=>$result[$i]['taskID']))->result_array();
			$result[$i]['folderName']=$tasks[0]['folderName'];
			$result[$i]['year']=$tasks[0]['year'];
			$result[$i]['countryCode']=$tasks[0]['countryCode'];
            if(isset($this->countries[$result[$i]['countryCode']])) {
                $result[$i]['country'] = $result[$i]['countryCode'] . ' - ' . $this->countries[$result[$i]['countryCode']];
            } else {
                $result[$i]['country'] = $result[$i]['countryCode'];
            }
			$result[$i]['folderName']=$tasks[0]['folderName'];
			if($tasks[0]['assignedGroupID']!="0")
			{
				$group = $this->db->get_where('groups',array('ID'=>$tasks[0]['assignedGroupID']))->result_array();
				$result[$i]['Group']=$group[0]['name'];
			}
			else
				$result[$i]['Group']="No Group";
			$user = $this->db->get_where('users',array('ID'=>$result[$i]['userID']))->result_array();
			$result[$i]['author']=$user[0]['firstName'] . ' ' . $user[0]['lastName'];
		}

		return $result;
	}
	public function getuser()
	{
		$tasks = $this->db->get('users');
		$result = $tasks->result_array();
		for($i=0;$i<$tasks->num_rows();$i++)
		{
            if(isset($this->countries[$result[$i]['countryCode']])) {
                $result[$i]['country'] = $result[$i]['countryCode'] . ' - ' . $this->countries[$result[$i]['countryCode']];
            } else {
                $result[$i]['country'] = $result[$i]['countryCode'];
            }
			$group = $this->db->get_where('groups',array('ID'=>$result[$i]['groupID']));
            if($group->num_rows() == 0) {
              $result[$i]['Group'] = 'No group';
            } else {
              $result[$i]['Group'] = $group->result_array()[0]['name'];
            }
			$review = $this->db->get_where('reviews',array('userID'=>$result[$i]['ID']));
			$result[$i]['Reviews']=$review->num_rows();
            unset($result[$i]['salt']);
            unset($result[$i]['password']);
		}

        $response = array();
        $response['usersList'] = $result;

		$group = $this->db->get('groups')->result_array();
        $response['groupsList'] = $group;

		$username = ($this->session->userdata['logged_in']['username']);
		$user = $this->db->get_where('users',array('username'=>$username))->result_array()[0];
        $response['ownID'] = $user['ID'];
        $response['isAdmin'] = ($user['role'] == 'Admin');

		return $response;
	}
	public function getprofile()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username));
		$result = $users->result_array();
		$group = $this->db->get_where('groups',array('ID'=>$result[0]['groupID']))->result_array();
		$result[0]['Group']=$group[0]['name'];
		$result[0]['flag1']="";
		$result[0]['flag2']="";
        unset($result[0]['salt']);
        unset($result[0]['password']);

		return $result[0];
	}
	public function setprofile()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$user = $this->db->get_where('users',array('username'=>$username))->result_array()[0];

		$item = $_POST['data'];
	
		if(isset($item['newpassword']) && md5($item['oldpassword'] . $user['salt']) == $user['password'])
		{
            $newdata = array();
            $newdata['salt'] = md5(time());
			$newdata['password'] = md5($item['newpassword'] . $newdata['salt']);
		} else {
            $newdata = array(
                'svnLogin' => $item['svnLogin'],
                'firstName' => $item['firstName'],
                'lastName' => $item['lastName'],
                'countryCode' => $item['countryCode'],
                'autoLoadTasks' => $item['autoLoadTasks'],
                'localCheckoutFolder' => $item['localCheckoutFolder']);
        }

        $this->db->update('users', $newdata, array('ID' => $user['ID']));

        return $this->getprofile();
	}


	public function getgeneral()
	{
		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();

		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username))->result_array();

		for($i=0;$i<$tasks->num_rows();$i++)
		{
            if($result[$i]['ownerID'] == -1) {
    			$result[$i]['ownerName'] = $result[$i]['svnLogin'];
            } else {
    			$result[$i]['ownerName'] = $userID_to_name[$result[$i]['ownerID']];
            }
            if(isset($this->countries[$result[$i]['countryCode']])) {
                $result[$i]['country'] = $result[$i]['countryCode'] . ' - ' . $this->countries[$result[$i]['countryCode']];
            } else {
                $result[$i]['country'] = $result[$i]['countryCode'];
            }
			if($result[$i]['assignedGroupID']!="0")
			{
				$group = $this->db->get_where('groups',array('ID'=>$result[$i]['assignedGroupID']))->result_array();
				$result[$i]['Group']=$group[0]['name'];
			}
			else
				$result[$i]['Group']="No Group";
		
			$review = $this->db->get_where('reviews',array('taskID'=>$result[$i]['ID']));
			$result[$i]['Reviews']=$review->num_rows();
			$reviewresult=$review->result_array();
			$sum1=0;
			$sum2=0;
			for($j=0;$j<$review->num_rows();$j++)
			{
				$sum1+=$reviewresult[$j]['currentRating'];
				$sum2+=$reviewresult[$j]['potentialRating'];
			}

			if($review->num_rows()>0)
				$result[$i]['ar']=number_format($sum1/$review->num_rows(),1);
			else
				$result[$i]['ar']=0;
			if($review->num_rows()>0)
				$result[$i]['p']=number_format($sum2/$review->num_rows(),1);
			else
				$result[$i]['p']=0;
					
			$result[$i]['authorflag']=($users[0]['ID']==$result[$i]['ownerID']);
			$result[$i]['groupadminflag']=($users[0]['groupRole']=="Admin");
			$result[$i]['adminflag']=($users[0]['role']=="Admin");
		}
	
		return $result;
	}

	public function lastsave()
	{
		$newitem = $_POST['data'];

        $task = $this->db->get_where('tasks', array('ID' => $newitem['ID']));
        if($task->num_rows() == 0) {
          return $this->getdata();
        }
        $task = $task->result_array()[0];

		$username = ($this->session->userdata['logged_in']['username']);
		$user = $this->db->get_where('users',array('username'=>$username))->result_array()[0];
        if($user['role'] == 'Admin') {
		  $this->db->update('tasks', array(
              'htmlFileName'=>$newitem['htmlFileName'],
              'pdfFileName'=>$newitem['pdfFileName'],
              'odtFileName'=>$newitem['odtFileName'],
              'assignedGroupID'=>$newitem['assignedGroupID'],
              'status'=>$newitem['status'],
              'statusComment'=>$newitem['statusComment']),
            array('ID'=>$newitem['ID']));
        } elseif($user['groupID'] == $task['assignedGroupID'] && $user['groupRole'] == 'Admin') {
		  $this->db->update('tasks', array(
              'status'=>$newitem['status'],
              'statusComment'=>$newitem['statusComment']),
            array('ID'=>$newitem['ID']));
        }

		return $this->getdata();
	}

	public function group()
	{
		$group = $this->db->get('groups')->result_array();
		return $group;
	}

	public function getyour()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username))->result_array();
		$reviews = $this->db->get_where('reviews',array('userID'=>$users[0]['ID']));
		$result = $reviews->result_array();
		for($i=0;$i<$reviews->num_rows();$i++)
		{
			$tasks = $this->db->get_where('tasks',array('ID'=>$result[$i]['taskID']))->result_array();
			$result[$i]['folderName']=$tasks[0]['folderName'];
		}
		return $result;
	}
	public function reviewcreate()
	{
        // TODO :: check which data is available
        $username = ($this->session->userdata['logged_in']['username']);
        $users = $this->db->get_where('users',array('username'=>$username));
        $userID = $users->result_array()[0]['ID'];

        $tasks = $this->db->get_where('tasks', array('folderName'=>$_POST['folderName']))->result_array();
        $taskID = $tasks[0]['ID'];
        $newreview = array(
            'userID' => $userID,
            'taskID' => $taskID,
            'currentRating' => 0,
            'potentialrating' => 0,
            'comment' => '',
            'isAssigned' => 0,
            'initialReviewDate' => date('y-m-d'),
            'lastChangeReviewDate' => date('y-m-d')
            );
        $this->db->insert('reviews', $newreview);
        $newreview['ID'] = $this->db->insert_id();
		return $newreview;
	}
	public function reviewchange()
	{
		$this->db->update('reviews',array(
                'comment'=>$_POST['comment'],
    		    'currentRating'=>$_POST['a'],
    		    'potentialRating'=>$_POST['b'],
    		    'lastChangeReviewDate'=>date('y-m-d')),
            array('ID'=>$_POST['id']));
		return true;
	}

	public function getall()
	{
		$tasks = $this->db->get('tasks');
		$count=$tasks->num_rows();
		$result = $tasks->result_array();
		$re=array();
		for($i=0;$i<$count;$i++)
		{
			$reviews = $this->db->get_where('reviews', 'taskID = '.$result[$i]['ID'].' AND currentRating > 0 AND potentialRating > 0');
			$co = $reviews->num_rows();
			$list=$reviews->result_array();
			$sum1=0;
			$sum2=0;
			for($j=0;$j<$co;$j++)
			{
				$user = $this->db->get_where('users',array('ID'=>$list[$j]['userID']))->result_array();
				$list[$j]['author']=$user[0]['firstName'] . ' ' . $user[0]['lastName'];
				$sum1+=$list[$j]['currentRating'];
				$sum2+=$list[$j]['potentialRating'];
			}
			$val['data']=$list;
			if($co>0)
			{
				$val['ar']=number_format($sum1/$co,1);
				$val['p']=number_format($sum2/$co,1);
			}
			else
			{
				$val['ar']=0;
				$val['p']=0;
			}
			$val['count']=$co;
			$re[$result[$i]['folderName']]=$val;
		}
		return $re;
	
	}
	public function getmessage()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$user = $this->db->get_where('users',array('username'=>$username))->result_array();
		$f=$user[0]['role']=='Admin';
		$reviews = $this->db->get('messages');
		$count=$reviews->num_rows();
		$result = $reviews->result_array();
		$sum1=0;
		$sum2=0;
		for($i=0;$i<$count;$i++)
		{
			$tasks = $this->db->get_where('tasks',array('ID'=>$result[$i]['taskID']))->result_array();
			$result[$i]['folderName']=$tasks[0]['folderName'];
			$user = $this->db->get_where('users',array('ID'=>$result[$i]['userID']))->result_array();
			$result[$i]['author']=$user[0]['firstName'] . ' ' . $user[0]['lastName'];
			$result[$i]['flag']=($user[0]['username']==$username);
			$result[$i]['flagModify']=($user[0]['username']==$username||$f);
			$result[$i]['flagAuthor']=($result[$i]['userID']==$tasks[0]['ownerID']);
		}
	
		return $result;
	}
	public function getlist()
	{
		$reviews = $this->db->get_where('tasks');
		$result = $reviews->result_array();
		return $result;
	}
	public function sendmess()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username))->result_array();
		$tasks = $this->db->get_where('tasks',array('folderName'=>$_POST['taskID']))->result_array();
		$item=array('taskID'=>$tasks[0]['ID'], 'userID'=>$users[0]['ID'], 'content'=>$_POST['mess'], 'dateCreated'=>date('y-m-d'), 'dateModified'=>date('y-m-d'));
		$this->db->insert('messages', $item);
		return $item;
	}
	public function discussionchange()
	{
		$this->db->update('messages',array('content'=>$_POST['mess']),array('ID'=>$_POST['ID']));
		return $_POST;
	}

	public function profilelisttasks()
	{
		$review = $this->db->get_where('reviews',array('userID'=>$_POST['data'],'isAssigned'=>1));
		$count = $review->num_rows();
		$result = $review->result_array();
		$re=array();
		for($i=0;$i<$count;$i++)
		{
			$tasks = $this->db->get_where('tasks',array('ID'=>$result[$i]['taskID']))->result_array();
			$re[$i]=$tasks[0];
		}
		return $re;
	}
	public function userupdate()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$user = $this->db->get_where('users',array('username'=>$username))->result_array()[0];
        if($user['role'] == 'Admin') {
          $this->db->update('users',array($_POST['member']=>$_POST['data']),array('ID'=>$_POST['id']));
        }
		return $_POST;
	}

	public function getpdf()
	{
		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();
		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username))->result_array();
		$result['autoLoadTasks']=$users[0]['autoLoadTasks'];
		$result['localCheckoutFolder']=$users[0]['localCheckoutFolder'];
		for($i=0;$i<$tasks->num_rows();$i++)
		{
			$result[$i]['link'] = $this->config->item('svn_reldir') . $result[$i]['folderPath'] . '/' . $result[$i]['folderName'] . '/' . $result[$i]['pdfFileName'];
			$result[$i]['otherlink']=$users[0]['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['pdfFileName'];
			if($result[$i]['pdfFileName']==NULL)
			{
				$result[$i]['link']="";
				$result[$i]['otherlink']="";
			}
		}
	
		return $result;
	}

	public function getodt()
	{
		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();
		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username))->result_array();
		$result['autoLoadTasks']=$users[0]['autoLoadTasks'];
		$result['localCheckoutFolder']=$users[0]['localCheckoutFolder'];
		for($i=0;$i<$tasks->num_rows();$i++)
		{
			$result[$i]['link'] = $this->config->item('svn_reldir') . $result[$i]['folderPath'] . '/' . $result[$i]['folderName'] . '/' . $result[$i]['odtFileName'];
			$result[$i]['otherlink']=$users[0]['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['odtFileName'];
		
			if($result[$i]['odtFileName']==NULL)
			{
				$result[$i]['link']="";
				$result[$i]['otherlink']="";
			}
		}
	
		return $result;
	}

	public function autosave()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$this->db->update('users',array('autoLoadTasks'=>"true"),array('username'=>$username)); // TODO :: uh, always true?
	}

    public function recupdatesvn($baseDir, $dir) {
        $dirFullPath = $baseDir . $dir;
        $dirName = basename($dirFullPath);
        // Check whether it's a task folder
        if(strlen($dirName) > 11 && $dirName[4] == '-' && $dirName[7] == '-') {
            // Current folder is a task folder
            $newItem = array();

            $newItem['folderName'] = $dirName;
            $newItem['folderPath'] = dirname($dir);

            $newItem['year'] = substr($dirName, 0, 4);
            $newItem['countryCode'] = substr($dirName, 5, 2);
            $newItem['textID'] = substr($dirName, 0, 10);

            $newItem['importDate'] = date('y-m-d');

            $svnLogs = svn_log($dirFullPath); // Can't do better
            $newItem['svnLogin'] = end($svnLogs)['author'];
            $newItem['repositoryDate'] = substr(end($svnLogs)['date'], 0, 10);
            $newItem['lastChangeDate'] = substr($svnLogs[0]['date'], 0, 10);

		    $owners = $this->db->get_where('users', array('svnLogin' => $newItem['svnLogin']));
            if($owners->num_rows() > 0) {
                $newItem['ownerID'] = $owners->result_array()[0]['ID'];
            } else {
                $newItem['ownerID'] = -1;
                // TODO :: check for new user <-> svnLogin matches after a new svnLogin is given in profile
            }

            // Search for files; they must start by textID
            $newItem['htmlFileName'] = '';
            $newItem['odtFileName'] = '';
            $newItem['pdfFileName'] = '';

            foreach(scandir($dirFullPath) as $elem) {
                if(is_dir($dirFullPath.'/'.$elem)) {
                    continue;
                } elseif($elem == 'index.html') {
                    $newItem['htmlFileName'] = $elem;
                } elseif(substr($elem, 0, 10) == $newItem['textID']) {
                    foreach(array('html', 'odt', 'pdf') as $ext) {
                        // We look for the first file with the expected filename
                        if($newItem[$ext.'FileName'] == '' && substr($elem, -strlen($ext)-1) == '.'.$ext) {
                            $newItem[$ext.'FileName'] = $elem;
                        }
                    }
                }
            }

			$check = $this->db->get_where('tasks',array('folderName' => $newItem["folderName"]));
			if($check->num_rows() == 0)
				$this->db->insert('tasks', $newItem);
			else
			{
				$this->db->update('tasks',
                    array('htmlFileName' => $newItem['htmlFileName'],
                          'pdfFileName' => $newItem['pdfFileName'],
                          'odtFileName' => $newItem['odtFileName'],
                          'lastChangeDate' => $newItem['lastChangeDate']),
                    array('folderName' => $newItem["folderName"]));
			}
        } else {
            // Current folder is not a task folder
            foreach(scandir($dirFullPath) as $elem) {
                $elemPath = $dirFullPath . '/' . $elem;
                if(is_dir($elemPath) && $elem != '.' && $elem != '..') {
                    $this->recupdatesvn($baseDir, $dir . '/' . $elem);
                }
            }
        }
    }

	public function updatesvn()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$user = $this->db->get_where('users',array('username'=>$username))->result_array()[0];
        if($user['role'] != 'Admin') {
            return;
        }

		ini_set('max_execution_time', 3600);
	
        svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME,             $this->config->item('svn_user'));
        svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD,             $this->config->item('svn_password'));
        svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true); // <--- Important for certificate issues!
        svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE,              true);
        svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE,                true);

        foreach($this->config->item('svn_subdirs') as $subdir) {
            $newRev = svn_update($this->config->item('svn_basedir') + $subdir);
            $this->recupdatesvn($this->config->item('svn_basedir'), $subdir);
        }
	}

    public $countries = array(
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua And Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia And Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote D\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island & Mcdonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran, Islamic Republic Of',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle Of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States Of',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'AN' => 'Netherlands Antilles',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts And Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre And Miquelon',
        'VC' => 'Saint Vincent And Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome And Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia And Sandwich Isl.',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard And Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad And Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks And Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis And Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    );
}
