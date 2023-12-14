<?php 
    include '../function.php'; 
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
    }
    $questions=getRandomQuestion(6,true,"state='".DA_DUYET."'","course_id='$_GET[courseId]'");
    $_SESSION['questions']=$questions;
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
	<main style="min-height: 100vh; max-width: 100%;">
			
        <div id="action" style="margin: 20px 0 0 13%;">
            <form action="./score.php<?php echo "?courseId=$_GET[courseId]"?>" method="post">
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
                ?>
                <button type="submit" name="submit" value="submit" class="btn btn-success">Gửi</button>
            </form>
           
        </div>
	</main>
    <?php 
        include 'footer.php'; 
    ?>
</body>

	
</html>