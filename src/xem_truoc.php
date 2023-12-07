<?php
    include '../function.php';
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: ./dang_nhap.php");
    }
    $editMode=isset($_POST["edit"]);
    if(isset($_GET["quesId"])){
        if(isset($_SESSION['editing_question'])){
            $question=$_SESSION['editing_question'];
        }
        if(!isset($_SESSION['editing_question']) || $_GET["quesId"]!= $question['id']){
            $_SESSION['editing_question']=getQuestionById($_GET["quesId"]);
            $question=$_SESSION['editing_question'];
        }
    }
    if(isset($_POST['xoaanh'])){
        $editMode=true;
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
    if(isset($_POST["confirm"])){
        $question['content']=$_POST['question_content'];
        if(isset($_FILES['question_img'])&&$_FILES['question_img']!=""){
            if(file_exists($question['imgpath'])) unlink($question['imgpath']);
            $fileUp=createFile($_FILES['question_img'],$question['id']);
        }
       
        if($fileUp){
            $question['imgpath']=$fileUp;
        }
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
        $isValid=updateQuesionById($question['id'],$question['content']);
        foreach ($question['answer'] as $key => $answer) {
            $isValid=updateAnswerById($answer['id'],$answer['content'],$answer["isTrue"]?1:0);
        }
        if($isValid){
            echo successMessage("Update thành công câu hỏi");
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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
            <p class="h3">Khóa học 
                <?php
                    if(isset($_GET["courseId"])){
                        $query="Select * from course where id=$_GET[courseId]";
                        $result=mysqli_query($conn,$query);
                        while($row=mysqli_fetch_assoc($result)){
                            print_r($row['name']);
                        }
                    }
                ?>
            </p>
			<a href="./bien_tap.php<?php echo"?courseId=$_GET[courseId]"?>"  class="btn btn-primary">Trở lại</a>
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
                                    echo input('text',"da$key",$answer['content'],'class="form-control"',$editMode?"":"readonly",$answer['isTrue']?"style='border:2px solid green'":"");
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
		
	</main>

    <?php include 'footer.php'; ?>

</body>

	
</html>