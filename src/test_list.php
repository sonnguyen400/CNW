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
	<title>Kiểm tra</title>
	<link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<script src="../js/bootstrap.bundle.min.js"></script>
    
</head>
<body>
	<?php include 'navbar.php';?>
	<main style="min-height: 100vh; width: 100%;">
		<?php
            $tests=getAllTest();
        ?>
		<ul  class="list-group">
            <li  class="list-group-item  d-flex flex-column w-100 justify-content-between">
                <div class="d-flex flex-column">
                    <h3>Title</h3>
                    <small class="disabled">Description</small>
                </div>
                <div>
                    <div class="remain-line"></div>
                </div>

            </li>
        </ul>
	</main>
	<?php include 'footer.php'; ?>
</body>

	
</html>