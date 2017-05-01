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

			$review = $this->db->get_where('reviews', 'taskID = '.$result[$i]['ID']);
			$reviewresult=$review->result_array();
			$sum1 = 0;
			$sum2 = 0;
            $nbreviews = 0;

            $result[$i]['reviewers'] = Array();
			for($j=0;$j<$review->num_rows();$j++)
			{
                if($reviewresult[$j]['isAssigned'] == 1) {
                    $result[$i]['reviewers'][$reviewresult[$j]['userID']] = $userID_to_name[$reviewresult[$j]['userID']];
                }
                if($reviewresult[$j]['currentRating'] > 0 && $reviewresult[$j]['potentialRating'] > 0) {
                  $sum1 += $reviewresult[$j]['currentRating'];
                  $sum2 += $reviewresult[$j]['potentialRating'];
                  $nbreviews += 1;
                }
			}
            if($nbreviews > 0) {
              $result[$i]['ar'] = number_format($sum1/$nbreviews,1);
              $result[$i]['p'] = number_format($sum2/$nbreviews,1);
            } else {
              $result[$i]['ar'] = 0;
              $result[$i]['p'] = 0;
            }
			$result[$i]['Reviews'] = $nbreviews;

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
            $result[$i]['isMine'] = ($selfuser['ID'] == $result[$i]['userID']);
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

        $response = array();
        $response['profile'] = $result[0];
        $response['countryList'] = array();
        foreach($this->countries as $code => $name) {
          $response['countryList'][] = array('code' => $code, 'name' => $code.' - '.$name);
        }

		return $response;
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
        $username = ($this->session->userdata['logged_in']['username']);
        $user = $this->db->get_where('users',array('username'=>$username))->result_array()[0];
        $cond = array('ID' => $_POST['id']);
        if($user['role'] != 'Admin') {
          $cond['userID'] = $user['ID'];
        }
        $review = $this->db->get_where('reviews', array('ID' => $_POST['id']));
        if($review->num_rows() > 0) {
          $curReview = $review->result_array()[0];
          if($curReview['isAssigned'] == '0' && $_POST['a'] == '-1' && $_POST['b'] == '-1') {
            $this->db->delete('reviews', array('ID' => $curReview['ID']));
          } else {
            $data = array(
              'comment' => $_POST['comment'],
              'currentRating' => max(0, $_POST['a']),
              'potentialRating'=> max(0, $_POST['b']),
              'lastChangeReviewDate'=>date('y-m-d'));
            if($review->result_array()[0]['initialReviewDate'] == '0000-00-00') {
              $data['initialReviewDate'] = date('y-m-d');
            }
            $this->db->update('reviews', $data, array('ID'=>$_POST['id']));
          }
		  return true;
        } else {
          return false;
        }
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
        $username = ($this->session->userdata['logged_in']['username']);
        $user = $this->db->get_where('users',array('username'=>$username))->result_array()[0];
		$review = $this->db->get_where('reviews',array('userID'=>$user['ID'],'isAssigned'=>1));
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

        // Modules are folders to update without scanning them for tasks
        foreach($this->config->item('svn_modules') as $subdir) {
            $newRev = svn_update($this->config->item('svn_basedir') . $subdir);
        }

        foreach($this->config->item('svn_subdirs') as $subdir) {
            $newRev = svn_update($this->config->item('svn_basedir') . $subdir);
            // TODO :: return something, such as for instance success or
            // failure ($newRev > -1) and/or how many tasks were detected
            $this->recupdatesvn($this->config->item('svn_basedir'), $subdir);
        }
	}

    public $countries = array(
        'AD' => 'Andorra',
        'AE' => 'United Arab Emirates',
        'AF' => 'Afghanistan',
        'AG' => 'Antigua And Barbuda',
        'AI' => 'Anguilla',
        'AL' => 'Albania',
        'AM' => 'Armenia',
        'AN' => 'Netherlands Antilles',
        'AO' => 'Angola',
        'AQ' => 'Antarctica',
        'AR' => 'Argentina',
        'AS' => 'American Samoa',
        'AT' => 'Austria',
        'AU' => 'Australia',
        'AW' => 'Aruba',
        'AX' => 'Aland Islands',
        'AZ' => 'Azerbaijan',
        'BA' => 'Bosnia And Herzegovina',
        'BB' => 'Barbados',
        'BD' => 'Bangladesh',
        'BE' => 'Belgium',
        'BF' => 'Burkina Faso',
        'BG' => 'Bulgaria',
        'BH' => 'Bahrain',
        'BI' => 'Burundi',
        'BJ' => 'Benin',
        'BL' => 'Saint Barthelemy',
        'BM' => 'Bermuda',
        'BN' => 'Brunei Darussalam',
        'BO' => 'Bolivia',
        'BR' => 'Brazil',
        'BS' => 'Bahamas',
        'BT' => 'Bhutan',
        'BV' => 'Bouvet Island',
        'BW' => 'Botswana',
        'BY' => 'Belarus',
        'BZ' => 'Belize',
        'CA' => 'Canada',
        'CC' => 'Cocos (Keeling) Islands',
        'CD' => 'Congo, Democratic Republic',
        'CF' => 'Central African Republic',
        'CG' => 'Congo',
        'CH' => 'Switzerland',
        'CI' => 'Cote D\'Ivoire',
        'CK' => 'Cook Islands',
        'CL' => 'Chile',
        'CM' => 'Cameroon',
        'CN' => 'China',
        'CO' => 'Colombia',
        'CR' => 'Costa Rica',
        'CU' => 'Cuba',
        'CV' => 'Cape Verde',
        'CX' => 'Christmas Island',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DE' => 'Germany',
        'DJ' => 'Djibouti',
        'DK' => 'Denmark',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'DZ' => 'Algeria',
        'EC' => 'Ecuador',
        'EE' => 'Estonia',
        'EG' => 'Egypt',
        'EH' => 'Western Sahara',
        'ER' => 'Eritrea',
        'ES' => 'Spain',
        'ET' => 'Ethiopia',
        'FI' => 'Finland',
        'FJ' => 'Fiji',
        'FK' => 'Falkland Islands (Malvinas)',
        'FM' => 'Micronesia, Federated States Of',
        'FO' => 'Faroe Islands',
        'FR' => 'France',
        'GA' => 'Gabon',
        'GB' => 'United Kingdom',
        'GD' => 'Grenada',
        'GE' => 'Georgia',
        'GF' => 'French Guiana',
        'GG' => 'Guernsey',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GL' => 'Greenland',
        'GM' => 'Gambia',
        'GN' => 'Guinea',
        'GP' => 'Guadeloupe',
        'GQ' => 'Equatorial Guinea',
        'GR' => 'Greece',
        'GS' => 'South Georgia And Sandwich Isl.',
        'GT' => 'Guatemala',
        'GU' => 'Guam',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HK' => 'Hong Kong',
        'HM' => 'Heard Island & Mcdonald Islands',
        'HN' => 'Honduras',
        'HR' => 'Croatia',
        'HT' => 'Haiti',
        'HU' => 'Hungary',
        'ID' => 'Indonesia',
        'IE' => 'Ireland',
        'IL' => 'Israel',
        'IM' => 'Isle Of Man',
        'IN' => 'India',
        'IO' => 'British Indian Ocean Territory',
        'IQ' => 'Iraq',
        'IR' => 'Iran, Islamic Republic Of',
        'IS' => 'Iceland',
        'IT' => 'Italy',
        'JE' => 'Jersey',
        'JM' => 'Jamaica',
        'JO' => 'Jordan',
        'JP' => 'Japan',
        'KE' => 'Kenya',
        'KG' => 'Kyrgyzstan',
        'KH' => 'Cambodia',
        'KI' => 'Kiribati',
        'KM' => 'Comoros',
        'KN' => 'Saint Kitts And Nevis',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KY' => 'Cayman Islands',
        'KZ' => 'Kazakhstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LB' => 'Lebanon',
        'LC' => 'Saint Lucia',
        'LI' => 'Liechtenstein',
        'LK' => 'Sri Lanka',
        'LR' => 'Liberia',
        'LS' => 'Lesotho',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'LV' => 'Latvia',
        'LY' => 'Libyan Arab Jamahiriya',
        'MA' => 'Morocco',
        'MC' => 'Monaco',
        'MD' => 'Moldova',
        'ME' => 'Montenegro',
        'MF' => 'Saint Martin',
        'MG' => 'Madagascar',
        'MH' => 'Marshall Islands',
        'MK' => 'Macedonia',
        'ML' => 'Mali',
        'MM' => 'Myanmar',
        'MN' => 'Mongolia',
        'MO' => 'Macao',
        'MP' => 'Northern Mariana Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MS' => 'Montserrat',
        'MT' => 'Malta',
        'MU' => 'Mauritius',
        'MV' => 'Maldives',
        'MW' => 'Malawi',
        'MX' => 'Mexico',
        'MY' => 'Malaysia',
        'MZ' => 'Mozambique',
        'NA' => 'Namibia',
        'NC' => 'New Caledonia',
        'NE' => 'Niger',
        'NF' => 'Norfolk Island',
        'NG' => 'Nigeria',
        'NI' => 'Nicaragua',
        'NL' => 'Netherlands',
        'NO' => 'Norway',
        'NP' => 'Nepal',
        'NR' => 'Nauru',
        'NU' => 'Niue',
        'NZ' => 'New Zealand',
        'OM' => 'Oman',
        'PA' => 'Panama',
        'PE' => 'Peru',
        'PF' => 'French Polynesia',
        'PG' => 'Papua New Guinea',
        'PH' => 'Philippines',
        'PK' => 'Pakistan',
        'PL' => 'Poland',
        'PM' => 'Saint Pierre And Miquelon',
        'PN' => 'Pitcairn',
        'PR' => 'Puerto Rico',
        'PS' => 'Palestinian Territory, Occupied',
        'PT' => 'Portugal',
        'PW' => 'Palau',
        'PY' => 'Paraguay',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RS' => 'Serbia',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'SA' => 'Saudi Arabia',
        'SB' => 'Solomon Islands',
        'SC' => 'Seychelles',
        'SD' => 'Sudan',
        'SE' => 'Sweden',
        'SG' => 'Singapore',
        'SH' => 'Saint Helena',
        'SI' => 'Slovenia',
        'SJ' => 'Svalbard And Jan Mayen',
        'SK' => 'Slovakia',
        'SL' => 'Sierra Leone',
        'SM' => 'San Marino',
        'SN' => 'Senegal',
        'SO' => 'Somalia',
        'SR' => 'Suriname',
        'ST' => 'Sao Tome And Principe',
        'SV' => 'El Salvador',
        'SY' => 'Syrian Arab Republic',
        'SZ' => 'Swaziland',
        'TC' => 'Turks And Caicos Islands',
        'TD' => 'Chad',
        'TF' => 'French Southern Territories',
        'TG' => 'Togo',
        'TH' => 'Thailand',
        'TJ' => 'Tajikistan',
        'TK' => 'Tokelau',
        'TL' => 'Timor-Leste',
        'TM' => 'Turkmenistan',
        'TN' => 'Tunisia',
        'TO' => 'Tonga',
        'TR' => 'Turkey',
        'TT' => 'Trinidad And Tobago',
        'TV' => 'Tuvalu',
        'TW' => 'Taiwan',
        'TZ' => 'Tanzania',
        'UA' => 'Ukraine',
        'UG' => 'Uganda',
        'UM' => 'United States Outlying Islands',
        'US' => 'United States',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VA' => 'Holy See (Vatican City State)',
        'VC' => 'Saint Vincent And Grenadines',
        'VE' => 'Venezuela',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'VN' => 'Viet Nam',
        'VU' => 'Vanuatu',
        'WF' => 'Wallis And Futuna',
        'WS' => 'Samoa',
        'YE' => 'Yemen',
        'YT' => 'Mayotte',
        'ZA' => 'South Africa',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    );
}
