<?php 
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
	<title>Thêm câu hỏi</title>
	<!-- Begin bootstrap cdn -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
	<!-- End bootstrap cdn -->

</head>
<body>
    <?php 
        include 'navbar.php';
    ?>
	<main style="min-height: 100vh; max-width: 100%;">
			<div id="action" style="margin: 20px 0 0 13%;">
            <p class="h3">Khóa học 
                <!-- tên khóa học -->
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
			<a href="./bien_tap.php<?php echo"?courseId=$_GET[courseId]"?>" class="btn btn-primary">Trở lại</a>
            <form action="./trac_nghiem_1DA.php<?php echo"?courseId=$_GET[courseId]"?>" method="POST" enctype="multipart/form-data">
			</div>
            <div style="margin: 20px 13%;">
                <div class="form-group">
                    <label for="name_quiz"><span style="color: red;">*</span>Nhập tên câu hỏi</label>
                    <input class="form-control"  type="text" value="<?php if((isset($_POST["btn"])||isset($_POST["createDa"]))&&isset($_POST["ten_cau_hoi"])){ echo $_POST["ten_cau_hoi"];}?>" name="ten_cau_hoi" id="">
                </div>
                <div class="form-group">
                    <label for="name_quiz">Ảnh cho câu hỏi</label>
                    <input class="form-control"  type="file" name="file_tai_len" id="">
                </div>
                <div class="form-group">
                    <label for="name_quiz">Dạng câu hỏi</label>
                    <input class="form-control" value="<?php echo TRAC_NGHIEM_1DA?>" readonly  type="text" name="dang_cau_hoi" id="">
                </div>
                <div class="form-group">
                    <label for="name_quiz"><span style="color: red;">*</span>Nhập số lượng đáp án</label>
                    <input class="form-control"  type="number" min='4' max='10' value="<?php if(isset($_POST["createDa"])&&isset($_POST["cautralois"])){ echo $_POST["cautralois"]<4?4:$_POST["cautralois"];}else echo 4?>" name="cautralois" id="">
                        <button name="createDa" class="btn-primary btn" style="margin-top: 10px">Tạo đáp án</button>
                </div>
                <?php
                    $defaultValue=4;
                    if(isset($_POST["cautralois"])){
                        $defaultValue=$_POST["cautralois"];    
                    }
                    $isValid=true;
                    for($i=0;$i<$defaultValue;$i++){
                        $isChecked=isset($_POST["check"])&&$_POST["check"]==$i?"checked":"";
                        $validField=true;
                        if(isset($_POST["da$i"])&&trim($_POST["da$i"])==""){
                            $isValid=$validField=false;
                        }
                        echo "<div style='margin: 20px 0 0 0;' class='input-group mb-3'>   
                                <input type='radio' name='check' value='$i' ".($isChecked).">
                                <input name='da$i' type='text' value='".(isset($_POST["da$i"])?$_POST["da$i"]:"")."' class='form-control'  placeholder='Nhập đáp án'>
                            </div>
                            ".($validField?"":"<small>Không thể bỏ trống</small>"); 
                    }
                ?>
                <div style="margin: 20px 0 0 0;" class="d-grid">
                    <input class="btn btn-primary btn-block" name="btn" type="submit" value="Thêm câu hỏi">
                </div>

                <?php
                    if(isset($_POST["btn"])){
                        $ten_cau_hoi=trim($_POST["ten_cau_hoi"]);
                        $dang_cau_hoi=$_POST["dang_cau_hoi"];
                        $das=Array();
                        $count=0;
                        for($i=0;$i<$defaultValue;$i++){
                            if(isset($_POST["da$i"])){
                                $isChecked=isset($_POST["check"])&&$i==$_POST["check"];
                                $count+=$isChecked?1:0;
                                $da=Array(
                                    "ans"=>$_POST["da$i"],
                                    "isTrue"=>$isChecked
                                );
                                array_push($das,$da);
                                
                            }
                        }
                        if($count==0) {
                            $isValid=false;
                            echo errorMessage("Câu hỏi phải có ít nhất một đáp áp đúng");
                        }
                        if(strlen($ten_cau_hoi)==0||!$isValid){
                            echo  errorMessage("Thêm câu hỏi thất bại");
                        }else{
                            $insertQues=insertQues($_SESSION["userid"],$_GET["courseId"],$dang_cau_hoi,$ten_cau_hoi,$_FILES["file_tai_len"], $_SESSION["role"]==ADMIN?DA_DUYET:CHUA_DUYET);
                            if($insertQues['id']!=false){
                                foreach ($das as $key => $da) {
                                    insertAns($insertQues['id'],$da["ans"],$da["isTrue"]);
                                }
                                echo successMessage("Thêm câu hỏi thành công");
                            };  
                        }
                    }
                ?>
            </div>
            </form>
		
	</main>

    <?php 
        // include 'footer.php'; 
    ?>

</body>

	
</html>