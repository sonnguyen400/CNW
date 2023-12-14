<?php

use function PHPSTORM_META\elementType;

    include '../function.php'; 
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
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
        
        .score_table,.score{
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .score{
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px ;
            margin-top: 20px;
            padding:32px;
            transform-origin:50% 0px;
            transform:scale(100%);
            animation: widing_box 2s;
        }
        @keyframes widing_box{
            from{
                transform:scale(50%);
            }
            to{
                transform:scale(100%);
            }
        }
        .exit_btn{
            margin-top: 20px;
        }
        .score h1{
            padding:30px;
            font-size:3.5rem;
        }
    </style>
</head>
<body>
    <?php 
        include 'navbar.php';
    ?>
	<main style="min-height: 100vh; max-width: 100%;">
			
        
        <div class="d-flex flex-wrap flex-column align-items-center" style="padding: 1%;margin: 5% 0 0 0; ">
            <div class="score_table">
                <h2>Kết quả</h2>
                <div class="score">
                    <?php
                        if(isset($_POST['submit'])){
                            $questions=$_SESSION['questions'];
                            $score=array_reduce($questions,function($pre,$question){
                                $id=$question['id'];
                                if($question['question_type']==CAUHOI_DIEN){
                                    if($question['answer'][0]['content']==$_POST["ques_$id"]){
                                        return $pre+ 1;
                                    }else{
                                        addNewRecord($_SESSION['userid'],$question['id']);
                                        return $pre+0;
                                    }
                                }
                                if($question['question_type']==TRAC_NGHIEM_1DA){
                                    $count=array_filter($question['answer'],function($answer)use($id){
                                        return $answer['isTrue']&&$answer['id']==$_POST["ques_$id"];
                                    });
                                    if(count($count)==1) return $pre+1;
                                    else{
                                        addNewRecord($_SESSION['userid'],$question['id']);
                                        return $pre+0;
                                    }
                                }
                                if($question['question_type']==TRAC_NGHIEM_nDA){
                                    $isTrue=true;
                                    for ($i=0; $i <count($question['answer']) ; $i++) { 
                                        $answer=$question['answer'][$i];
                                        if($answer['isTrue']&&!isset($_POST["ans_$answer[id]"])){
                                            $isTrue=false;
                                        }
                                        if(!$answer['isTrue']&&isset($_POST["ans_$answer[id]"])){
                                            $isTrue=false;
                                        }
                                    }
                                    if(!$isTrue){
                                        addNewRecord($_SESSION['userid'],$question['id']);
                                        return $pre+0;
                                    }else return $pre+0;
                                }
                            },0);
                            $question_amount=count($questions);
                            if(isset($_GET["courseId"])){
                                $type=getCourseById($_GET["courseId"])['name'];
                            }else if(isset($_GET['testId'])){
                                $type=getTestById($_GET['testId'])['test_type'];
                            }
                            insertTestRecord($question_amount,$score,$type);
                            echo "
                                <h1>".round(($score*1.0/$question_amount)*10.0,2)."</h1>
                                <h6>Làm được $score/$question_amount câu </h6>
                            ";
                        }
                    ?>
                </div>
                <a class="btn btn-success exit_btn" href="./khoa_hoc.php">Thoát</a>
            </div>
        </div>
	</main>
    <?php 
        // include 'footer.php'; 
    ?>
</body>

	
</html>