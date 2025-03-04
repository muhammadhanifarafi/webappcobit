<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo e(url(auth()->user()->foto ?? '')); ?>" class="img-circle img-profil" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo e(auth()->user()->name); ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li>
                <a href="<?php echo e(route('dashboard')); ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <?php if(auth()->user()->level == 1 || auth()->user()->level == 2 || auth()->user()->level == 3 || auth()->user()->level == 5): ?>
            <li class="header">MASTER</li>
            <!-- <li>
                <a href="<?php echo e(route('permintaan_pengembangan.index')); ?>">
                    <i class="fa fa-envelope-o"></i> <span>Permintaan Pengembangan</span>
                </a>
            </li> -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-envelope-o"></i> <span>Permintaan Pengembangan</span>
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('permintaan_pengembangan.index')); ?>"><i class="fa fa-check-circle"></i>Approved</a></li>
                    <li><a href="<?php echo e(route('permintaan_pengembangan.index2')); ?>"><i class="fa fa-clock-o"></i>Pending Approval</a></li>
                </ul>
            </li>
            <!-- <li>
                <a href="<?php echo e(route('persetujuan_pengembangan.index')); ?>">
                    <i class="fa fa-check-square-o"></i> <span>Persetujuan Pengembangan</span>
                </a>
            </li> -->
            <li>
                <a href="<?php echo e(route('perencanaan_proyek.index')); ?>">
                    <i class="fa fa-map-o"></i> <span>Perencanaan Proyek</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('perencanaan_kebutuhan.index')); ?>">
                    <i class="fa fa-book"></i> <span>Perencanaan Kebutuhan</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('analisis_desain.index')); ?>">
                    <i class="fa fa-clone"></i> <span>Analisis & Desain</span>
                </a>
            </li>
            <!-- <li>
                <a href="<?php echo e(route('pengembangan_aplikasi.index')); ?>">
                    <i class="fa fa-code"></i> <span>Pengembangan Aplikasi</span>
                </a>
            </li> -->
            <li>
                <a href="<?php echo e(route('quality_assurance_testing.index')); ?>">
                    <i class="fa fa-pencil-square-o"></i> <span>Quality Assurance Testing</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('user_acceptance_testing.index')); ?>">
                    <i class="fa fa-pencil-square-o"></i> <span>User Acceptance Testing</span>
                </a>
            </li>
            <!-- <li>
                <a href="<?php echo e(route('quality_assurance_testing.index')); ?>">
                    <i class="fa fa-laptop"></i> <span>Rencana Implementasi</span>
                </a>
            </li> -->
            <li>
                <a href="<?php echo e(route('serah_terima_aplikasi.index')); ?>">
                    <i class="fa fa-handshake-o"></i> <span>Berita Acara Serah Terima</span>
                </a>
            </li>
            
            <?php if(auth()->user()->level == 1): ?>
            <li class="header">SYSTEM</li>
            <li>
                <a href="<?php echo e(route('user.index')); ?>">
                    <i class="fa fa-users"></i> <span>User</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route("setting.index")); ?>">
                    <i class="fa fa-cogs"></i> <span>Pengaturan</span>
                </a>
            </li>
            <?php endif; ?>
            <?php else: ?>
            <li>
                <a href="<?php echo e(route('transaksi.index')); ?>">
                    <i class="fa fa-cart-arrow-down"></i> <span>Transaksi Aktif</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('transaksi.baru')); ?>">
                    <i class="fa fa-cart-arrow-down"></i> <span>Transaksi Baru</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
<?php /**PATH /home/cobitdemoptsico/public_html/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>