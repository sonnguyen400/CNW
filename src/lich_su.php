<?php 
    include '../function.php'; 
    include '../connectdb.php';
    if(!isLogin()){
        header("Location: "."./dang_nhap.php");
    }
    const page=5;
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
        nav.pagination{
            float: right;
        }
        a[disabled]{
            pointer-events: none;
            opacity: 0.4;
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
            <?php
                $record0=getAllTestRecord($_SESSION['userid'],$_GET['page'],page);
                echo " <table  class='table table-striped'>
                            <tr>
                            <td>STT</td>
                            <td>Thời gian</td>
                            <td>Số lượng câu hỏi</td>
                            <td>Câu trả lời đúng</td>
                            <td>Kết quả</td>
                        </tr>";
                foreach ($record0 as $key => $record) {
                    echo    "<tr>
                                <td>".($key+1)."</td>
                                <td>".$record['test_at']."</td>
                                <td>".$record['question_number']."</td>
                                <td>$record[answer_number]</td>
                                <td>".(round(($score*1.0/$question_amount),2))."%</td>
                            </tr>";
                }
                echo "</table>";
                echo "<nav class='pagination' aria-label='Page navigation example'>
                        <form method='GET'>
                            <ul class='pagination'>
                                <li class='page-item'><a class='page-link' ".($_GET['page']<=0?'disabled':"")."  href='./lich_su.php?&page=".($_GET['page']-page)."&page1=$_GET[page1]' >Previous</a></li>
                                <li class='page-item'><a class='page-link' ".(count($record0)==0?'disabled':"")."    href='./lich_su.php?&page=".($_GET['page']+page)."&page1=$_GET[page1]'>Next</a></li>
                            </ul>
                        </method>
                    </nav>";
            ?>
        </div>

        <div style="clear: right;">
            <h5>Lịch sử  làm sai</h5>
            <?php
                $record1=getRecord($_GET['page1'],page,$_SESSION['userid']);
                echo " <table  class='table table-striped'>
                            <tr>
                                <td>STT</td>
                                <td>Phân loại</td>
                                <td>Câu hỏi</td>
                                <td>Số lần làm sai</td>
                                <td>Thao tác</td>
                            </tr>";
                foreach ($record1 as $key => $record) {
                    echo    "<tr>
                                <td>".($key+1)."</td>
                                <td>".$record['question']['course']['name']."</td>
                                <td>".$record['question']['content']."</td>
                                <td>$record[wrong_count]</td>
                                <td><a href='./xem_truoc.php/quesId=".($record['question']['id'])."' class='btn btn-success'>Xem trước</a></td>
                            </tr>";
                }
                echo "</table>";
                echo "<nav class='pagination' aria-label='Page navigation example'>
                        <form method='GET'>
                            <ul class='pagination'>
                                <li class='page-item'><a class='page-link' ".($_GET['page1']<=0?'disabled':"")."  href='./lich_su.php?page=$_GET[page]&page1=".($_GET['page1']-page)."' >Previous</a></li>
                                <li class='page-item'><a class='page-link' ".(count($record1)==0?'disabled':"")."    href='./lich_su.php?page=$_GET[page]&page1=".($_GET['page1']+page)."'>Next</a></li>
                            </ul>
                        </method>
                    </nav>";
            ?>
            
            
        </div>
	</main>
    <?php 
        // include 'footer.php'; 
    ?>
</body>
</html>