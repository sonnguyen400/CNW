<?php
    include '../function.php';
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
    }
    const page=20;
    $questionIds=Array();
    if(!isset($_SESSION['questionIds'])){
        $_SESSION['questionIds']=Array();
    }
    $questionIds= $_SESSION['questionIds'];

    $updateCheck=function() {
        global $questionIds;
        foreach ($_POST as $key => $value) {
            if(str_starts_with($value,"question_")){
                echo $value;
                $id=explode("_",$value)[1];
                if(!in_array($id,$questionIds)) {
                    echo $id;
                    array_push($questionIds,$id);
                }
            }
        }
        $_SESSION['questionIds']= $questionIds;
    };
   
    if(isset($_POST['deselected'])){
        if (($key = array_search($_POST['deselected'],$questionIds)) !== false) {
            unset($questionIds[$key]);
            $updateCheck();
        }
    }
    if(isset($_POST['prePage'])){
        $updateCheck();
        navigate("./create_test.php?page=".($_GET['page']-1));
    }
    if(isset($_POST['nextPage'])){
        $updateCheck();
        navigate("./create_test.php?page=".($_GET['page']+1));
    }
    if(isset($_POST['create'])){
        $updateCheck();
    }
    $isValid=true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Khóa học</title>
	<!-- Begin bootstrap cdn -->
	<link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<script src="../js/bootstrap.bundle.min.js"></script>
	<!-- End bootstrap cdn -->
    <style>
        button[disabled]{
            pointer-events: none;
            opacity: 0.4;
        }
        .warn-message{
            color: red;
        }
    </style>
