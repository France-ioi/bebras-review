<?php
// TODO :: Rewrite all requests to use actual JOINs

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

		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();
		for($i=0;$i<$tasks->num_rows();$i++)
		{
            if($result[$i]['ownerID'] == -1) {
    			$result[$i]['ownerName'] = $result[$i]['svnLogin'];
            } else {
    			$result[$i]['ownerName'] = $userID_to_name[$result[$i]['ownerID']];
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
		}
		
		return $result;
	}
	public function getreviews()
	{
		$reviews = $this->db->get('reviews');
		$result = $reviews->result_array();
		for($i=0;$i<$reviews->num_rows();$i++)
		{
			$tasks = $this->db->get_where('tasks',array('ID'=>$result[$i]['taskID']))->result_array();
			$result[$i]['folderName']=$tasks[0]['folderName'];
			$result[$i]['year']=$tasks[0]['year'];
			$result[$i]['countryCode']=$tasks[0]['countryCode'];
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
			
			$group = $this->db->get_where('groups',array('ID'=>$result[$i]['groupID']))->result_array();
			$result[$i]['Group']=$group[0]['name'];
			$review = $this->db->get_where('reviews',array('userID'=>$result[$i]['ID']));
			$result[$i]['Reviews']=$review->num_rows();
		}
		
		return $result;
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
		return $result[0];
	}
	public function setprofile()
	{
		//$users = $this->db->get('users');
		//$result = $users->result_array();
		//$group = $this->db->get_where('groups',array('ID'=>$result[0]['groupID']))->result_array();
		//$result[0]['Group']=$group[0]['name'];
		
		//return $result[0];
		$item=$_POST['data'];
		
		$users = $this->db->delete('users',array('ID'=>$item['ID']));
		unset($item['Group']);
		if($item['flag1']!=""&&$item['flag2']!=""&&md5($item['flag1'])==$item['password'])
		{
			$item['password']=md5($item['flag2']);
		}
		unset($item['flag1']);
		unset($item['flag2']);
		$this->db->insert('users',$item);

		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username));
		$result = $users->result_array();
		$group = $this->db->get_where('groups',array('ID'=>$result[0]['groupID']))->result_array();
		$result[0]['Group']=$group[0]['name'];
		$result[0]['flag1']="";
		$result[0]['flag2']="";
		return $result[0];

		//$result = $users->result_array();
		//$group = $this->db->get_where('groups',array('ID'=>$result[0]['groupID']))->result_array();
		//$result[0]['Group']=$group[0]['name'];
		//return $result[0];
	}

	

	public function getgeneral()
	{
		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();

		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username))->result_array();

		for($i=0;$i<$tasks->num_rows();$i++)
		{	
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
		$item=$_POST['data'];

		$this->db->update('tasks',array('htmlFileName'=>$item['htmlFileName'],'pdfFileName'=>$item['pdfFileName'],'odtFileName'=>$item['odtFileName'],'assignedGroupID'=>$item['assignedGroupID'],'status'=>$item['status'],'statusComment'=>$item['statusComment'],'ownerComment'=>$item['ownerComment']),array('ID'=>$item['ID']));

		return $item;
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
		$this->db->update('reviews',array('comment'=>$_POST['comment']),array('ID'=>$_POST['id']));
		$this->db->update('reviews',array('currentRating'=>$_POST['a']),array('ID'=>$_POST['id']));
		$this->db->update('reviews',array('potentialRating'=>$_POST['b']),array('ID'=>$_POST['id']));
		$this->db->update('reviews',array('lastChangeReviewDate'=>date('y-m-d')),array('ID'=>$_POST['id']));
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
			$reviews = $this->db->get_where('reviews', array('taskID'=>$result[$i]['ID']));
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
		$this->db->update('users',array($_POST['member']=>$_POST['data']),array('ID'=>$_POST['id']));
		return $_POST;
	}

	public function gethtml()
	{
		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();
		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('username'=>$username))->result_array();
		$result['autoLoadTasks']=$users[0]['autoLoadTasks'];
		$result['localCheckoutFolder']=$users[0]['localCheckoutFolder'];
		for($i=0;$i<$tasks->num_rows();$i++)
		{
			$result[$i]['link'] = $this->config->item('svn_reldir') . $result[$i]['folderPath'] . '/' . $result[$i]['folderName'] . '/' . $result[$i]['htmlFileName'];
			$result[$i]['otherlink']=$users[0]['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['htmlFileName'];
			if($result[$i]['htmlFileName']==NULL)
			{
				$result[$i]['link']="";
				$result[$i]['otherlink']="";
			}
		}
		
		return $result;
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
}
