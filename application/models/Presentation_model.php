<?php
class presentation_model extends CI_Model {
	public function __construct()
	{
		$this->load->database();
	}
	public function getdata()
	{
		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();
		for($i=0;$i<$tasks->num_rows();$i++)
		{
			
			$group = $this->db->get_where('groups',array('ID'=>$result[$i]['assignedGroupID']))->result_array();
			$result[$i]['Group']=$group[0]['name'];
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
			$group = $this->db->get_where('groups',array('ID'=>$tasks[0]['assignedGroupID']))->result_array();
			$result[$i]['Group']=$group[0]['name'];
			$user = $this->db->get_where('users',array('ID'=>$result[$i]['userID']))->result_array();
			$result[$i]['author']=$user[0]['firstName'];
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
		$users = $this->db->get_where('users',array('firstName'=>$username));
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
		$users = $this->db->get_where('users',array('firstName'=>$username));
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
		$users = $this->db->get_where('users',array('firstName'=>$username))->result_array();

		for($i=0;$i<$tasks->num_rows();$i++)
		{	
			$group = $this->db->get_where('groups',array('ID'=>$result[$i]['assignedGroupID']))->result_array();
			$result[$i]['Group']=$group[0]['name'];
			
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
		
		$users = $this->db->delete('tasks',array('ID'=>$item['ID']));
		unset($item['Group']);
		unset($item['Reviews']);
		unset($item['ar']);
		unset($item['p']);
		unset($item['authorflag']);
		unset($item['groupadminflag']);
		unset($item['adminflag']);
		
		$this->db->insert('tasks',$item);

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
		$users = $this->db->get_where('users',array('firstName'=>$username))->result_array();
		$reviews = $this->db->get_where('reviews',array('userID'=>$users[0]['ID']));
		$result = $reviews->result_array();
		return $result;
	}
	public function reviewchange()
	{
		$reviews = $this->db->get_where('reviews',array('ID'=>$_POST['ID']));
		$this->db->update('reviews',array('comment'=>$_POST['comment']),array('ID'=>$_POST['id']));
		return $result;
	}
	
	public function getall()
	{
		$reviews = $this->db->get('reviews');
		$count=$reviews->num_rows();
		$result = $reviews->result_array();
		$re['count']=$count;
		$sum1=0;
		$sum2=0;
		for($i=0;$i<$count;$i++)
		{	
			//$user = $this->db->get_where('users',array('ID'=>$result[$i]['userID']))->result_array();
			$user = $this->db->get_where('users',array('ID'=>$result[$i]['userID']))->result_array();
			$result[$i]['author']=$user[0]['firstName'];
			$sum1+=$result[$i]['currentRating'];
			$sum2+=$result[$i]['potentialRating'];
		}
		$re['ar']=number_format($sum1/$count,1);
		$re['p']=number_format($sum2/$count,1);
		$re['data']=$result;
		return $re;
	}
	public function getmessage()
	{
		$username = ($this->session->userdata['logged_in']['username']);
		$user = $this->db->get_where('users',array('firstName'=>$username))->result_array();
		$f=$user[0]['role']=='Admin';
		$reviews = $this->db->get('messages');
		$count=$reviews->num_rows();
		$result = $reviews->result_array();
		$sum1=0;
		$sum2=0;
		for($i=0;$i<$count;$i++)
		{	
			$user = $this->db->get_where('users',array('ID'=>$result[$i]['userID']))->result_array();
			$result[$i]['author']=$user[0]['firstName'];
			$result[$i]['flag']=($user[0]['firstName']==$username);
			$result[$i]['flag1']=($user[0]['firstName']==$username||$f);
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
		$users = $this->db->get_where('users',array('firstName'=>$username))->result_array();
		$item=array('taskID'=>$_POST['taskID'], 'userID'=>$users[0]['ID'], 'content'=>$_POST['mess'], 'dateCreated'=>date('y-m-d'), 'dateModified'=>date('y-m-d'));
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
		$users = $this->db->get_where('users',array('firstName'=>$username))->result_array();
		$result['autoLoadTasks']=$users[0]['autoLoadTasks'];
		$result['localCheckoutFolder']=$users[0]['localCheckoutFolder'];
		for($i=0;$i<$tasks->num_rows();$i++)
		{
			$result[$i]['link']="/bebras-review/SVN/".$result[$i]['folderName']."/".$result[$i]['htmlFilename']."-eng.html";
			$result[$i]['otherlink']=$users[0]['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['htmlFilename']."-eng.html";
			if(substr($users[0]['localCheckoutFolder'],0,4)!="http")
				$result[$i]['otherlink']="";
		}
		
		return $result;
	}

	public function getpdf()
	{
		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();
		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('firstName'=>$username))->result_array();
		$result['autoLoadTasks']=$users[0]['autoLoadTasks'];
		$result['localCheckoutFolder']=$users[0]['localCheckoutFolder'];
		for($i=0;$i<$tasks->num_rows();$i++)
		{
			$result[$i]['link']="/bebras-review/SVN/".$result[$i]['folderName']."/".$result[$i]['pdfFileName']."-eng.html";
			$result[$i]['otherlink']=$users[0]['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['pdfFileName']."-eng.html";
			if(substr($users[0]['localCheckoutFolder'],0,4)!="http")
				$result[$i]['otherlink']="";
		}
		
		return $result;
	}

	public function getodt()
	{
		$tasks = $this->db->get('tasks');
		$result = $tasks->result_array();
		$username = ($this->session->userdata['logged_in']['username']);
		$users = $this->db->get_where('users',array('firstName'=>$username))->result_array();
		$result['autoLoadTasks']=$users[0]['autoLoadTasks'];
		$result['localCheckoutFolder']=$users[0]['localCheckoutFolder'];
		for($i=0;$i<$tasks->num_rows();$i++)
		{
			$result[$i]['link']="/bebras-review/SVN/".$result[$i]['folderName']."/".$result[$i]['odtFileName']."-eng.html";
			$result[$i]['otherlink']=$users[0]['localCheckoutFolder']."/".$result[$i]['folderName']."/".$result[$i]['odtFileName']."-eng.html";
			if(substr($users[0]['localCheckoutFolder'],0,4)!="http")
				$result[$i]['otherlink']="";
		}
		
		return $result;
	}
	
}