<?php
    include '../function.php';
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
    }
    if(isset($_POST['delete'])){
        deleteTestById($_POST['delete']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kiểm tra</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<script src="../js/bootstrap.bundle.min.js"></script>
    
</head>
<body>
	<?php include 'navbar.php';?>
	<main style="min-height: 100vh; width:100%;">
		<?php
            $tests=getAllTest();
        ?>
		<ul  class="list-group" style="max-width: 1200px;margin:20px auto">
            <?php
                $tests=getAllTest();
                foreach ($tests as $key => $test) {
                    echo testItem($test);
                }
            ?>
        </ul>
	</main>
	<?php include 'footer.php'; ?>
</body>

	
</html>