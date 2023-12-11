<?php 
	include 'function.php';
    if(isLogin()){
        header("Location: ./src/khoa_hoc.php");
    }else{
        header("Location: ./src/dang_nhap.php");
    }
	// dùng hàm kiểm tra đăng nhập trong file funciton
	// nếu đăng nhập rồi thì truy cập vào trang khóa học
	// còn chưa đăng nhập thì điều hướng ra trang đăng nhập
 ?>