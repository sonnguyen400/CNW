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
        .duyet,.chuaduyet{
            border-radius: 15px;
            color:aliceblue;
            padding: 3px 8px;
        }
        .duyet{
            background-color: #42ff90;
        }
        .chuaduyet{
            background-color: #f55385;
        }
        input.cauhoi{
            font-size: 20px;
            font-weight:400;
        }
        input[readonly],
        input[readonly]:focus{
            outline: none;
            border: none;
        }
        .form-group>img{
            max-width: 600px;
            min-width: 300px;
        }
        .action-btns button{
            margin:10px 5px 20px 0px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>
	<main style="min-height: 100vh; max-width: 100%;">
					<!-- <hr> -->
			
		<div id="action" style="margin: 20px 0 0 13%;">
            <p class="h3">
                <!--Tên khóa học  -->
                Khóa học: 
                <?php
                    if(isset($_GET["courseId"])){
                        $course=getCourseById($_GET["courseId"]);
                        print_r($course['name']);
                    }
                ?>
                
            </p>
			<a href="<?php echo $_SESSION['prepage']; ?>"  class="btn btn-primary">Trở lại</a>
            <form action="" method="POST" enctype="multipart/form-data">
                <div   style="margin: 20px 30%;">
                    <!-- tên câu hỏi -->
                    <div class="form-group">
                        <label for="name_quiz"><h4>Câu hỏi:</h4></label>
                        <?php
                            echo input("text","question_content","$question[content]",$editMode==false?"readonly":""," class='cauhoi'");
                            if($question['state']==DA_DUYET){
                                echo "<span class='duyet'>".DA_DUYET."</span>";
                            }else echo "<span class='chuaduyet'>".CHUA_DUYET."</span>";
                        ?>
                    </div>
                    <!-- ảnh câu hỏi -->
                    <div class="form-group">
                        <?php
                            if(file_exists($question['imgpath'])){
                                echo "<img name='question_img' src='$question[imgpath]?=filemtime($question[imgpath])' />";
                            }
                            if($editMode){
                                echo "<label for='name_quiz'>Ảnh cho câu hỏi</label>
                                    <div style='display:flex;flex-direction:row;width:100%'>
                                    <input class='form-control'  type='file' name='question_img' id=''>
                                    <button name='xoaanh' value='$question[imgpath]' class='btn btn-danger'>Xóa ảnh</button>
                                    </div>";
                                
                            }
                        ?>
                       
                    </div>
                
                    <div style='margin: 20px 0 0 0;' class='input-group mb-3'>
                        <?php
                            $showRightAnswer= ($question['user_id']==$_SESSION['userid']||$_SESSION['role']==ADMIN);
                            if($question['question_type']==CAUHOI_DIEN){
                                echo input('text','da',$question['answer'][0]['content'],'class="form-control"',$editMode?"":"readonly");
                            }else if($question['question_type']==TRAC_NGHIEM_1DA||$question['question_type']==TRAC_NGHIEM_nDA){
                                foreach ($question['answer'] as $key => $answer) {
                                    
                                    echo "<div style='margin: 20px 0 0 0;' class='input-group mb-3'> ";
                                    if($editMode){
                                        echo input($question['question_type']==TRAC_NGHIEM_1DA?"radio":"checkbox",
                                                $question['question_type']==TRAC_NGHIEM_1DA?"radio":"check$key",
                                                "$key",
                                                $answer['isTrue']?"checked":"");
                                    }
                                    echo input('text',"da$key",$answer['content'],'class="form-control"',$editMode?"":"readonly",$answer['isTrue']&&$showRightAnswer?"style='border:2px solid green'":"");
                                    echo "</div>";
                                }
                            }
                        ?>
                    </div>
                    <?php
                        echo "<div class='action-btns'>";
                            if($editMode){
                                echo button('submit','confirm','confirm','Xong',"class='btn btn-primary'");
                                echo  button('submit','cancelEdit',"cancelEdit",'Hủy',"class='btn btn-danger'");
                            }else{
                                if($question['state']==CHUA_DUYET)
                                    echo button('submit','edit','edit','Sửa',"class='btn btn-primary'");
                            }
                            if($_SESSION["role"]==ADMIN&&$question['state']==CHUA_DUYET){
                                echo button('submit','duyet',$_GET['quesId'],"Duyệt","class='btn btn-success'")."<br>";
                            }
                            if($_SESSION["role"]==ADMIN) echo button('submit','delete',$_GET['quesId'],"Xóa","class='btn btn-danger'");
                        echo "</div>";
                    ?>
                </div>
            </form>
		</div>
	</main>

    <?php include 'footer.php'; ?>

</body>

	
</html>