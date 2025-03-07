<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ url(auth()->user()->foto ?? '') }}" class="img-circle img-profil" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ auth()->user()->name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <!-- <li>
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li> -->
            @if (auth()->user()->level == 2 || auth()->user()->level == 5)
                <li>
                    <a href="{{ route('permintaan_pengembangan.index3') }}">
                        <i class="fa fa-map-o"></i> <span>List Project</span>
                    </a>
                </li>    
            @endif      
            @if (auth()->user()->level == 2)
            <!-- <li class="header">DETAIL MASTER</li> -->
            @else
            <li class="header">MASTER</li>
            @endif
            @if(auth()->user()->level == 1 || auth()->user()->level == 3 || auth()->user()->level == 5)
            <!-- <li>
                <a href="{{ route('permintaan_pengembangan.index') }}">
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
                    <li>
                        <a href="#" onclick="addForm('{{ route('permintaan_pengembangan.store') }}')">
                            <i class="fa fa-plus-circle"></i> Tambah Permintaan
                        </a>
                    </li>
                    <!-- <li><a href="{{ route('permintaan_pengembangan.forminputpengembangan') }}"><i class="fa fa-check-circle"></i>Permintaan Pengembangan</a></li> -->
                    <li><a href="{{ route('permintaan_pengembangan.index') }}"><i class="fa fa-check-circle"></i>Approved</a></li>
                    <li><a href="{{ route('permintaan_pengembangan.index2') }}"><i class="fa fa-clock-o"></i>Pending Approval</a></li>
                </ul>
            </li>
            <!-- <li>
                <a href="{{ route('persetujuan_pengembangan.index') }}">
                    <i class="fa fa-check-square-o"></i> <span>Persetujuan Pengembangan</span>
                </a>
            </li> -->
            <li>
                <a href="{{ route('perencanaan_proyek.index') }}">
                    <i class="fa fa-map-o"></i> <span>Perencanaan Proyek</span>
                </a>
            </li>
            <li>
                <a href="{{ route('perencanaan_kebutuhan.index') }}">
                    <i class="fa fa-book"></i> <span>Perencanaan Kebutuhan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('analisis_desain.index') }}">
                    <i class="fa fa-clone"></i> <span>Analisis & Desain</span>
                </a>
            </li>
            <!-- <li>
                <a href="{{ route('pengembangan_aplikasi.index') }}">
                    <i class="fa fa-code"></i> <span>Pengembangan Aplikasi</span>
                </a>
            </li> -->
            <li>
                <a href="{{ route('quality_assurance_testing.index') }}">
                    <i class="fa fa-pencil-square-o"></i> <span>Quality Assurance Testing</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user_acceptance_testing.index') }}">
                    <i class="fa fa-pencil-square-o"></i> <span>User Acceptance Testing</span>
                </a>
            </li>
            <!-- <li>
                <a href="{{ route('quality_assurance_testing.index') }}">
                    <i class="fa fa-laptop"></i> <span>Rencana Implementasi</span>
                </a>
            </li> -->
            <li>
                <a href="{{ route('serah_terima_aplikasi.index') }}">
                    <i class="fa fa-handshake-o"></i> <span>Berita Acara Serah Terima</span>
                </a>
            </li>
            <li class="header">REPORT</li>
            <li>
                <a href="{{ route('laporan.index') }}">
                    <i class="fa fa-file-pdf-o"></i> <span>Laporan</span>
                </a>
            </li>
            @endif
            @if (auth()->user()->level == 1)
            <li class="header">SYSTEM</li>
            <li>
                <a href="{{ route('user.index') }}">
                    <i class="fa fa-users"></i> <span>User</span>
                </a>
            </li>
            <li>
                <a href="{{ route('setting.index') }}">
                    <i class="fa fa-cogs"></i> <span>Pengaturan</span>
                </a>
            </li>
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
