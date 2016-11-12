<?php  
require_once '../model/gaugeService.class.php';
require_once '../model/userService.class.php';
header("content-type:text/html;charset=utf-8");
$gaugeService=new gaugeService();
if (!empty($_REQUEST['flag'])) {
	$flag=$_REQUEST['flag'];
	if ($flag=="buyAdd") {
		$CLJL=$_POST['CLJL'];
		$applytime=$_POST['applytime'];
		$dptid=$_POST['dptid'];
		$gaugeSpr=$_POST['gaugeSpr'];
		$uid=$_POST['uid'];

		$res=$gaugeService->buyAdd($CLJL,$applytime,$dptid,$gaugeSpr,$uid);

		if ($res!=0) {
			header("location: ../buyApply.php");
			exit();
		}else{
			echo "添加失败。";
			exit();
		}
	}

	// 根据备件申报的基本信息id获取详细信息，用于展开
	else if($flag=="getBuyDtl"){
		$basic=$_GET['id'];
		$res = $gaugeService->getBuyDtl($basic);
		echo "$res";
		exit();
	}

	// 获取单个申报备件的信息
	else if ($flag=="getSprDtl") {
		$id = $_GET['id'];
		$res = $gaugeService->getSprDtl($id);
		echo "$res";
		exit();
	}

	// 修改单个备件申报信息
	else if ($flag=="uptSprById") {
		$code = $_POST['code'];
		$id = $_POST['id'];
		$info = $_POST['info'];
		$name = $_POST['name'];
		$no = $_POST['no'];
		$num = $_POST['num'];
		$unit = $_POST['unit'];

		$res = $gaugeService->uptSprById($code,$id,$info,$name,$no,$num,$unit);

		if ($res!=0) {
			header("location: ../buyApply.php");
			exit();
		}else{
			echo "修改失败。";
			exit();
		}
	}

	// 删除单个备件申报信息
	else if ($flag=="delSprById") {
		$id = $_GET['id'];

		$res = $gaugeService->delSprById($id);
		if ($res!=0) {
			header("location: ../buyApply.php");
			exit();
		}else{
			echo "删除失败。";
			exit();
		}
	}

	// 删除某一备件申报列表
	else if ($flag=="delBuy") {
		$id = $_GET['id'];
		$res = $gaugeService->delBuy($id);
		if ($res!=0) {
			header("location: ../buyApply.php");
			exit();
		}else{
			echo "删除失败。";
			exit();
		}
	}

	else if ($flag == "check") {
		$pro = $_POST['pro'];
		$phase = $_POST['phase'];
		$res = $gaugeService->checkAuth($pro, $phase);
		echo "$res";
		exit();
	}

	// 审核备件申报
	else if ($flag == "apvBuy") {
		$apvInfo = $_POST['apvInfo'];	
		$apvRes = $_POST['apvRes'];	
		$id = $_POST['id'];
		$res = $gaugeService->apvBuy($apvInfo,$apvRes,$id);
		if ($res == 0) {
			header("location: ./../buyApv.php");
		}else{
			echo "操作失败,请联系管理员";
		}
	}

	// 入厂检定
	else if ($flag == "checkSpr") {
		// 入厂检定全部为不合格
		$checkRes = $_POST['checkRes'];
		$checkTime = date("Y-m-d H:i:s");
		$id = $_POST['id'];
		$res = $gaugeService->checkSpr($id,$checkRes,$checkTime);
		if ($res != 0) {
			header("location: ./../buyCheck.php");
		}else{
			echo "操作失败,请联系管理员";
		}
	}

	// 存库入账
	else if($flag == "storeSpr"){
		$id = $_POST['id']; 
		$storeRes = $_POST['storeRes']; 
		$storeTime = date("Y-m-d H:i:s");
		$num = $_POST['num'];
		$res = $gaugeService->storeSpr($id, $storeRes, $storeTime, $num);
		if ($res != 0) {
			header("location: ./../buyStore.php");
			exit();
		}else{
			echo "操作失败，请联系管理员";
			exit();
		}
	}

	// 安装验收备件，在dtl表中添加devid和installtime
	else if($flag == "useSpr"){
		$dateInstall = $_POST['dateInstall'];
		$pid = $_POST['pid'];
		$number = $_POST['number'];
		$para = $_POST['para'];
		$sprId = $_POST['sprId'];

		// 先添加备件基本信息
		$devId = $gaugeService->transSpr($sprId,$number,'正常',$pid,$dateInstall);
		$res = $gaugeService->useDtl($devId,$para);

	}

	else if ($flag == "addSprInCk") {
		$sprId = $_POST['sprId'];
		$check = $_POST['check'];
    	$time = date("Y-m-d H:i:s"); 
		// 将合格数量的信息添加到check表当中去
    	$res[] = $gaugeService->addSprInCk($sprId,$check,$time); 
		// 将dtl表的sprid修改check信息
		$res[] = $gaugeService->checkSpr($sprId,2,$time);
		if (!in_array(0,$res)) {
			header("location: ./../buyCheckHis.php");
		}else{
			echo "操作失败。";
		}
	}

	// // 获取单个备件的入场检定信息
	// else if ($flag == "getCkInfo") {
	// 	$sprId = $_GET['sprId'];
	// 	$res = $gaugeService->getCkInfo($sprId);
	// 	echo "$res";
	// 	exit();
	// }

	// 库存在领取
	else if ($flag == "takeSpr") {
		$dptId = $_POST['dptId'];
		$sprId = $_POST['id'];
		$num = $_POST['num'];
		$takeTime = date("Y-m-d H:i:s");
		$res = $gaugeService->takeSpr($sprId, $takeTime, $num,$dptId);
		if ($res != 0) {
			header("location: ./../buyStoreHouse.php");
			exit();
		}else{
			echo "操作失败。";
			exit();
		}
	}

	// 查看库存入库、领取、再领取的时间
	else if ($flag == "getStoreInfo") {
		$sprId = $_GET['sprId'];
		$res = $gaugeService->getStoreInfo($sprId);
		echo "$res";
		exit();
	}

	else if ($flag == "spareSpr") {
		$num = $_GET['num'];
		$id = $_GET['id'];
		$res = $gaugeService->transSpr($id,$num,'备用',0,0);
		echo "$res";
		exit();
	}

	else if ($flag == "endSpr") {
		$id = $_GET['id'];
		$installtime = date("Y-m-d");
		$res = $gaugeService->endSpr($id,$installtime);
		if ($res != 0) {
			header("location: ./../buyInstall.php");
			exit();
		}else{
			echo "操作失败。";
			exit();
		}
	}

	else if ($flag == "installXls") {
		$devId = $_GET['devid'];
		$res = $gaugeService->installXls($devId);
		echo "$res";
		exit();
	}

	else if ($flag == "installInfo") {
		$conclude = $_POST['conclude'];
		$installInfo = $_POST['installInfo'];
		$location = $_POST['location'];
		$paraInfo = $_POST['paraInfo'];
		$runInfo = $_POST['runInfo'];
		$devId = $_POST['devId'];

		$res = $gaugeService->installInfo($conclude,$installInfo,$location,$paraInfo,$devId,$runInfo);
		if ($res != 0) {
			header("location: ./../buyInstallHis.php");
			exit();
		}else{
			echo "操作失败。";
			exit();
		}
	}

	else if ($flag == "flowInfo") {
		$id =$_GET['id'];
		$res = $gaugeService->getFlowInfo($id);
		echo "$res";
		exit();
	}

	else if($flag == "seeSpr"){
		$sprId = $_GET['sprId'];
		$res = $gaugeService->seeSpr($sprId);
		echo "$res";
		exit();
	}

	else if ($flag == "getCkInfo") {
		$checkTime = $_GET['checktime'];
		$sprid = $_GET['sprid'];
		$res = $gaugeService->getCkInfo($checkTime,$sprid);
		echo "$res";
		exit();
	}


}
?>