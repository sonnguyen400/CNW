<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="../index.php">ProjectPHP K71</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
             <?php
                $query="Select * from user where id='$_SESSION[userid]'";
                $result=mysqli_fetch_assoc(mysqli_query($conn,$query));
                echo "Tài khoản $result[userName]";
             ?>
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item" href="./lich_su.php?page=0&page1=0">Lịch sử</a></li>
              <li><a class="dropdown-item" href="./test_list.php?page=0&page1=0">Bài kiểm tra</a></li>
            <li><a class="dropdown-item" href="./dang_nhap.php?action=logout">Đăng xuất</a></li>
          </ul>
        </li>
      </ul>
    </div>
    <?php
        if($_SESSION['role']==ADMIN){
            echo "";
        }
    ?>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
             ADMIN
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <li><a class="dropdown-item" href="./create_test.php?page=0">Tạo bài kiểm tra</a></li>
            <li><a class="dropdown-item" href="./lich_su.php?page=0&page1=0">Lịch sử</a></li>
          </ul>
        </li>
      </ul>
    </div>

  </div>
</nav>