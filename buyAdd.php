<?php 
require_once "model/cookie.php";
checkValidate();
$user=$_SESSION['user'];
$uid=$_SESSION['uid'];

require_once "model/userService.class.php";
$userService=new userService();
$basic=$userService->getFct($uid);

// require_once "model/repairService.class.php";
// $repairService=new repairService();

require_once "model/gaugeService.class.php";
$gaugeService=new gaugeService();
// 获取测量记录部门编号
$cljl=$gaugeService->getCLJL($basic['did']);

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

<title>备件申报明细-仪表管理</title>

<!-- Bootstrap core CSS -->
<style type="text/css">
    .basic{
      border-bottom: 1px solid #CCCCCC;
      padding:5px 10px 0px 10px;
    }

    form >.basic > .row > .col-md-4 > .input-group{
      margin-bottom: 15px;
    }

    .part{
      padding: 10px 0px 10px 0px;
      border-bottom: 1px solid #CCCCCC;
    }
    
    .part > .col-md-4 > .input-group{
      margin:5px;
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
// include "message.php";
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
<div class="container">
  <div class="page-header">
    <h4>　仪表备件申报明细</h4>
  </div>
<form class="form-horizontal" action="controller/gaugeProcess.php" method="post" id="buyAdd">  
  <div class="basic">
    <div class="row">
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-addon">申报时间</span>
          <input type="text" class="form-control" name="applytime" value='<?php echo date("Y-m-d H:i:s");?>' readonly>
        </div> 
        <div class="input-group">
          <span class="input-group-addon">申报分厂</span>
          <input type="text" class="form-control" name='factory' value='<?php echo "{$basic['factory']}";?>' readonly>
        </div>
      </div>
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-addon">测量记录</span>
          <input type="text" class="form-control" name="CLJL" value='CLJL-<?php echo $cljl;?>-09' readonly>
        </div> 
        <div class="input-group">
          <span class="input-group-addon">申报人员</span>
          <input type="text" class="form-control" name='user' value='<?php echo "$user";?>' readonly>
          <input type="hidden" name='uid' value='<?php echo "$uid";?>'>
        </div>
      </div>
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-addon">申报单位</span>
          <input type="text" class="form-control" name="depart" value='<?php echo "{$basic['depart']}";?>' readonly>
          <input type="hidden" name="dptid" value='<?php echo "{$basic['did']}";?>'>
        </div> 
        <div class="input-group">
          <span class="input-group-addon">备件总数</span>
          <input type="text" class="form-control" name="sprNum">
          <span class="input-group-btn">
            <button class="btn btn-default" type="button" id="sprNum">确定</button>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div id="addPart">
  <?php 
    $addHtml="";
    for ($i=1; $i < 7; $i++) { 
      $addHtml.="<div class='part row'>".
               " <div class='col-md-4'>".
               "   <div class='input-group'>".
               "     <span class='input-group-addon'>".$i.": 存货编码</span>".
               "     <input type='text' class='form-control' name='gaugeSpr[".$i."][code]'>".
               "   </div> ".
               " </div>".
               " <div class='col-md-4'> ".
               "   <div class='input-group'>".
               "     <span class='input-group-addon'>存货名称</span>".
               "     <input type='text' class='form-control' name='gaugeSpr[".$i."][name]'>".
               "   </div> ".
               " </div>".
               " <div class='col-md-4'> ".
               "   <div class='input-group'>".
               "     <span class='input-group-addon'>规格型号</span>".
               "     <input type='text' class='form-control' name='gaugeSpr[".$i."][no]'>".
               "   </div> ".
               " </div>".
               " <div class='col-md-4'>".
               "   <div class='input-group'>".
               "     <span class='input-group-addon'>　单　　位</span>".
               "     <input type='text' class='form-control' name='gaugeSpr[".$i."][unit]'>".
               "   </div>  ".
               " </div>".
               " <div class='col-md-4'>".
               "   <div class='input-group'>".
               "     <span class='input-group-addon'>数　　量</span>".
               "     <input type='text' class='form-control' name='gaugeSpr[".$i."][num]'>".
               "   </div>  ".
               " </div>".
               " <div class='col-md-4'>".
               "   <div class='input-group'>".
               "     <span class='input-group-addon'>备注描述</span>".
               "     <input type='text' class='form-control' name='gaugeSpr[".$i."][info]'>".
               "   </div>  ".
               " </div>".
               "</div>";
    }
    echo "$addHtml";
  ?>
    </div>

    <div style="text-align: center">
      <input type="hidden" name="flag" value="buyAdd">
      <button type="submit" class="btn btn-primary" style="width:200px;margin: 20px 0px">确 认 申 报</button>       
    </div>
</form>

<div class="modal fade"  id="notNum" >
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
         <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-10px"><span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body"><br/>
            <div class="loginModal">所填内容应为数字且大于零，请重新填写。</div><br/>
         </div>
         <div class="modal-footer">  
          <button type="button" class="btn btn-primary" data-dismiss="modal">关闭</button>
        </div>
    </div>
  </div>
</div>
<div class="modal fade"  id="failAdd" >
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
         <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-10px"><span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body"><br/>
            <div class="loginModal">所填信息不完整，请补充。</div><br/>
         </div>
         <div class="modal-footer">  
          <button type="button" class="btn btn-primary" data-dismiss="modal">关闭</button>
        </div>
    </div>
  </div>
</div>
</body>
<script src="bootstrap/js/jquery.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<script src="tp/bootstrap-datetimepicker.js"></script>
<script src="tp/bootstrap-datetimepicker.zh-CN.js"></script>
<script type="text/javascript">
// 提交时验证所有input的框不为空
$(document).on("click","button[type=submit]",yesAdd);
function yesAdd(){
  var all_allow=true;
  $("#buyAdd .form-control").each(function(){
    if ($(this).val()=="") {
      $('#failAdd').modal({
          keyboard: true
      });
      all_allow=false;
    }
  });
  return all_allow;
};

 //时间选择器
  $(".datetime").datetimepicker({
    format: 'yyyy-mm-dd', language: "zh-CN", autoclose: true,minView:2
  });

$(document).on("click","#sprNum",addPartByNum);
  function addPartByNum(){
    var num=$("input[name=sprNum]").val();
    if(isNaN(num) | num<=0 ){
      $('#notNum').modal({
          keyboard: true
      });
      return 0;
    }

      var $addHtml="";
      for (var i = 1; i <= num; i++) {
        $addHtml+=
        "<div class='part row'>"+
        "  <div class='col-md-4'>"+
        "    <div class='input-group'>"+
        "      <span class='input-group-addon'>"+i+": 存货编码</span>"+
        "      <input type='text' class='form-control' name='gaugeSpr["+i+"][code]'>"+
        "    </div> "+
        "  </div>"+
        "  <div class='col-md-4'> "+
        "    <div class='input-group'>"+
        "      <span class='input-group-addon'>存货名称</span>"+
        "      <input type='text' class='form-control' name='gaugeSpr["+i+"][name]'>"+
        "    </div> "+
        "  </div>"+
        "  <div class='col-md-4'> "+
        "    <div class='input-group'>"+
        "      <span class='input-group-addon'>规格型号</span>"+
        "      <input type='text' class='form-control' name='gaugeSpr["+i+"][no]'>"+
        "    </div> "+
        "  </div>"+
        "  <div class='col-md-4'>"+
        "    <div class='input-group'>"+
        "      <span class='input-group-addon'>　单　　位</span>"+
        "      <input type='text' class='form-control' name='gaugeSpr["+i+"][unit]'>"+
        "    </div>  "+
        "  </div>"+
        "  <div class='col-md-4'>"+
        "    <div class='input-group'>"+
        "      <span class='input-group-addon'>数　　量</span>"+
        "      <input type='text' class='form-control' name='gaugeSpr["+i+"][num]'>"+
        "    </div>  "+
        "  </div>"+
        "  <div class='col-md-4'>"+
        "    <div class='input-group'>"+
        "      <span class='input-group-addon'>备注描述</span>"+
        "      <input type='text' class='form-control' name='gaugeSpr["+i+"][info]'>"+
        "    </div>  "+
        "  </div>"+
        "</div>";
    }
    $("#addPart").empty().append($addHtml);
  }

</script>
</html>