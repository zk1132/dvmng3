<?php 
require_once "model/cookie.php";
require_once "model/repairService.class.php";
require_once 'model/paging.class.php';
require_once 'model/gaugeService.class.php';
// require_once './model/dptService.class.php';
checkValidate();
$user=$_SESSION['user'];

$paging=new paging();
$paging->pageNow=1;
$paging->pageSize=18;
$paging->gotoUrl="buyCheck.php";
if (!empty($_GET['pageNow'])) {
  $paging->pageNow=$_GET['pageNow'];
}

$gaugeService = new gaugeService();
$gaugeService->buyCheck($paging);

// $dptService = new dptService();
// $dptAll = $dptService->getDpt();


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<meta name="description" content="普阳钢铁设备管理系统">
<meta name="author" content="安瑶">
<link rel="icon" href="img/favicon.ico">
<title>备件入厂检定-仪表管理</title>
<style type="text/css">
#apvSpr li{
    list-style: none;
    margin:10px 0px;
}

.open > th, .open > td{
  background-color:#F0F0F0;
}

th > .glyphicon-trash{
  display:none;
} 

tr:hover > th > .glyphicon-trash {
  display: inline;
}

</style>
<link rel="stylesheet" href="tp/datetimepicker.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="bootstrap/js/html5shiv.js"></script>
  <script src="bootstrap/js/respond.js"></script>
<![endif]-->
</head>
<body role="document">
<?php 
  $repairService=new repairService();
  include "message.php";
?>
<nav class="navbar navbar-inverse">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="homePage.php">设备管理系统</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li><a href="homePage.php">首页</a></li>
        <li class="active dropdown">
          <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button">设备购置 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="buyGauge.php">仪表备件申报</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">设备档案 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="usingList.php">在用设备</a></li>
             <?php if (!in_array(4,$_SESSION['funcid'])  && $_SESSION['user'] != 'admin') {
                        echo "<li role='separator' class='divider'></li><li>";
                      } 
                ?>
                <li><a href="spareList.php">备品备件</a></li>
                
                <?php if (in_array(4,$_SESSION['funcid']) || $_SESSION['user'] == 'admin') {
                        echo "<li role='separator' class='divider'></li><li><a href='devPara.php'>属性参数</a></li>";
                      } 
                ?>
          </ul>
        </li>
        <li class="dropdown">
          <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">日常巡检 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="inspStd.php">巡检标准</a></li>
            <li><a href="inspMis.php">巡检计划</a></li>
            <li class="divider">&nbsp;</li>
            <li><a href="inspList.php">巡检记录</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">维修保养 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="repPlan.php">检修计划</a></li>
            <li><a href="repMis.php">维修/保养任务</a></li>
            <li class="divider">&nbsp;</li>
            <li><a href="repList.php">维修记录</a></li>
          </ul>
        </li>
      </ul>
       <ul class="nav navbar-nav navbar-right">
       <?php if (in_array(10,$_SESSION['funcid']) || $_SESSION['user'] == 'admin') {
                      echo "<li><a href='dptUser.php'>用户管理</a></li>";
                    } 
             ?>
       
        <li class="dropdown">
        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php 
              if (empty($user)) {
                echo "用户信息";
              }else{
                echo "$user";
              } 
            ?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">我的基本信息</a></li>
            <li><a href="#">更改密码</a></li>
            <li class="divider">&nbsp;</li>
            <li><a href="login.php">注销</a></li>
          </ul>
          </li>
      </ul>

    </div><!--/.nav-collapse -->
  </div>
</nav>

<!-- 审核弹出框 -->
<div class="modal fade" id="checkSpr">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">备件入厂检定</h4>
      </div>
      <form class="form-horizontal" action="controller/gaugeProcess.php" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label class="col-sm-4 control-label">合格数量：</label>
            <div class="col-sm-7">
                <div class="input-group input-group-sm" style="width:80%">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" id="numMinus"><span class="glyphicon glyphicon-minus"></span></button>
                  </span>
                  <input type="text" class="form-control" name='num' readonly="readonly" value='0' style="text-align: right;">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" id="numPlus"><span class="glyphicon glyphicon-plus"></span></button>
                  </span>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="flag" value="checkSpr">
            <input type="hidden" name="id">
            <!-- 2为合格，3为不合格 -->
            <input type="hidden" name="checkRes" value='3'>
            <button class="btn btn-primary" id="yesCheckSpr">确认</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
          </div>
          </div>
        </form>
    </div>
  </div>
</div>


<!-- 精度必须是数字 -->
<div class="modal fade"  id="failAccuracy" >
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
         <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-10px"><span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body"><br/>
            <div class="loginModal">精度等级必须数字。</div><br/>
         </div>
         <div class="modal-footer">  
          <button type="button" class="btn btn-primary" data-dismiss="modal">关闭</button>
        </div>
    </div>
  </div>
</div>


