<?php
// Get Css hover
$navs          = getCssOfMenu('mode') ?? array();
$navLinkActive = $navs['navLinkActive'] ?? '';
$navLinkOnlick = $navs['navLinkOnlick'] ?? 'info';
$name = $_SESSION['fullName'] ?? '';

$menuManageAdmin = '';
if($_SESSION['role'] == 1){
    $menuManageAdmin .= <<< EOF
        <li class="nav-item nav-link-new">
            <a href="javascript:void(0)" class="nav-link">
                <i class="fas fa-user-shield nav-icon"></i>
                <p>
                    Quản lí quản trị viên
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="detail-admin.php" class="nav-link nav-link-new-detail">
                        <i class="fas fa-plus-square nav-icon"></i>
                        <p>Thêm quản trị viên</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage-admin.php" class="nav-link nav-link-new-list">
                        <i class="fas fa-list-ul nav-icon"></i>
                        <p>Danh sách quản trị viên</p>
                    </a>
                </li>
            </ul>
            </li>
EOF;
}

//Output HTML
print <<<EOF
<aside class="main-sidebar sidebar-dark-primary elevation-4">
<!-- Brand Logo -->
<a href="#" class="brand-link">
    <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">Hệ thống quản lý</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image" style="color: #fff; font-size: 140%;">
            <i class="nav-icon fas fa-user-circle"></i>
        </div>
        <div class="info">
            <a href="update-profile.php" class="d-block">Xin chào {$name}</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="home.php" class="nav-link nav-link-dashboard">
                    <i class="nav-icon fas fa-home"></i>
                    <p>Trang chủ</p>
                </a>
            </li>
            <li class="nav-item nav-link-new">
                <a href="checkinOut.php" class="nav-link">
                    <i class="fas fa-user-check nav-icon"></i>
                    <p>
                        Checkin / checkout
                    </p>
                </a>
            </li>
            {$menuManageAdmin}
            <li class="nav-item nav-link-new">
                <a href="javascript:void(0)" class="nav-link">
                    <i class="fas fa-clipboard-list nav-icon"></i>
                    <p>
                        Quản lí vị trí
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="detail-postion.php" class="nav-link nav-link-new-detail">
                            <i class="fas fa-plus-square nav-icon"></i>
                            <p>Thêm vị trí</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage-postion.php" class="nav-link nav-link-new-list">
                            <i class="fas fa-list-ul nav-icon"></i>
                            <p>Danh sách các vị trí</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-link-new">
                <a href="javascript:void(0)" class="nav-link">
                    <i class="fas fa-users nav-icon"></i>
                    <p>
                        Quản lý nhân sự
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="detail-member.php" class="nav-link nav-link-new-detail">
                            <i class="fas fa-plus-square nav-icon"></i>
                            <p>Thêm nhân sự</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manage-member.php" class="nav-link nav-link-new-list">
                            <i class="fas fa-list-ul nav-icon"></i>
                            <p>Danh sách nhân sự</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-link-new">
                <a href="javascript:void(0)" class="nav-link">
                    <i class="nav-icon fas fa-newspaper"></i>
                    <p>
                        Quản lí bài viết
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="detail-news.php" class="nav-link nav-link-new-detail">
                            <i class="fas fa-plus-square nav-icon"></i>
                            <p>Thêm bài viết</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="list-news.php" class="nav-link nav-link-new-list">
                            <i class="fas fa-list-ul nav-icon"></i>
                            <p>Danh sách bài viết</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-link-new">
                <a href="send-request.php" class="nav-link">
                    <i class="fas fa-envelope-open-text nav-icon"></i>
                    <p>
                        Gửi email
                    </p>
                </a>
            </li>
            <li class="nav-item nav-link-new">
                <a href="javascript:void(0)" class="nav-link">
                    <i class="fas fa-user-cog nav-icon"></i>
                    <p>
                        Cài đặt hệ thống
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="detail-news.php" class="nav-link nav-link-new-detail">
                            <i class="fas fa-plus-square nav-icon"></i>
                            <p>Thêm bài viết</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="list-news.php" class="nav-link nav-link-new-list">
                            <i class="fas fa-list-ul nav-icon"></i>
                            <p>Danh sách bài viết</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <a href="logout.php" style="position: absolute; bottom: 0; margin-bottom: 20px">
            <i class="fas fa-sign-out-alt nav-icon" style="font-size: 20px"></i>&nbsp
            Đăng xuất
        </a>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
</aside>
EOF;
?>