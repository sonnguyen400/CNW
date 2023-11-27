<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
        session_start();
    }
	include 'connectdb.php';
    // Begin Login function
	function isLogin(){
		return isset($_SESSION['userid']);
	}
    // begin checkLogin
    function checkLogin($username, $password)
	{
        global $conn;
		$encodedPass=md5($password);
        $query="Select * from user where username='$username' and password='$encodedPass'";
        $result=mysqli_query($conn,$query);
        if(mysqli_num_rows($result)==0){
            header("Location:"."./dang_nhap.php?status=loginfail");
        }else{
            $user=mysqli_fetch_assoc($result);
            $_SESSION["userid"]=$user['id'];
            header("Location:"."../src/khoa_hoc.php");
        }
	}
    // end checkLogin
?>

  