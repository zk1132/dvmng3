<?php
require_once 'sqlHelper.class.php';
class userService{
	function checkUser($code,$password){
		$sqlHelper=new sqlHelper();
		$sql="SELECT psw,name,id from user where code='$code'";
		$bsc=$sqlHelper->dql($sql);

		// 用户名不存在 用户名不正确
		if (empty($bsc)) {
			$res = 1;
		}else if ( $bsc['psw'] != $password) {
			$res = 2;
		}else{
			// 存在该用户查询其权限，和管辖范围
			$sql = "SELECT staff_role_func.fid funcid
					from staff_user_role
					left join staff_role_func
					on staff_user_role.rid=staff_role_func.rid
					where uid = {$bsc['id']}";
			$funcid = $sqlHelper->dql_arr($sql);
			$_SESSION['funcid'] = array_column($funcid,'funcid');
		
			$sql = "SELECT dptid from staff_user_dpt where uid={$bsc['id']}";
			$dptid = $sqlHelper->dql_arr($sql);
			$_SESSION['dptid'] = array_column($dptid,'dptid');

			$_SESSION['user'] = $bsc['name'];
			$_SESSION['uid'] = $bsc['id'];
			$sqlHelper->close_connect();
			
			$res = 3;
		}
		return $res;
	}

	// 查询当前用户所在分厂
	function getFct($uid){
		$sqlHelper=new sqlHelper();
		$sql="SELECT depart.fid,factory.depart as factory,depart.id as did,depart.depart
			  from user 
			  left join depart
			  on user.departid=depart.id
			  LEFT JOIN depart as factory 
			  on factory.id=depart.fid
			  where user.id=$uid";
		$res=$sqlHelper->dql($sql);
		 // [fid] => [factory] => [did] => 1 [depart] => 新区竖炉
		 if (empty($res['fid'])) {
		 	$res['factory'] = $res['depart'];
		 	$res['depart'] = "分厂级";
		 }
		$sqlHelper->close_connect();
		return $res;
	}
	
	
}
?>
