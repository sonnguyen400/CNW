<?php
    include '../function.php';
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: ./dang_nhap.php");
    }
    if(isset($_GET["quesId"])){
        $question=getQuestionById($_GET["quesId"]);
    }
    $editMode=isset($_POST["edit"]);
    
    if(isset($_POST['xoaanh'])){
        $editMode=true;
        $question['imgpath']="";
        unlink($_POST['xoaanh']);
    }
    if(isset($_POST['duyet'])){
        duyetCauhoi($_GET['quesId']);
        $_SESSION['editing_question']=getQuestionById($_GET["quesId"]);
        $question=$_SESSION['editing_question'];
    }
    if(isset($_POST['delete'])){
        deleteQuestionById($_GET['quesId']);
        header("Location: ./bien_tap.php?courseId=$_GET(courseId)");
    }


    //Logic sửa Câu hỏi
    if(isset($_POST["confirm"])){
       //Image edit
        if(file_exists($_FILES['question_img']['tmp_name']) &&is_uploaded_file($_FILES['question_img']['tmp_name'])){
            if(file_exists($question['imgpath'])) unlink($question['imgpath']);
            $fileUp=createFile($_FILES['question_img'],$question['id']);
            $question['imgpath']=$fileUp;
        }

        $question['content']=$_POST['question_content'];
        if($question['question_type']==CAUHOI_DIEN){
            $question['answer'][0]['content']=$_POST["da"];
        }else if($question['question_type']==TRAC_NGHIEM_1DA){
            for($i=0;$i<count($question['answer']);$i++){
                $question['answer'][$i]['content']=$_POST["da$i"];
                $question['answer'][$i]['isTrue']=$_POST["radio"]==$i;
            }
        }else if($question['question_type']==TRAC_NGHIEM_nDA){
            for($i=0;$i<count($question['answer']);$i++){
                $question['answer'][$i]['content']=$_POST["da$i"];
                $question['answer'][$i]['isTrue']=isset($_POST["check$i"])?1:0;
            }
        }
        $isValid=updateQuesionById($question['id'],$question['content'],$question['imgpath']);
        foreach ($question['answer'] as $key => $answer) {
            $isValid=updateAnswerById($answer['id'],$answer['content'],$answer["isTrue"]?1:0);
        }
        if($isValid){
            echo successMessage("Update thành công câu hỏi");
            header("Refresh: 3;");
        }else{
            echo errorMessage("Thao tác cập nhật thất bại");
        }
        $_SESSION['editing_question']=$question;
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Xem trước</title>
	<!-- Begin bootstrap cdn -->
	<link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<script src="../js/bootstrap.bundle.min.js"></script>
	<!-- End bootstrap cdn -->
    <style>
        .h3{
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php';?>
	<main style="min-height: 100vh; max-width: 100%;">

		<div id="action" style="margin: 20px 0 0 13%;"> 
            <?php
                if(isset($_GET["testId"])||isset($_GET['courseId'])){
                    if(isset($_GET["testId"])){
                        $test=getTestById($_GET["testId"]);
                        echo("<p class='h3'>Thống kê điểm số bài kiểm tra - $test[title] </p>");
                        $user_score=analyticsTest($_GET["testId"],null);
                    }else if(isset($_GET["courseId"])){
                        $course=getCourseById($_GET["courseId"]);
                        echo("<p class='h3'>Thống kê điểm số bài kiểm tra - $course[name] </p>");
                        $user_score=analyticsTest(null,$_GET["courseId"]);
                    }
                    
                   
                    echo " <table class='table table-striped'>
                            <tr>
                                <td>STT</td>
                                <td>ID người dùng</td>
                                <td>Tên người dùng</td>
                                <td>Số câu đúng</td>
                                <td>Điểm thang 4</td>
                                <td>Điểm thang 10</td>
                            </tr>";
                    if(count($user_score)==0){
                        echo "<tr><td>Chưa có ai làm bài kiểm tra này</td></tr>";
                    }
                    foreach ($user_score as $key => $record) {
                        echo    "<tr>
                                    <td>".($key+1)."</td>
                                    <td>".$record['user']['id']."</td>
                                    <td>".$record['user']['userName']."</td>
                                    <td>$record[answer_number]</td>
                                    <td>".(round(($record['answer_number']*1.0/$record['question_number']),2)*4)."</td>
                                    <td>".(round(($record['answer_number']*1.0/$record['question_number']),2)*10)."</td>
                                </tr>";
                    }
                        echo "</table>";
                }
            ?>
		</div>
	</main>

    <?php include 'footer.php'; ?>

</body>

	
</html>