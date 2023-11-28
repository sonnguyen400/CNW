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


    


    //General Service
    function update($tablename,$property,$value,$predicate){
        global $conn;
        $updateQuery="update $tablename set $property='$value' where $predicate";
        mysqli_query($conn,$updateQuery);
    }
    function updateAll($tablename,$keyValue,$predicate){
        global $conn;
        $kV="";
        foreach ($keyValue as $key => $value) {
            $kV=$kV."  "."$key='$value'";
        }
        $updateQuery="update $tablename set $kV where $predicate";
        mysqli_query($conn,$updateQuery);
    }
    function insert($tablename,$object){
        global $conn;
        $keys="";
        $values="";
        foreach ($object as $key => $value) {
            $keys=$key.",".$keys;
            $values="'$value',".$values;
        }
        $keys=substr($keys, 0, -1);
        $values=substr($values, 0, -1);
        $query ="insert into $tablename($keys) value($values)";
        if(mysqli_query($conn,$query)){
            return mysqli_insert_id($conn);
        }
        return false;
    }
    //Answer Service
    function insertAns($quesId,$ans,$isTrue){
        global $conn;
        $object=Array(
            "ques_id"=>$quesId,
            "ans"=>$ans,
            "isTrue"=>$isTrue
        );
        return insert("answer",$object);
    }
    function getAnsByQuesId($quesId){
        
    }
    //Question Service
    function insertQues($user_id,$course_id,$quesType,$ques,$file){
        global $conn;
        $object=Array(
            "user_id"=>$user_id,
            "course_id"=>$course_id,
            "ques_type"=>$quesType,
            "ques"=>$ques
        );
        $id=insert("question",$object);
        $fileok=false;
        if($id!=false&&isset($file)){
            $fileok=createFile($file,$id);
            if($fileok!=false){
                update("question","imgpath",$fileok,"id=$id");
            }
        }
        
        return Array(
            "id"=>$id,
            "file"=>$fileok
        );
    }






    ////Utils
    function errorMessage($errMessage){
        return "<div class='alert alert-warning text-center' role='alert'>".(isset($errMessage)?$errMessage:"Thao tác hất bại")."</div>";
    }
    function successMessage($successMessage){
        return "<div class='alert alert-success text-center' role='alert'>".(isset($successMessage)?$successMessage:"Thao tác thành công")."</div>";
    }
    //File handler
    function createFile($file,$quesId){
        if(isset($file)&&$file['error']==0){
            $ex=pathinfo($file['name'],PATHINFO_EXTENSION);
            $path="../images/$quesId.$ex";
            move_uploaded_file($file["tmp_name"],$path);
            return $path;
        }
        return false;
    }
?>

  