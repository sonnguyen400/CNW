<?php
    include '../function.php';
    if(isset($_POST['submitLogin'])){
        if(checkLogin($_POST['username'],$_POST['password'])){
            header("Location:"."../index.php");
        }
    }
    if(isset($_GET['action'])=='logout'){
        session_unset();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Đăng nhập</title>
	<!-- Begin bootstrap cdn -->
	<link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<script src="../js/bootstrap.bundle.min.js"></script>
	<!-- End bootstrap cdn -->

</head>
<body>

    <?php
        if(isset($_GET['action'])){
            if(isset($_GET['action'])=='logout'){
                unset($_SESSION['userid']);
            }
        }
        if(isset($_GET['status'])){
            if($_GET['status']=='loginfail'){
                echo '<div class="alert alert-danger text-center" role="alert">Mẫu:Tài khoản hoặc mật khẩu không chính xác</div>';
            };
        }
    ?>
	
	<main style="min-height: 100vh; margin-top: 10%;">
		<div class="d-flex justify-content-center"><h1>Đăng nhập</h1></div>
		<div class="d-flex justify-content-center">
			<form class="w-25" method="POST">
				<div class="mb-3">
				  <label for="username" class="form-label">Username</label>
				  <input type="text" class="form-control" id="username" name="username" placeholder="Nhập username">
				</div>
				<div class="mb-3">
				    <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
				    <div class="col">
				      <input type="password" class="form-control" id="inputPassword" placeholder="Nhập Password" name="password">
				    </div>
				</div>
				<input type="submit" class="btn btn-primary" name="submitLogin" value="Đăng nhập">
			  </form>
		</div>

        <?php 
        include 'footer.php'; 
        
        ?>

		
	</main>
	
</body>

	
</html>