<?php

if(isset($_GET['refid'])) $refid = $_GET['refid'];
else $refid = 0;
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
if(isset($_GET['show'])) $show = $_GET['show'];
else $show = 0;

$servername = "localhost";
$username = "root";
$password = "tU0BXrD4YxsI";
$db = "bitnami_wordpress";
$conn = new mysqli($servername, $username, $password, $db);
if($conn->connect_error){die("Connection failed:".$conn->connect_error );}

if($show == 1){
  echo 'Username:'.$current_user->user_login.'<br/>';
  echo 'User email: ' . $current_user->user_email . '<br />';
  echo 'User first name: ' . $current_user->user_firstname . '<br />';
  echo 'User last name: ' . $current_user->user_lastname . '<br />';
  echo 'User display name: ' . $current_user->display_name . '<br />';
  echo 'User ID: ' . $current_user->ID . '<br />';
echo 'User registered: '.$current_user->user_registered.'<br/>';
$hashcode = hash('sha256',$current_user->user_registerd.$current_user->ID );
echo $_SERVER['HTTP_X_FORWARDED_FOR']."<br>";
echo $_SERVER['REMOTE_ADDR']."<br>";
echo $_SERVER['HTTP_X_FORWARDED_SERVER'].'<br>';
echo $_SERVER['SERVER_NAME'].'<br>';

//echo $hashcode;
echo "[post_id: ".get_the_id()."]";
echo get_the_user_ip();
  }

$postid = get_the_id();
if(isset($_GET['refid'])&& $refid > 0){
$dofirststep = 1;
$allowed = 0;


//first step
if($dofirststep == 1){
$userip = get_the_user_ip();
$queryunique = "select * from my_uniqueuser where userid = $currentuserid and postid = $postid and userip= '$userip'";
//echo $queryunique.'<br>';
$res = $conn->query($queryunique);
if($res->num_rows > 0) {$allowed = 0; }
else {
$queryinsert = "insert into my_uniqueuser(userid, postid, userip ) values($currentuserid, $postid, '$userip')";
if($show == 1)echo $queryinsert;
if($conn->query($queryinsert) == TRUE){$allowed = 1;if($show == 1) echo "(q1)";}
else{ echo "[$currentuserid $postid $userip]"; $allowed = 0;}
}
}


//second step
if(($dofirststep == 1 && $allowed == 1)||($dofirststep == 0)){
$querycheck = "select user_ID from my_points where post_ID=$postid and user_ID = $refid";
$res = $conn->query($querycheck);
if($res->num_rows > 0){
$queryincrement = "update my_points set points = points + 1 
where user_ID = $refid and post_ID = $postid";
if($conn->query($queryincrement) == TRUE){if($show == 1) echo "(q2)";}else echo "qincnot";
}
else {
$queryinsertnew = "insert into my_points(user_ID, post_ID, points) values($refid,$postid, 1)";
//echo $queryinsertnew;
if($conn->query($queryinsertnew) == TRUE){if($show == 1)echo "(q3)";}else if($show==1)echo "$queryinsertnew";
}
//echo $query01;

//echo "done";
//echo "[refpoints: ".$row['refpoints']."]";
//echo "<div class='numberCircle'>Your points: ".$row['refpoints']."</div>";
}//end of second step
}//end of if

if(is_user_logged_in() && $currentuserid > 0){
$querypointcount= "select sum(points) as sumpoint from my_points 
where post_ID = $postid and user_ID = $current_user->ID";
//echo $querypointcount."<br>";
$res = $conn->query($querypointcount);

if($res->num_rows > 0){
while($row = $res->fetch_assoc()){
if($row['sumpoint'] > 0)
$p = $row['sumpoint'];
echo "<div class='numberCircle'>#$p</div>";
$p = 0;
}
}else echo "notdone";
//$postid = get_the_id();
}//end of second if
$conn->close();

?>
