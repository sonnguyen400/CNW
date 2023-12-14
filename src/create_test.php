<?php
    include '../function.php';
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
    }
    const page=10;
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
    if(isset($_SESSION['page'])){
        if($_SESSION['page']<0){
            $_SESSION['page']=0;
        }
        $page=$_SESSION['page'];
    }else{
        $_SESSION['page']=0;
        $page=$_SESSION['page'];
    }
    if(isset($_POST['prePage'])){
        $_SESSION['page']-=1;
        $page=$_SESSION['page'];
        $updateCheck();
    }
    if(isset($_POST['nextPage'])){
        $_SESSION['page']+=1;
        $page=$_SESSION['page'];
        $updateCheck();
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
</head>
<body>
	<?php 
        include 'navbar.php';
    ?>
    
	<main style="min-height: 100vh; width: 100%;">
		<form method="POST"  class="row row-cols-1 row-cols-md-3 g-4" style="margin: 0 auto; width: 80%;">
        <div >
			<h6>Chọn thủ công từ danh sách câu hỏi</h6>
            <p>Trang <?php echo $page+1;?></p>
		</div>
        <?php   
                $questions=createTestQUestionCollection($page*page,page);
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
                                <td><a target='blank' href='./xem_truoc.php?quesId=".($question['id'])."' class='btn btn-success'>Xem trước</a></td>
                            </tr>";
                }
                echo "</table>";
                echo "<nav class='pagination' aria-label='Page navigation example' style='width:100%;justify-content: end'>
                        <form method='GET'>
                            <ul class='pagination'>
                                <li  class='page-item'><button type='submit' name='prePage' class='page-link' ".($page<=0?'disabled':"")."  >Previous</button></li>
                                <li class='page-item'><button  type='submit' name='nextPage' class='page-link' ".(count($questions)==0?'disabled':"").">Next</button></li>
                            </ul>
                        </method>
                    </nav>";
            ?>
            <div class="form-group">
                <input type="checkbox" name="random" <?php echo isset($_POST['random'])?"checked":""?>>
                <h6 style="display: inline-block;" for="name_quiz">Chọn ngẫu nhiên câu hỏi </h6>
                <small class="desc-message">(Tích chọn ngẫu nhiên câu hỏi sẽ bỏ qua những lựa chọn thủ công ở bảng trên)</small>
            </div>
            <div class="form-group">
                <label for="random_number">Số lượng câu hỏi ngẫu nhiên</label>
                <input class="form-control" name="random_number" value=5  type="text" placeholder="Số lượng câu hỏi ngẫu nhiên" value="<?php echo isset($_POST['random_number'])?$_POST['random_number']:""?>">
                
                <?php
                    if(isset($_POST['random'])&&$_POST['random_number']<5){
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
                <div class="form-group">
                    <label for="name_quiz">Số lần làm bài </label>
                    <input value=0  type="text" name="limit_count" class="form-control"  value="<?php echo isset($_POST['desc'])?$_POST['desc']:""?>">
                    <small>Mặc định 0 là không giới hạn số lần làm</small>
                    <?php
                        if(isset($_POST['limit_count'])&&($_POST['limit_count']<0||trim($_POST['limit_count'])=="")){
                            echo "<small class='warn-message'>Giới hạn số lần làm bài không được bỏ trống và lớn hơn 0</small>";
                        }
                    ?>
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
            if(isset($_POST['create'])){
                $releaseTime=new DateTime($_POST['release_time'],TIME_ZONE);
                $endTime=new DateTime($_POST['end_time'],TIME_ZONE);
                $date=new DateTime('now',TIME_ZONE);
                if($endTime<$date){
                    $isValid=false;
                    echo errorMessage("Thời gian kết thúc không hợp lệ. Hạn bài thi phải lớn hơn 0 phút");
                }
                $interval=$endTime->diff($releaseTime);
                $interval=  (($interval->format("%Y"))*365*24*60)+
                            (($interval->format("%m"))*30*24*60)+
                            (($interval->format("%d"))*24*60)+
                            (($interval->format("%H"))*60);
                if($interval<=$_POST['limit_time']){
                    $isValid=true;
                    echo errorMessage("Thời gian bắt đầu và thời gian kết thúc không hợp lệ<br> Hạn bài làm phải lớn hơn 0 phút và lớn hơn thời gian làm bài!");
                }
                

                if(!isset($_POST['random'])){
                    if(count($questionIds)<5){
                        echo errorMessage("Bài kiểm tra phải đạt tối thiểu 5 câu hỏi");
                        $isValid=false;
                    } 
                }else{
                    $random=getRandomQuestion($_POST['random_number'],false,"state='".DA_DUYET."'");
                    if(count($random)<$_POST['random_number']){
                        $isValid=false;
                        echo errorMessage("Số lượng câu hỏi trong Database không đủ để lựa chọn ngẫu nhiên");
                    }
                }
                

                //Check valid cuối cùng
                if($isValid){
                    $questionCollection=Array();
                    if(isset($_POST['random'])){
                        $questionCollection=$random;
                    }else{
                        $questionCollection=$questionIds;
                    }
                    if(insertNewTest($_POST['title'],$_POST['desc'],$_POST['limit_time'],$_POST['limit_count'],$_POST['release_time'],$_POST['end_time'],$questionCollection)){
                        $_SESSION['questionIds']=null;
                        $_SESSION['page']=null;
                        echo successMessage("Tạo bài kiểm tra thành công");
                    }else{
                        echo errorMessage("Tạo bài kiểm tra mới thất bại !");
                    }
                }
            }
            
            
            
        ?>
	</main>
	<?php include 'footer.php'; ?>
    <!-- <a class='btn btn-primary' href='./test.php?courseId=$row[id]'>Kiểm tra</a> -->
</body>

	
</html>