</head>
<body>
	<?php 
        include 'navbar.php';
    ?>
	<main style="min-height: 100vh; width: 100%;">
		<div class="" style="text-align: center;">
			<h2>Danh sách câu hỏi</h2>
            <h3>Trang <?php echo $_GET['page'];?></h3>
		</div>
		<form method="POST" class="row row-cols-1 row-cols-md-3 g-4" style="margin: 0 auto; width: 80%;">
        <?php   
                $questions=createTestQUestionCollection($_GET['page']*page,page);
                echo "<nav class='pagination' aria-label='Page navigation example'>
                        <form method='GET'>
                            <ul class='pagination'>
                                <li  class='page-item'><button type='submit' name='prePage' class='page-link' ".($_GET['page']<=0?'disabled':"")."  >Previous</button></li>
                                <li class='page-item'><button  type='submit' name='nextPage' class='page-link' ".(count($questions)==0?'disabled':"").">Next</button></li>
                            </ul>
                        </method>
                    </nav>";
                
                echo " <table  class='table table-striped'>
                            <tr>
                                <td>STT</td>
                                <td>Chọn</td>
                                <td>Phân loại</td>
                                <td>Câu hỏi</td>
                                <td>Thao tác</td>
                            </tr>";
                foreach ($questions as $key => $question) {
                    $isChecked= in_array($question['id'],$questionIds);
                    echo    "<tr>
                                <td>".($key+1)."</td>
                                <td>
                                    ".($isChecked?
                                    "<button name='deselected' value=$question[id]>Hủy chọn</button>":
                                    "<input type='checkbox' 
                                    name='question_name_$question[id]' 
                                    value='question_$question[id]'>")."
                                    
                                </td>
                                <td>".($question['course']['name'])."</td>
                                <td>".$question['content']."</td>
                                <td><a target='_blank' href='./xem_truoc.php/quesId=".($question['id'])."' class='btn btn-success'>Xem trước</a></td>
                            </tr>";
                }
                echo "</table>";
                
            ?>
            <div class="form-group">
                <label for="name_quiz">Chọn ngẫu nhiên câu hỏi </label>
                <input type="checkbox" name="random" <?php echo isset($_POST['random'])?"checked":""?>>
                <br>
                <small>(Tích chọn ngẫu nhiên câu hỏi sẽ bỏ qua những lựa chọn cụ thể ở bảng trên)</small>
                <input class="form-control" name="random_number" value=5  type="text" placeholder="Số lượng câu hỏi ngẫu nhiên" value="<?php echo isset($_POST['random_number'])?$_POST['random_number']:""?>">
                <?php
                    if(isset($_POST['create'])&&isset($_POST['random'])&&$_POST['random_number']<5){
                        $isValid=false;
                        echo "<small class='warn-message'>Số lượng câu hỏi ngẫu nhiên không được bỏ trống và phải lớn hơn 5</small>";
                    }
                ?>
            </div>

            <div style="width:100%" class="row row-cols-1 row-cols-md-3 g-4">
                <div class="form-group">
                    <label for="name_quiz">Tiêu đề bài kiểm tra</label>
                    <input type="text" name="title" class="form-control"  value="<?php echo isset($_POST['title'])?$_POST['title']:""?>">
                    <?php
                        if(isset($_POST['create'])&&trim($_POST['title'])==""){
                            $isValid=false;
                            echo "<small class='warn-message'>Không thể bỏ trống</small>";
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="name_quiz">Mô tả</label>
                    <input type="text" name="desc" class="form-control"  value="<?php echo isset($_POST['desc'])?$_POST['desc']:""?>">
                </div>
            </div>


            <div style="width:100%" class="row row-cols-1 row-cols-md-3 g-4">
                <div class="form-group">
                    <label for="name_quiz">Chọn thời gian phát bài</label>
                    <input type="datetime-local" name="release_time" class="form-control"  value="<?php echo isset($_POST['release_time'])?$_POST['release_time']:""?>">
                    <?php
                        if(isset($_POST['create'])&&trim($_POST['release_time'])==""){
                            $isValid=false;
                            echo "<small class='warn-message'>Không thể bỏ trống</small>";
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="name_quiz">Chọn thời gian hết hạn</label>
                    <input type="datetime-local" name="end_time" class="form-control"   value="<?php echo isset($_POST['end_time'])?$_POST['end_time']:""?>">
                    <?php
                        if(isset($_POST['create'])&&trim($_POST['end_time'])==""){
                            $isValid=false;
                            echo "<small class='warn-message'>Không thể bỏ trống</small>";
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label for="name_quiz">Chọn thời gian làm bài thi (phút)</label>
                    <input type="number" min=1 name="limit_time" placeholder="Đơn vị (phút)" class="form-control"  value="<?php echo isset($_POST['limit_time'])?$_POST['limit_time']:""?>">
                    <?php
                        if(isset($_POST['create'])&&isset($_POST['limit_time'])){
                            if($_POST['limit_time']<1){
                                $isValid=false;
                                echo "<small class='warn-message'>Thời gian làm bài phải lớn hơn 1 phút</small>";
                            }
                        }
                    ?>
                </div>
            </div>
            <div class="form-group" style="width: 100%;display: flex;justify-content: end;">
                <button type="submit" class="btn btn-success" name="create">Tạo bài kiểm tra</button>
            </div>
            
            
        </form>
        <?php
            if(!isset($_POST['random'])&&isset($_POST['create'])){
                if(count($questionIds)<5){
                    echo errorMessage("Bài kiểm tra phải đạt tối thiểu 5 câu hỏi");
                    $isValid=false;
                } 
            }
            if(isset($_POST['random'])){
                $random=getRandomQuestion($_POST['random_number'],false,"state='".DA_DUYET."'");
                if(count($questionIds)<$_POST['random_number']){
                    $isValid=false;
                    echo errorMessage("Số lượng câu hỏi trong Database không đủ để lựa chọn ngẫu nhiên");
                }
            }
            if(isset($_POST['create'])&&$isValid){
                $questionCollection=Array();
                if(isset($_POST['random'])){
                    $questionCollection=$random;
                }else{
                    $questionCollection=$questionIds;
                }
                if(insertNewTest($_POST['title'],$_POST['desc'],$_POST['limit_time'],$_POST['release_time'],$_POST['end_time'],$questionCollection)){
                    echo successMessage("Tạo bài kiểm tra thành công");
                };
            }
        ?>
	</main>
	<?php include 'footer.php'; ?>
    <!-- <a class='btn btn-primary' href='./test.php?courseId=$row[id]'>Kiểm tra</a> -->
</body>

	
</html>