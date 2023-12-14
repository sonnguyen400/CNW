<?php 
    include '../function.php'; 
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
    }
    $questions=Array();
    if(isset($_GET['courseId'])){
        $questions=getRandomQuestion(6,true,"state='".DA_DUYET."'","course_id='$_GET[courseId]'");
    }
    if(isset($_GET['testId'])){
        $test=getTestById($_GET['testId'],true);
        $questions=$test['questions'];
    }
    $_SESSION['questions']=$questions;
    if(count($questions)<5){
        echo errorMessage("Bài kiểm tra chưa sẵn sàng");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Biên tập</title>
	<!-- Begin bootstrap cdn -->
	<link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<script src="../js/bootstrap.bundle.min.js"></script>
	<!-- End bootstrap cdn -->
    <style>
        img{
            max-width: 400px;
        }
        a{
            text-decoration: none;
            color: white;
        }
        form{
            width: 100%;
        }
        .action-btns button{
            margin-right: 5px;
        }
        
        input:read-only{
            border: none !important;
            outline: none !important;
        }
        input[type='checkbox'],
        input[type='radio']{
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php 
        include 'navbar.php';
    ?>
	<main style="min-height: 100vh; max-width: 1200px;">
        <div id="action" style="margin: 20px 0 0 13%;">
            <?php
                if(isset($_GET['courseId'])){
                    $course=getCourseById($_GET['courseId']);
                    echo "<h2>Khóa học  $course[name]</h2>";
                }else if(isset($GET['testId'])){
                    echo "<h2>$test[title]</h2>";
                }
            ?>
            <div style="max-width: 800px;margin:40px auto">
                <form action="./score.php?<?php echo isset($_GET['courseId'])?"courseId=$_GET[courseId]":"testId=$_GET[testId]";?>" method="post">
                    <?php
                        foreach ($questions as $key => $question) {
                            switch ($question['question_type']) {
                                case CAUHOI_DIEN:
                                    echo CauHoiDien($question,$key+1);
                                    break;
                                case TRAC_NGHIEM_1DA:
                                    echo TracNghiem1Da($question,$key+1);
                                    break;
                                case TRAC_NGHIEM_nDA:
                                    echo TracNghiemnDa($question,$key+1);
                                    break;
                                default:
                                    break;
                            }
                        }
                        if(count($questions)<5){
                            echo "<a class='btn btn-success' href='$_SESSION[prepage]'>Quay lại</a>";
                        }else{
                            echo "<button type='submit' name='submit' value='submit' class='btn btn-success'>Gửi</button>";
                        }
                    ?>
                    
                    
                </form>
            </div>
           
        </div>
	</main>
    <?php 
        include 'footer.php'; 
    ?>
</body>

	
</html>