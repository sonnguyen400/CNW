<?php 
    include '../function.php'; 
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
    }
    if(isset($_POST['delete'])){
        deleteQuestionById($_POST['delete']);
    }
    if(isset($_POST['duyet'])){
        duyetCauhoi($_POST['duyet']);
    }
    if(isset($_POST['view'])){
        header("Location: ./xem_truoc.php?quesId=$_POST[view]&courseId=$_GET[courseId]");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Biên tập</title>
	<!-- Begin bootstrap cdn -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
	<!-- End bootstrap cdn -->
    <style>
        nav.pagination{
            float: right;
        }
    </style>
</head>
<body>
    <?php 
        include 'navbar.php';
    ?>
	<main style="min-height: 100vh; max-width: 1200px;padding:40px">
        <div >
            <h5>Lịch sử kiểm tra</h5>
            <table  class="table table-striped">
                <tr>
                    <td>Thời gian</td>
                    <td>Số lượng câu hỏi</td>
                    <td>Câu trả lời đúng</td>
                    <td>Kết quả</td>
                </tr>
            </table>
            <nav class="pagination" aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>

        <div style="clear: right;">
            <h5>Lịch sử  làm sai</h5>
            <table  class="table table-striped">
                <tr>
                    <td>Khóa học</td>
                    <td>Câu hỏi</td>
                    <td>Số lần làm sai</td>
                </tr>
                <?php
                
                ?>
            </table>
            <nav class="pagination" aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
	</main>
    <?php 
        // include 'footer.php'; 
    ?>
</body>
</html>