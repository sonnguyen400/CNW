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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="	sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<!-- End bootstrap cdn -->

</head>
<body>
    <?php 
        // include 'navbar.php';
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
            <form action="" method="POST" enctype="multipart/form-data">
			</div>
            <div style="margin: 20px 13%;">
                <div class="form-group">
                    <label for="name_quiz"><span style="color: red;">*</span>Nhập tên câu hỏi</label>
                    <input class="form-control"  type="text" value="<?php if(isset($_POST["btn"])&&isset($_POST["ten_cau_hoi"])){ echo $_POST["ten_cau_hoi"];}?>" name="ten_cau_hoi" id="">
                </div>
                <div class="form-group">
                    <label for="name_quiz">Ảnh cho câu hỏi</label>
                    <input class="form-control"  type="file" name="file_tai_len" id="">
                </div>
                <div class="form-group">
                    <label for="name_quiz">Dạng câu hỏi</label>
                    <input class="form-control" value="Điền" readonly  type="text" name="dang_cau_hoi" id="">
                </div>
                <div style='margin: 20px 0 0 0;' class='input-group mb-3'>   
                    <input name='da' type='text' value="<?php if(isset($_POST["btn"])&&isset($_POST["da"])){ echo $_POST["da"];}?>" class='form-control' placeholder='Nhập đáp án'>
                </div>
                <?php

                    if(isset($_POST["btn"])){
                        $ten_cau_hoi=trim($_POST["ten_cau_hoi"]);
                        $dang_cau_hoi=trim($_POST["dang_cau_hoi"]);
                        $da=trim($_POST["da"]);
                        if(strlen($ten_cau_hoi)==0||strlen($dang_cau_hoi)==0||strlen($da)==0){
                            echo  errorMessage("Thêm câu hỏi thất bại");
                        }else{
                            $insertQues=insertQues($_SESSION["userid"],$_GET["courseId"],$dang_cau_hoi,$ten_cau_hoi,$_FILES["file_tai_len"]);
                            if($insertQues['id']!=false){
                                if( insertAns($insertQues['id'],$da,true)!=false)
                                    echo successMessage("Thêm câu hỏi thành công");
                            };  
                        }
                    }
                ?>
                
                <div style="margin: 20px 0 0 0;" class="d-grid">
                    <input class="btn btn-primary btn-block" name="btn" type="submit" value="Thêm câu hỏi">
                </div>

            </div>
            </form>
		
	</main>

    <?php 
        // include 'footer.php'; 
    ?>

</body>

	
</html>