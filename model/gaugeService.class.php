<?php 
header("content-type:text/html;charset=utf-8");
require_once 'sqlHelper.class.php';
require_once 'paging.class.php';
require_once 'classifyBuild.php';
class gaugeService{
	public $authWhr="";
	public $authAnd="";

	function __construct(){
		$sqlHelper=new sqlHelper();
		$upid=$_SESSION['dptid'];
		$pmt=$_SESSION['permit'];
		switch ($pmt) {
			case '0':
				$this->authWhr="";
				$this->authAnd="";
				break;
			case '1':
				$sql="select id from depart where id=$upid or path in('%-{$upid}','%-{$upid}-%')";
				$upid=$sqlHelper->dql_arr($sql);
				$upid=implode(",",array_column($upid,'id'));
				$this->authWhr=" where gauge_spr_bsc.depart in(".$upid.") ";
				$this->authAnd=" and gauge_spr_bsc.depart in(".$upid.") ";
				break;
			case '2':
				$this->authWhr=" where gauge_spr_bsc.depart=$upid ";
				$this->authAnd=" and gauge_spr_bsc.depart=$upid ";
				break;
		}
		$sqlHelper->close_connect();	
	}

	// 获取所在部门所有备件申报
	function buyBsc($paging){
		$sqlHelper = new sqlHelper();
		$sql1 = "select createtime, factory.depart as factory, depart.depart, user.name,cljl,see,gauge_spr_bsc.id
				 from gauge_spr_bsc
				 left join depart
				 on gauge_spr_bsc.depart=depart.id
				 left join depart as factory
				 on depart.fid=factory.id
				 left join user
				 on user.id=gauge_spr_bsc.user ".$this->authWhr." limit ".($paging->pageNow-1)*$paging->pageSize.",$paging->pageSize";
		$sql2 = "select count(*)
				 from gauge_spr_bsc
				 left join depart
				 on gauge_spr_bsc.depart=depart.id
				 left join depart as factory
				 on depart.fid=factory.id
				 left join user
				 on user.id=gauge_spr_bsc.user ".$this->authWhr;
		$res = $sqlHelper->dqlPaging($sql1,$sql2,$paging);
		$sqlHelper->close_connect();
	}

	// 获取测量记录部门编号
	function getCLJL($dptid){
		$sqlHelper = new sqlHelper();
		$sql="select num from gauge_dpt_num where depart = $dptid";
		$res=$sqlHelper->dql($sql);
		$sqlHelper->close_connect();
		return $res['num'];
	}

	function buyAdd($CLJL,$applytime,$dptid,$fid,$gaugeSpr,$uid){
		$sqlHelper=new sqlHelper();
		// basic depart--dptid user--user cljl--CLJL createtime----applytime
		// 将申报表基本信息加到basic表中
		$sql="insert into gauge_spr_bsc (depart, user, cljl, createtime) values ($dptid, $uid, '{$CLJL}', '{$applytime}')";
		$res=$sqlHelper->dml($sql);
		$bscid=mysql_insert_id();
		$sql="insert into gauge_spr_dtl (code,name,no,unit,num,info,basic,res) values ";
		for ($i=1; $i <= count($gaugeSpr); $i++) { 
			// [存货编号 code] => 510740110018 [ name 名称] => 超声波流量计 [规格型号 no ] => TJZ-100B [unit 单位] => 个 [数量 num ] => 3 [备注描述 info] => 无
			if ($i != count($gaugeSpr)) {
				$sql .= "('{$gaugeSpr[$i][0]}', '{$gaugeSpr[$i][1]}', '{$gaugeSpr[$i][2]}', '{$gaugeSpr[$i][3]}', {$gaugeSpr[$i][4]}, '{$gaugeSpr[$i][5]}', $bscid, 0), ";
			}else{
				$sql .= "('{$gaugeSpr[$i][0]}', '{$gaugeSpr[$i][1]}', '{$gaugeSpr[$i][2]}', '{$gaugeSpr[$i][3]}', {$gaugeSpr[$i][4]}, '{$gaugeSpr[$i][5]}', $bscid, 0) ";
			}
		}
		$res=$sqlHelper->dml($sql);
		$sqlHelper->close_connect();
		return $res;
	}
	// 根据备件申报的基本信息id获取详细信息，用于展开
	function getBuyDtl($basic){
		$sqlHelper = new sqlHelper();
		$sql = "select code,id,info,name,no,num,unit,see from gauge_spr_dtl where basic = $basic";
		$res=$sqlHelper->dql_arr($sql);
		$res=json_encode($res,JSON_UNESCAPED_UNICODE);
		$sqlHelper->close_connect();
		return $res;
	}

	function getSprDtl($id){
		$sqlHelper = new sqlHelper();
		$sql="select * from gauge_spr_dtl where id = $id";
		$res = $sqlHelper->dql($sql);
		$res = json_encode($res,JSON_UNESCAPED_UNICODE);
		$sqlHelper->close_connect();
		return $res;
	}

	function uptSprById($code,$id,$info,$name,$no,$num,$unit){
		$sqlHelper = new sqlHelper();
		$sql = "update gauge_spr_dtl set code='{$code}',info='{$info}',name='{$name}',no='{$no}',num='{$num}',unit='{$unit}' where id=$id";
		$res = $sqlHelper->dml($sql);
		$sqlHelper->close_connect();
		return $res;
	}

	// 删除单个备件申报信息
	function delSprById($id){
		$sqlHelper = new sqlHelper();
		$sql = "delete from gauge_spr_dtl where id = $id";
		$res = $sqlHelper->dml($sql);
		$sqlHelper->close_connect();
		return $res;
	}

	function delBuy($id){
		$sqlHelper = new sqlHelper();
		$sql = "delete from gauge_spr_bsc where id = $id";
		$res = $sqlHelper->dml($sql);
		$sqlHelper->close_connect();
		return $res;
	}
}
?>