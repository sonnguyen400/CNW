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

    </style>
</head>
<body>
    <?php 
        include 'navbar.php';
    ?>
	<main style="min-height: 100vh; max-width: 100%;">
			
        <div id="action" style="margin: 20px 0 0 13%;">
            <form action="" method="post">
                <?php
                    // $questions=getRandomQuestion(5,1);
                    // print_r($questions);
                    // foreach ($questions as $key => $question) {
                    //     switch ($variable) {
                    //         case CAUHOI_DIEN:
                    //             echo CauHoiDien($question,$key);
                    //             break;
                    //         case TRAC_NGHIEM_1DA:
                    //             echo TracNghiem1Da($question,$key);
                    //             break;
                    //         case TRAC_NGHIEM_nDA:
                    //             echo TracNghiemnDa($question,$key);
                    //             break;
                    //         default:
                    //             # code...
                    //             break;
                    //     }
                    // }
                ?>
            </form>
           
        </div>
	</main>
    <?php 
        // include 'footer.php'; 
    ?>
</body>

	
</html>