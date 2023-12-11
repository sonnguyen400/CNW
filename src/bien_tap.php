<?php 
    include '../function.php'; 
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
    }
    if(isset($_POST['delete'])){
        deleteQuestionById($_POST['delete']);
    }
    if(isset($_POST['duyet'])){
        duyetCauhoi($_POST['duyet']);
    }
    if(isset($_POST['view'])){
        header("Location: ./xem_truoc.php?quesId=$_POST[view]&courseId=$_GET[courseId]");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Biên tập</title>
	<!-- Begin bootstrap cdn -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
	<main style="min-height: 100vh; max-width: 100%;">
			
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
            <a href="./khoa_hoc.php" class="btn btn-primary">Trở lại</a>
           
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                Thêm câu hỏi
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="./them_cau_hoi.php<?php echo "?courseId=$_GET[courseId]" ?>" >Câu hỏi điền</a></li>
                <li><a class="dropdown-item" href="./trac_nghiem.php<?php echo "?courseId=$_GET[courseId]" ?>">Trắc nghiệm nhiều đáp án</a></li>
                <li><a class="dropdown-item" href="./trac_nghiem_1DA.php<?php echo "?courseId=$_GET[courseId]" ?>">Trắc nghiệm một đáp án</a></li>
            </ul>
           
        </div>
        <div class="d-flex flex-wrap flex-column align-items-center" style="padding: 1%;margin: 5% 0 0 0; ">
            <p class="h3">Danh sách câu hỏi</p>
            <form action="./bien_tap.php<?php echo"?courseId=$_GET[courseId]"?>" method="post">
                <table  class="table table-striped">
                    <tr>
                        <th>STT</th>
                        <th>Tên câu hỏi</th>
                        <th>Loại câu hỏi</th>
                        <th>Đáp án</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th> 
                    </tr>
                    <?php
                        function actionRender($question){
                            $action="";
                            $action=$action."<button name='view' value='$question[id]' class='btn btn-primary'>Xem trước</button>";
                            if($_SESSION['role']==ADMIN){
                                if($question['state']==CHUA_DUYET){
                                    $action=$action."<button name='duyet' value='$question[id]' class='btn btn-success'>Duyệt</button>";
                                }
                                $action=$action."<button name='delete' value='$question[id]' class='btn btn-danger'>Xóa</button>";
                            }
                            return "<div style='display:flex;flex-direction:row;' class='action-btns'>$action</div>";
                        }
                        $questions=getAllQuestionByCourseId($_GET['courseId'],$_SESSION['userid']);
                        
                        for ($i=0; $i <count($questions) ; $i++) { 
                            $answers=Array();
                            foreach ($questions[$i]['answer'] as $key => $answer) {
                                if($answer['isTrue']) array_push($answers,$answer);
                            }
                            echo "<tr>
                                    <th>$i</th>
                                    <th>".$questions[$i]['content']."</th>
                                    <th>".$questions[$i]['question_type']."</th>
                                    <th>".join(" ",array_map(function($answer){return "<p>$answer[content]</p>"; },$answers))."</th>
                                    <th>".$questions[$i]['user']['userName']."</th>
                                    <th>".$questions[$i]['state']."</th>
                                    <th>".actionRender($questions[$i])."</th> 
                                </tr>";
                        }
                        


                        if(count($questions)==0){
                            echo "<tr>
                                    <td align='center' colspan='6'>Không có câu hỏi nào</td>
                                </tr>";
                        }
                    ?>
                    
                </table>
            </form>
            
        </div>
	</main>
    <?php 
        // include 'footer.php'; 
    ?>
</body>

	
</html>