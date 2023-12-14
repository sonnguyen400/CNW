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
    
    const QUESTION_TABLE="cau_hoi";
    const COURSE_TABLE="khoa_hoc";
    const TIME_ZONE=new DateTimeZone("Asia/Ho_Chi_Minh");
    const CREATE_TEST_OK="create_test_ok";
    //Navigate function
    if(isset($_SERVER['HTTP_REFERER'])){
        $_SESSION['prepage']=$_SERVER['HTTP_REFERER'];
    }else{
        $_SESSION['prepage']="../index.php";
    }

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
            return true;
        }
	}
    // end checkLogin


    //Component
    function input($type,$name,$value,...$properties){
        $properties=join(" ",$properties);
        return "<input name='$name' type='$type' value='$value' $properties>";
    }
    function button($type,$name,$value,$children,...$properties){
        $properties=join(" ",$properties);
        return "<button name='$name' type='$type' value='$value' $properties>$children</button>";
    }


    //General Service
    function update($tablename,$property,$value,$predicate){
        global $conn;
        $updateQuery="update $tablename set $property=$value where $predicate";
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

    function get($tablename,...$predicate){
        global $conn;
        if(count($predicate)!=0){
            $predicate=" where ".join(" and ",$predicate);
        }else  $predicate=" ";
        $query="select * from $tablename $predicate";
        $result=mysqli_query($conn,$query);
        if($result) return $result;
        return false;
    }
    function getPagination($tablename,$predicate,$orderBy,$order,...$limit){
        global $conn;

        if(count($predicate)!=0)   $predicate=" where ".join(" and ",$predicate);
        else  $predicate="";
        if(count($orderBy)!=0)   $orderBy=" order by ".join(" , ",$orderBy);
        else $orderBy="";
        if($order=="ASC"||$order=="DESC") $orderBy=$orderBy." $order ";
        if(count($limit)!=0) $limit=" limit ".join(",",$limit);
        else $limit="";
        $query="Select * from $tablename $predicate $orderBy $limit";
        $result=mysqli_query($conn,$query);
        if($result) return $result;
        return false;

    }

    function getById($tablename,$id){
        $result=get($tablename,"id=$id");
        if($result) return mysqli_fetch_assoc($result);
        return false;
    }
    //Course Service
    function getCourseById($id){
        $result=get(COURSE_TABLE,"id=$id");
        if($result) return mysqli_fetch_assoc($result);
        return false;
    }
    function getAllCourse(){
        $result=get(COURSE_TABLE);
        $arr=Array();
        while($row=mysqli_fetch_assoc($result)){
            array_push($arr,$row);
        }
        return $arr;
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
        $result= get("Answer","question_id=$quesId");
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
        $id=insert(QUESTION_TABLE,$object);
        $fileok=false;
        if($id!=false&&isset($file)){
            $fileok=createFile($file,$id);
            if($fileok!=false){
                update(QUESTION_TABLE,"imgpath","'$fileok'","id=$id");
            }
        }
        
        return Array(
            "id"=>$id,
            "file"=>$fileok
        );
    }

    function getQuestionById($id,$getAnswer=true,$getUser=true,$getCourse=false){
        $question=getById(QUESTION_TABLE,$id);
        if($question!=false){
            if($getAnswer){
                $answers=getAnsByQuesId($question["id"]);
                $question["answer"]=$answers;
            }
            if($getUser){
                $user=getUserById($question['user_id']);
                $question["user"]=$user;
            }
            if($getCourse){
                $question["course"]=getCourseById($question["course_id"]);
            }
            return $question;
        }
        return false;
    }
    function getRandomQuestion($limit,$detail,...$predicate){
        global $conn;
        $predicate=join(" and ",$predicate);
        $query="select id from ".QUESTION_TABLE." where $predicate order by rand() limit $limit";
        $arr=Array();
        $result=mysqli_query($conn,$query);
        while($row=mysqli_fetch_assoc($result)){
            array_push($arr,$row['id']);
        }
        if($detail){
            for($i=0;$i<count($arr);$i++){
                $arr[$i]=getQuestionById($arr[$i],true,false);
            }
        }
        return $arr;
    }
    function getAllQuestionByCourseId($courseId,$user_id=false,...$predicate) {
        $predicate=[...$predicate,"course_id=$courseId"];
        $predicate=join(" and ",$predicate);
        $user=getUserById($user_id);
        if($user['role']!="ADMIN"){
            $predicate=$predicate." and user_id=$user_id";
        }
        $question=get(QUESTION_TABLE,$predicate);
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
        
        deleteById(QUESTION_TABLE,$questionId);
    }
    function updateQuesionById($id,$content,$imgpath=""){
        update("question","content","'$content'","id=$id");
        if(strlen(trim($imgpath))!=0){
            update("question","imgpath","'$imgpath'","id=$id");
        }
        
    }

    function duyetCauhoi($questionId) {
        update(QUESTION_TABLE,"state","'Đã duyệt'","id=$questionId");
    }
    function createTestQUestionCollection($page,$limit){
        $result=getPagination(QUESTION_TABLE,["state='".DA_DUYET."'"],['id'],"ASC",$page,$limit);
        $arr=Array();
        while($row=mysqli_fetch_assoc($result)){
            $row=getQuestionById($row['id'],false,false,true);
            array_push($arr,$row);
        }
        return $arr;
    }
    




    // Answer history service
    function addNewRecord($userId,$questionId){
        $result=get("answer_history","user_id=$userId","question_id=$questionId");
        if(mysqli_num_rows($result)==0){
            $wrongAnswer=Array(
                "question_id"=>$questionId,
                "user_id"=>$userId
            );
            return insert("answer_history",$wrongAnswer);
        }else{
            $y_answer=mysqli_fetch_assoc($result);
            update("answer_history","wrong_count","wrong_count+1","id= $y_answer[id]");
        }
        
    }
    function getAllRecord(){
        $result=get("answer_history");
        $arr=Array();
        while($row=mysqli_fetch_assoc($result)){
            $row['question']=getQuestionById($row['question_id'],false,false,true);
        }
    }
    function getAllRecordByUserId($userId){
        $result=getPagination("answer_history",["user_id=$userId"],"test_at","ASC",);
        $arr=Array();
        while($row=mysqli_fetch_assoc($result)){
            array_push($arr,$row);
        }
        for ($i=0; $i <count($arr) ; $i++) { 
            $arr[$i]['question']=getQuestionById($arr[$i]["id"]);
        }
        return $arr;
    }
    function getRecordById($userId,$questionId){
        $result=get("answer_history","id=$questionId");
        if($result) return mysqli_fetch_assoc($result);
        return false;
    }
    function getRecord($user_id,$page,$number) {
        $predicate=array("user_id=$user_id");
        $result=getPagination("answer_history",$predicate,["test_at"],"DESC",$page,$number);
        $arr=Array();
        while($row=mysqli_fetch_assoc($result)){
            $row['question']=getQuestionById($row['question_id'],false,false,true);
            array_push($arr,$row);
        }
        return $arr;
    }


    //Test service
    function insertNewTest($title,$testDesc,$limitTime,$limitCount,$releaseTime,$endTime,$questionIds){
        $testObject=Array(
            "title"=>$title,
            "test_desc"=>$testDesc,
            "limit_time"=>$limitTime,
            "release_time"=>$releaseTime,
            "end_time"=>$endTime,
            "limit_count"=>$limitCount
        );
        if($id=insert("test",$testObject)){
            $isValid=true;
            foreach ($questionIds as $key => $value) {
                $obj=Array(
                    "test_id"=>$id,
                    "question_id"=>$value
                );
                $isValid=insert("test_question",$obj);
            }
            if(!$isValid) return true;
        }
        return false;
    }
    function getTestById($testId,$fetchQuestionList=false){
        $result=get("test","id=$testId");
        $result=mysqli_fetch_assoc($result);
        if($fetchQuestionList){
            $arr=Array();
            $questionIds=get("test_question","test_id=$testId");
            while($row=mysqli_fetch_assoc($questionIds)){
                $question=getQuestionById($row['question_id'],true,false,false);
                array_push($arr,$question);
            }
            $result['questions']=$arr;
        }
        return $result;
    }
    function getAllTest(){
        $result=getPagination("test",[],['test_at'],"DESC");
        $arr=Array();
        while($row=mysqli_fetch_assoc($result)){
            array_push($arr,$row);
        }
        return $arr;
    }
    function deleteTestById($testId){
        deleteById("test",$testId);
    }
    


    //test history
    function insertTestRecord($questionAmount,$rightAnswer,$course_id,$test_id){
        global $conn;
        $object=Array(
            "user_id"=>$_SESSION['userid'],
            "question_number"=>$questionAmount,
            "answer_number"=>$rightAnswer,
            "course_id"=>$course_id,
            "test_id"=>$test_id
        );
        mysqli_query($conn,"SET FOREIGN_KEY_CHECKS=0;");
        $result= insert("test_history",$object);
        mysqli_query($conn,"SET FOREIGN_KEY_CHECKS=1;");
        return $result;
    }
    function getAllTestRecord($user_id=-1,...$limit){
        $arr=Array();
        if($user_id!=-1){
            $result=getPagination("test_history",["user_id=$user_id"],['test_at'],"DESC",...$limit);
            while($row=mysqli_fetch_assoc($result)){
                array_push($arr,$row);
            }
        }else{
            $result=getPagination("test_history",['test_at'],"DESC",...$limit);
            while($row=mysqli_fetch_assoc($result)){
                array_push($arr,$row);
            }
        }
        for ($i=0; $i <count($arr) ; $i++) { 
            if(isset($arr[$i]['course_id'])&&$arr[$i]['course_id']!=0){
                $arr[$i]['test_type']=getCourseById($arr[$i]['course_id'])['name'];
            }else if(isset($arr[$i]['test_id'])&&trim($arr[$i]['test_id'])!=""){
                $arr[$i]['test_type']=getTestById($arr[$i]['test_id'])['title'];
            }
        }
        
        return $arr;
    }

    function analyticsTest($test_id,$course_id){
        if(isset($test_id)){
            $predicate=["test_id=".$test_id];
        }else  if(isset($course_id)){
            $predicate=["course_id=".$course_id];
        }
        $result=getPagination("test_history",$predicate,['score'],"ASC");
        $arr=Array();
        while($row=mysqli_fetch_assoc($result)){
            $row['user']=getUserById($row["user_id"]);
            array_push($arr,$row);
        }
        return $arr;
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
    //Navigate($link)
    function navigate($link){
        header("Location: $link");
    }
    //Component
    function FormGroup($children,$class=""){
        return "<div class='form-group $class'>$children</div>";
    }
    function FormInput($type,$name,$value,...$property){
        $properties=" class='form-control' ".join(" ",$property);
        return input($type,$name,$value,$properties);
    }
    
    function Question($question,$order=0){
        $form=FormGroup("Câu $order: $question[content]");
        if(isset($question['imgpath'])&&file_exists($question['imgpath'])){
            $form=$form.FormGroup("<img src='$question[imgpath]'/>");
        }
        $form=$form."<input type='hidden' name='type_$question[id]' value='$question[question_type]'>";
        return $form;
    }
    function CauHoiDien($question,$order=0){
        $form="<div>";
        $form=$form.Question($question,$order);
        $form=$form.FormGroup(FormInput("text","ques_$question[id]",""));
        $form=$form."</div>";
        return $form;
    }
    function TracNghiem1Da($question,$order=0){
        $form="<div>";
        $form=$form.Question($question,$order);
        $ans=join(" ",array_map(function($answer){
            return 
            "<div style='margin: 20px 0 0 0;' class='input-group mb-3'> 
                ".input("radio","ques_$answer[question_id]",$answer['id']).
                input("text",$answer['question_id'],$answer['content'],"readonly")."
            </div>";
        },$question['answer']));
        $form=$form.$ans;
        $form=$form."</div>";
        return $form;
    }
    function TracNghiemnDa($question,$order){
        $form="<div>";
        $form=$form.Question($question,$order);
        $ans=join(" ",array_map(function($answer){
            return 
            "<div style='margin: 20px 0 0 0;' class='input-group mb-3'> 
                ".input("checkbox","ans_$answer[id]",$answer['id']).
                input("text",$answer['question_id'],$answer['content'],"readonly")."
            </div>";
        },$question['answer']));
        $form=$form.$ans;
        $form=$form."</div>";
        return $form;
    }
    function testItem($test){
        $ended= $test['end_time']<date('Y-m-d h:i:sa')?"disabled":"";
        return "<li  class='test-item $ended d-flex w-100 justify-content-between align-items-stretch'>
                    <div class='list-group-item d-flex flex-column flex-grow-1'>
                        <div class='d-flex justify-content-between align-items-start'>
                            <div class='d-flex flex-column'>
                                <h3>$test[title]</h3>
                                <small class='disabled'>$test[test_desc]</small>
                            </div>
                            <div class='d-flex flex-column'>
                                <div>Thời gian làm bài </div>
                                <div>$test[limit_time] phút</div>
                            </div>
                            
                            <div>
                                <div class='d-flex'>
                                    <div class='bubble success'>Bắt đầu $test[release_time]</div>
                                    <div class='bubble error'>Kết thúc $test[end_time]</div>
                                </div>
                                <div class='d-flex action_btn test_action justify-content-end'>
                                    ".($_SESSION['role']==ADMIN?
                                        "<button name='delete' value='$test[id]' class='btn btn-danger'>Xóa</button>
                                        <a name='analytics' href='./testanalytics.php?testId=$test[id]' class='btn btn-success'>Thống kê</a>"
                                        :"")
                                    ."<a name='start' href='./test.php?testId=$test[id]' class='btn btn-success'>Bắt đầu</a>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </li>";
    }
?>

  