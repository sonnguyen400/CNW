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
		<div class="" style="text-align: center;">
			<h2>Khóa học</h2>
		</div>
        <!-- begin khóa học -->
		<div class="row row-cols-1 row-cols-md-3 g-4" style="margin: 0 auto; width: 80%;">
        <?php 
            $courses=getAllCourse();
            foreach ($courses as $key => $row) {
                echo "  <div class='col'>
                            <div class='card'>
                                <img src='$row[imgpath]' class='card-img-top' alt='Course Image'>
                                <div class='card-body'>
                                <h5 class='card-title'>$row[name]</h5>
                                <a class='btn btn-primary' href='./bien_tap.php?courseId=$row[id]'>Truy cập</a>
                                <a class='btn btn-primary' href='./test.php?courseId=$row[id]'>Kiểm tra</a>
                                </div>
                            </div>
                        </div>";
            }
        ?>
			

		</div>
	</main>
	<?php include 'footer.php'; ?>
</body>

	
</html>