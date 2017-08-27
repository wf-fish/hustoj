<?php require("admin-header.php");

if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}?>
<?php if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	if (isset($_POST['rjpid'])){
		$rjpid=intval($_POST['rjpid']);
		$sql="UPDATE `solution` SET `result`=1 WHERE `problem_id`=?";
		pdo_query($sql,$rjpid) ;
		$sql="delete from `sim` WHERE `s_id` in (select solution_id from solution where `problem_id`=?)";
		pdo_query($sql,$rjpid) ;
		$url="../status.php?problem_id=".$rjpid;
		echo "Rejudged Problem ".$rjpid;
		echo "<script>location.href='$url';</script>";
	}
	else if (isset($_POST['rjsid'])){
		$rjsid=intval($_POST['rjsid']);
		$sql="delete from `sim` WHERE `s_id`=?";
		pdo_query($sql,$rjsid) ;
		$sql="UPDATE `solution` SET `result`=1 WHERE `solution_id`=?";
		pdo_query($sql,$rjsid) ;
		$url="../status.php?top=".($rjsid+1);
		echo "Rejudged Runid ".$rjsid;
		echo "<script>location.href='$url';</script>";
	}else if (isset($_POST['rjcid'])){
		$rjcid=intval($_POST['rjcid']);
		$sql="UPDATE `solution` SET `result`=1 WHERE `contest_id`=?";
		pdo_query($sql,$rjcid) ;
		$url="../status.php?cid=".($rjcid);
		echo "Rejudged Contest id :".$rjcid;
		echo "<script>location.href='$url';</script>";
	}
	echo str_repeat(" ",4096);
	if($OJ_REDIS){
           $redis = new Redis();
           $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);
	   if(isset($OJ_REDISAUTH)) $redis->auth($OJ_REDISAUTH);
                $sql="select solution_id from solution where result=1 and problem_id>0";
                 $result=pdo_query($sql);
                 foreach($result as $row){
                        echo $row['solution_id']."\n";
                        $redis->lpush($OJ_REDISQNAME,$row['solution_id']);
                }
           $redis->close();     
        }

}
?>
<b>Rejudge</b>
	<ol>
	<li>Problem
	<form action='rejudge.php' method=post>
		<input type=input name='rjpid'>	<input type='hidden' name='do' value='do'>
		<input type=submit value=submit>
		<?php require_once("../include/set_post_key.php");?>
	</form>
	<li>Solution
	<form action='rejudge.php' method=post>
		<input type=input name='rjsid'>	<input type='hidden' name='do' value='do'>
		<input type=hidden name="postkey" value="<?php echo $_SESSION['postkey']?>">
		<input type=submit value=submit>
	</form>
	<li>Contest
	<form action='rejudge.php' method=post>
		<input type=input name='rjcid'>	<input type='hidden' name='do' value='do'>
		<input type=hidden name="postkey" value="<?php echo $_SESSION['postkey']?>">
		<input type=submit value=submit>
	</form>
