<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profil_sekolah->nama_sekolah ?? 'Sistem Manajemen Aset' }} - @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Toastr & SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
    
    <!-- jQuery (Required for Toastr and Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header" style="flex-direction: column; align-items: flex-start; padding: 1.5rem; gap: 0.5rem; height: auto;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem; width: 100%;">
                    @if(isset($profil_sekolah) && $profil_sekolah->logo)
                        <img src="{{ asset('uploads/' . $profil_sekolah->logo) }}" alt="Logo" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; background: white; padding: 2px; flex-shrink: 0;">
                    @else
                        <div class="logo-icon" style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="fa-solid fa-school"></i>
                        </div>
                    @endif
                    
                    <div style="display: flex; flex-direction: column; justify-content: center; flex: 1;">
                        <h2 style="font-size: 1rem; line-height: 1.3; margin: 0 0 0.2rem 0; white-space: normal; word-break: break-word; color: white;" title="{{ $profil_sekolah->nama_sekolah ?? 'Nama Sekolah' }}">
                            {{ $profil_sekolah->nama_sekolah ?? 'Nama Sekolah' }}
                        </h2>
                        <span style="font-size: 0.75rem; color: rgba(255,255,255,0.7); white-space: normal; word-break: break-word; line-height: 1.3;" title="{{ $profil_sekolah->sub_nama ?? 'Sub Nama Sekolah' }}">
                            {{ $profil_sekolah->sub_nama ?? 'Sub Nama Sekolah' }}
                        </span>
                    </div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-group-title">Utama</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                
                <div class="nav-group-title" style="margin-top: 1rem;">Master Data</div>
                <a href="{{ route('aset.index') }}" class="nav-item {{ request()->routeIs('aset.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Barang & Aset</span>
                </a>
                <a href="{{ route('ruangan.index') }}" class="nav-item {{ request()->routeIs('ruangan.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-door-open"></i>
                    <span>Ruangan</span>
                </a>
                <a href="{{ route('kategori.index') }}" class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('sumber-dana.index') }}" class="nav-item {{ request()->routeIs('sumber-dana.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sack-dollar"></i>
                    <span>Sumber Dana</span>
                </a>

                <div class="nav-group-title">Transaksi</div>
                <a href="{{ route('mutasi.index') }}" class="nav-item {{ request()->routeIs('mutasi.*') || request()->routeIs('kondisi.*') || request()->routeIs('peminjaman.*') || request()->routeIs('barang-keluar.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-right-left"></i>
                    <span>Transaksi Aset</span>
                </a>

                <div class="nav-group-title">Pelaporan</div>
                <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Laporan & Filter</span>
                </a>
            </nav>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="page-title">
                        <h1>@yield('title')</h1>
                    </div>
                </div>
                
                <div class="dropdown" id="profileDropdown">
                    <div class="user-profile" onclick="toggleDropdown(event)">
                        <div class="avatar">A</div>
                        <span>Admin Sekolah</span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem; margin-left: 0.25rem;"></i>
                    </div>
                    <div class="dropdown-menu">
                        <div class="dropdown-header" style="padding: 0.5rem 1.25rem; font-weight: bold; color: var(--text-main); font-size: 0.85rem;">
                            Pengaturan
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('profil.sekolah') }}" class="dropdown-item">
                            <i class="fa-solid fa-school"></i> Profil Sekolah
                        </a>
                        <a href="{{ route('profil.akun') }}" class="dropdown-item">
                            <i class="fa-solid fa-user-pen"></i> Profil Akun
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item" style="color: var(--danger);" onclick="event.preventDefault(); Swal.fire({title: 'Logout', text: 'Yakin ingin keluar?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Logout', confirmButtonColor: '#f1556c'}).then((result) => { if (result.isConfirmed) { document.getElementById('logout-form').submit(); } });">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                        <form id="logout-form" action="#" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Toastr notifications will handle session messages -->

                @yield('content')
            </div>
        </main>
    </div>
    
    <script>
        // Global Table Search Logic
        document.querySelectorAll('.table-search').forEach(input => {
            input.addEventListener('keyup', function() {
                let filter = this.value.toLowerCase();
                let card = this.closest('.card');
                if(!card) return;
                
                let table = card.querySelector('.table');
                if(!table) return;
                
                let trs = table.querySelectorAll('tbody tr');
                
                trs.forEach(tr => {
                    let textContent = tr.textContent.toLowerCase();
                    if (textContent.indexOf(filter) > -1) {
                        tr.style.display = "";
                    } else {
                        tr.style.display = "none";
                    }
                });
            });
        });

        // Sidebar Toggle Logic
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.toggle('mobile-open');
                document.getElementById('sidebarOverlay').classList.toggle('show');
            } else {
                document.getElementById('sidebar').classList.toggle('collapsed');
            }
        });

        // Close sidebar on overlay click (mobile)
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                document.getElementById('sidebar').classList.remove('mobile-open');
                this.classList.remove('show');
            });
        }

        // Dropdown Toggle Logic
        function toggleDropdown(event) {
            event.stopPropagation();
            document.getElementById('profileDropdown').classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                const dropdowns = document.getElementsByClassName('dropdown');
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        });

        // Toastr Configuration & Execution
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif

        // Row Checkbox Toggle Logic
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('table tbody tr').forEach(tr => {
                let checkbox = tr.querySelector('.unit-checkbox');
                if (checkbox) {
                    tr.style.cursor = 'pointer';
                    tr.addEventListener('click', function(e) {
                        // Jangan toggle jika yang di klik adalah checkbox itu sendiri, tombol, input lain, atau link
                        if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A' && !e.target.closest('button') && !e.target.closest('a')) {
                            checkbox.checked = !checkbox.checked;
                            // Trigger change event agar logika background warna (jika ada) berjalan
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