<div class="container">
  <div class="row">
  <div class="col-md-10">
    <div class="page-header">
        <h4>　仪表备件入厂检定</h4>
    </div>
    <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>存货编码</th><th>存货名称</th><th>规格型号</th><th>申报部门</th>
            <th>申报总数</th><th>已检定</th><th>未检定</th><th style="width:4%"></th>
          </tr>
        </thead>
        <tbody class="tablebody">
        <?php 
          if (count($paging->res_array) == 0) {
            echo "<tr><td colspan=12>当前无新的入厂检定任务</td></tr>";
          }
          for ($i=0; $i < count($paging->res_array); $i++) { 
            $row = $paging->res_array[$i];
            $renum = $row['num'] - $row['checknum'];
            $addHtml = 
            "<tr>
                <td>{$row['code']}</td>
                <td><a href='javascript:flowInfo({$row['id']})'>{$row['name']}</td>
                <td>{$row['no']}</td>
                <td>{$row['factory']}{$row['depart']}</td>
                <td>{$row['num']} {$row['unit']}</td>
                <td>{$row['checknum']} {$row['unit']}</td>
                <td>$renum {$row['unit']}</td>
                <td><a class='glyphicon glyphicon-check' href='javascript:sprCheck({$row['id']},\"{$row['name']}\",\"{$row['no']}\",\"{$row['num']}\");'></a></td>
             </tr>";
             echo "$addHtml";

          }
        ?>
        </tbody>
        </table>
        <div class='page-count'><?php echo $paging->navi?></div>                    
    </div>
    <div class="col-md-2">
    <div class="col-md-3">
    <?php  include "buyNavi.php";?>
    </div>
    </div>
</div>
</div>








<script src="bootstrap/js/jquery.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="tp/bootstrap-datetimepicker.js"></script>
<script src="tp/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="bootstrap/js/bootstrap-suggest.js"></script>
<?php  include "./buyJs.php";?>
<script type="text/javascript">
// 入账的备件数目加
$("#checkSpr #numPlus").click(function(){
  var num = parseInt($("#checkSpr input[name=num]").val());
  var max = $(this).attr("max");
  if (num != max) {
    num++;
    $("#checkSpr input[name=num]").val(num);
  }
});

// 入账的备件数目减
$("#checkSpr #numMinus").click(function(){
  var num = parseInt($("#checkSpr input[name=num]").val());
  if (num != 0) {
    num--;
    $("#checkSpr input[name=num]").val(num);
  }
});


// 部门搜索提示
 $("#addSprInfo input[name=nDptCk]").bsSuggest({
    allowNoKeyword: false,
    showBtn: false,
    indexId:2,
    // indexKey: 1,
    data: {
         'value':<?php  echo "$dptAll"; ?>,
    }
}).on('onDataRequestSuccess', function (e, result) {
    console.log('onDataRequestSuccess: ', result);
}).on('onSetSelectValue', function (e, keyword, data) {
   console.log('onSetSelectValue: ', keyword, data);
   var idDepart=$(this).attr("data-id");
   $(this).parents("form").find("input[name=dptCk]").val(idDepart);
}).on('onUnsetSelectValue', function (e) {
    console.log("onUnsetSelectValue");
});


// 确定添加检定信息到mysql中
$("#yesAddInfo").click(function(){
  var allow_submit = true;

  // 精度必须是个数字
  var accuracy = $("#addSprInfo input[name=accuracy]").val();
  if(isNaN(parseFloat(accuracy)) && accuracy.length !=0){
    $("#failAccuracy").modal({
      keyboard:true
    });
    return false;
  }

  // 所有input不得为空
  $("#addSprInfo input[type=text]").each(function(){
    if ($(this).val() == "") {
      $("#failAdd").modal({
        keyboard:true
      });
      var allow_submit = false;
    }
  });

  if (allow_submit == true) {
    $.get("./controller/gaugeProcess.php",$("#addSprForm").serialize(),function(data,success){
      // 添加成功，关闭两个框
      if (data !=0 ) {
        // $('#addSprInfo, #checkSpr').modal('hide');
        location.href="./buyCheck.php";
      }
    },'text');
  }

  
});

//时间选择器
$(".datetime").datetimepicker({
  format: 'yyyy-mm-dd', language: "zh-CN", autoclose: true,minView:2,
});

$("#yesCheckSpr").click(function(){
  var max = $("#numPlus").attr('max');
  var num = $("#checkSpr input[name=num]").val();
  var sprId = $("#checkSpr input[name=id]").val();
  var allow_submit = false;
  if(num == 0){
    // 全都不合格
    allow_submit = true;
  }else{
    location.href="./buyCheckAdd.php?id="+sprId+"&num="+num;
  }
  return allow_submit;
});

// 检定弹出框
function sprCheck(id,name,no,num){
  $("#checkSpr input[name=id]").val(id);
  $("#numPlus").attr('max',num);
  $("#checkSpr").modal({
    keyboard:true
  });
}


    </script>
  </body>
</html>