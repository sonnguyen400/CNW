<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
        session_start();
    }
	include 'connectdb.php';
    const ADMIN="ADMIN";
    const USER="USER";
    const TRAC_NGHIEM_1DA="Trắc nghiệm 1 đáp án";
    const TRAC_NGHIEM_nDA="Trắc nghiệm nhiều đáp án";
    const CAUHOI_DIEN="Câu hỏi điền";
    const CHUA_DUYET="Chưa duyệt";
    const DA_DUYET="Đã duyệt";




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
            return false;
        }else{
            $user=mysqli_fetch_assoc($result);
            $_SESSION["userid"]=$user['id'];
            $_SESSION["role"]=$user['role'];
            header("Location:"."../src/khoa_hoc.php");
            return true;
        }
	}
    // end checkLogin


    //Component
    function input($type,$name,$value,...$properties){
        $properties=join(" ",$properties);
        return "<input name='$name' type='$type' value='$value' $properties/>";
    }
    function button($type,$name,$value,$children,...$properties){
        $properties=join(" ",$properties);
        return "<button name='$name' type='$type' value='$value' $properties>$children</button>";
    }


    //General Service
    function update($tablename,$property,$value,$predicate){
        global $conn;
        $updateQuery="update $tablename set $property='$value' where $predicate";
        return mysqli_query($conn,$updateQuery);
    }
    function updateAll($tablename,$keyValue,$predicate){
        global $conn;
        $kV="";
        foreach ($keyValue as $key => $value) {
            if(gettype($value)=='string') $value="'$value'";
            if(!isset($value)) unset($keyValue[$key]);
            $kV=$kV."  "."$key=$value,";
        }
        $kV=substr($kV, 0, -1);
        $updateQuery="update $tablename set $kV where $predicate";
        return mysqli_query($conn,$updateQuery);
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
    function deleteById($tablename,$id){
        global $conn;
        $query="delete from $tablename where id=$id";
        if(mysqli_query($conn,$query)){
            return true;
        }
        return false;
    }

    function getByPredicate($tablename,$predicate){
        global $conn;
        $query="select * from $tablename where $predicate";
        $result=mysqli_query($conn,$query);
        if($result) return $result;
        return false;
    }

    function getById($tablename,$id){
        $result=getByPredicate($tablename,"id=$id");
        if($result) return mysqli_fetch_assoc($result);
        return false;
    }
    
    //User Service
    function getUserById($user_id){
        $user=getById("user",$user_id);
        unset($user['password']);
        return $user;
    }
    //Answer Service
    function insertAns($quesId,$content,$isTrue,){
        $object=Array(
            "question_id"=>$quesId,
            "content"=>$content,
            "isTrue"=>$isTrue
        );
        return insert("answer",$object);
    }
    function getAnsByQuesId($quesId){
        $result= getByPredicate("Answer","question_id=$quesId");
        $arr=Array();
        while($row=mysqli_fetch_assoc($result)){
            array_push($arr,$row);
        }
        return $arr;
    }
    function updateAnswerById($id,$content,$isTrue,$pos=null){
        $answe=Array(
            "content"=>$content,
            "isTrue"=>$isTrue,
        );
        if(isset($pos)){
            $answe['pos']=$pos;
        }
        return updateAll("answer",$answe,"id=$id");
    }
    
    //Question Service
    const GET_ANSWER=1;
    function insertQues($user_id,$course_id,$quesType,$content,$file,$state=CHUA_DUYET){
        $object=Array(
            "user_id"=>$user_id,
            "course_id"=>$course_id,
            "question_type"=>$quesType,
            "content"=>$content,
            "state"=>$state
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

    function getQuestionById($id,$getAnswer=true){
        $question=getById("Question",$id);
        if($question!=false){
            if($getAnswer==true){
                $answers=getAnsByQuesId($question["id"]);
                $question["answer"]=$answers;
            }
            $user=getUserById($question['user_id']);
            $question["user"]=$user;
            return $question;
        }
        return false;
    }
    function getRandomQuestion($limit,$courseId,...$predicate){
        global $conn;
        $predicate="course_id=$courseId".join(" ",$predicate);
        $query="select id from question where $predicate order by rand() limit $limit";
        echo $query;
        $arr=Array();
        $result=mysqli_query($conn,$query);
        while($row=mysqli_fetch_assoc($result)){
            array_push($row,$arr);
            print_r($row);
            echo "<br>";
        }
        // for($i=0;$i<count($arr);$i++){
        //     $arr[$i]=getQuestionById($arr[$i]['id']);
        // }
        
        return $arr;
    }
    function getAllQuestionByCourseId($courseId,$user_id=false) {
        $predicate="course_id=$courseId";
        $user=getUserById($user_id);
        if($user['role']!="ADMIN"){
            $predicate=$predicate." and user_id=$user_id";
        }
        $question=getByPredicate("Question",$predicate);
        $arr=Array();
        while($row=mysqli_fetch_assoc($question)){
            $answers=getAnsByQuesId($row["id"]);
            $user=getUserById($row['user_id']);
            $row["answer"]=$answers;
            $row["user"]=$user;
            array_push($arr,$row);
        }
        return $arr;
    }
    function deleteQuestionById($questionId){
        $question=getQuestionById($questionId);
        foreach ($question['answer'] as $key => $answer) {
            deleteById("answer",$answer['id']);
        }
        if(isset($question['imgpath'])&&file_exists($question['imgpath'])){
            unlink($question['imgpath']);
        }
        
        deleteById("question",$questionId);
    }
    function updateQuesionById($id,$content){
        update("question","content",$content,"id=$id");
    }

    function duyetCauhoi($questionId) {
        update("question","state","Đã duyệt","id=$questionId");
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
    function replaceQuestionImg($filepath,$name){
        $ex=pathinfo($filepath,PATHINFO_EXTENSION);
        $path="../images/$name.$ex";
        move_uploaded_file($filepath,$path);
    }
    //print_arr
    function print_arr($arr){
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }
    //Render
    function FormGroup($children,$class=""){
        return "<div class='form-group $class'>$children</div>";
    }
    function FormInput($type,$name,$value,...$property){
        $properties=" class='form-control' "+join(" ",$property);
        return input($type,$name,$value,$properties);
    }
    
    function Question($question,$order=0){
        $form="";
        if($order!=0) $form=$form.FormGroup("Câu $order:$question[content]");
        if(isset($question['imgpath'])&&file_exists($question['imgpath'])){
            $form=$form.FormGroup("<img src='$question[imgpath]'/>");
        }
        $form=$form."<input type='hidden' name='type_$question[id]' value='$question[question_type]'/>";
        return $form;
    }
    function CauHoiDien($question,$order=0){
        $form="<div>";
        $form=$form.Question($question,$order);
        $form.FormGroup(FormInput("text","ans_$question[id]",""));
        $form=$form."</div>";
        return $form;
    }
    function TracNghiem1Da($question,$order=0){
        $form="<div>";
        $form=$form.Question($question,$order);
        $ans=join(" ",array_map(function($answer){
            return 
            "<div style='margin: 20px 0 0 0;' class='input-group mb-3'> 
                ".input("radio","ans_$answer[question_id]",$answer['id']).
                input("text",$answer['question_id'],$answer['content'])."
            </div>";
        },$question['answer']));
    }
    function TracNghiemnDa($question,$order){
        $form="<div>";
        $form=$form.Question($question,$order);
        $ans=join(" ",array_map(function($answer){
            return 
            "<div style='margin: 20px 0 0 0;' class='input-group mb-3'> 
                ".input("checkbox","check_$answer[id]",$answer['id']).
                input("text",$answer['question_id'],$answer['content'])."
            </div>";
        },$question['answer']));
    }
?>

  