<?php

use App\Services\BookService;

// Initialize service
$bookService = new BookService();

// Get data for landing page
$stats = $bookService->getDashboardStats();
$popularBooks = $bookService->getPopularBooks(8);
$categories = $bookService->getCategories();
$recentBooks = $bookService->getRecentBooks(8);

// Get category colors
$categoryColors = [
    'Matematika' => 'primary',
    'IPA' => 'success',
    'IPS' => 'info',
    'Bahasa' => 'warning',
    'Seni' => 'danger',
    'Teknologi' => 'purple',
    'Fiksi' => 'info',
    'Non-Fiksi' => 'success',
    'Sejarah' => 'warning',
    'Agama' => 'danger',
];

// Default categories if database is empty
$defaultCategories = [
    ['name' => 'Matematika', 'icon' => 'calculator', 'color' => 'primary'],
    ['name' => 'IPA', 'icon' => 'flask', 'color' => 'success'],
    ['name' => 'IPS', 'icon' => 'globe', 'color' => 'info'],
    ['name' => 'Bahasa', 'icon' => 'book', 'color' => 'warning'],
    ['name' => 'Seni', 'icon' => 'palette', 'color' => 'danger'],
    ['name' => 'Teknologi', 'icon' => 'laptop', 'color' => 'purple'],
];

$displayCategories = !empty($categories) ? array_map(function($cat) use ($categoryColors) {
    return [
        'name' => $cat,
        'icon' => 'book',
        'color' => $categoryColors[$cat] ?? 'primary',
    ];
}, $categories) : $defaultCategories;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Perpus Sekolah - PUSTAKA-KLIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #2e3440;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
            color: #2e3440;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .nav-link {
            color: var(--dark-color) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6f8ff7 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        
        .search-box {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-input {
            height: 50px;
            border-radius: 25px;
            padding-right: 120px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .search-button {
            position: absolute;
            right: 5px;
            top: 5px;
            height: 40px;
            border-radius: 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0 20px;
            transition: background-color 0.3s;
        }
        
        .search-button:hover {
            background-color: #2e59d9;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #4e73df 0%, #6f8ff7 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card.success {
            background: linear-gradient(135deg, #1cc88a 0%, #2ce0a3 100%);
        }
        
        .stats-card.warning {
            background: linear-gradient(135deg, #f6c23e 0%, #f7d56f 100%);
        }
        
        .stats-card.info {
            background: linear-gradient(135deg, #36b9cc 0%, #5de0e8 100%);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .category-section {
            margin-bottom: 40px;
        }
        
        .category-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 25px;
            color: var(--dark-color);
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .book-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .book-cover {
            height: 200px;
            background-size: cover;
            background-position: center;
            background-color: #e9ecef;
        }
        
        .book-info {
            padding: 15px;
        }
        
        .book-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .book-author {
            color: var(--secondary-color);
            font-size: 0.85rem;
            margin-bottom: 10px;
        }
        
        .book-category {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            background-color: rgba(78, 115, 223, 0.1);
            color: var(--primary-color);
            margin-bottom: 8px;
        }
        
        .book-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-available {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success-color);
        }
        
        .status-borrowed {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger-color);
        }
        
        .status-empty {
            background-color: rgba(134, 142, 150, 0.1);
            color: #6c757d;
        }
        
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }
        
        .footer-link {
            color: #d1d3e2;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 40px;
        }
        
        .loading-spinner.active {
            display: block;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.8rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .stats-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-book-half me-2"></i>PUSTAKA - KLIK
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">BERANDA</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#katalog">KATALOG</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kategori">KATEGORI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">TENTANG</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin" target="_blank">
                            <i class="bi bi-gear me-1"></i>ADMIN
                        </a>
                    </li>
                </ul>
                <div class="d-flex ms-3">
                    <a href="/admin/login" class="btn btn-outline-primary me-2">MASUK</a>
                    <a href="/admin/login" class="btn btn-primary">DAFTAR</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">Jelajahi Dunia Lewat Jendela Buku.</h1>
            <p class="hero-subtitle">Akses ribuan koleksi perpustakaan sekolah kapan saja</p>
            
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Cari buku, penulis, atau kategori...">
                <button class="search-button" onclick="searchBooks()">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
            
            <!-- Quick Stats -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-3">
                            <div class="stats-card success">
                                <div class="stats-icon"><i class="bi bi-book"></i></div>
                                <div class="stats-number">{{ number_format($stats['total_books'] ?? 0) }}</div>
                                <div class="stats-label">Total Buku</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stats-card warning">
                                <div class="stats-icon"><i class="bi bi-journal-bookmark"></i></div>
                                <div class="stats-number">{{ number_format($stats['total_titles'] ?? 0) }}</div>
                                <div class="stats-label">Judul Buku</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stats-card info">
                                <div class="stats-icon"><i class="bi bi-people"></i></div>
                                <div class="stats-number">{{ number_format($stats['total_members'] ?? 0) }}</div>
                                <div class="stats-label">Anggota</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stats-card">
                                <div class="stats-icon"><i class="bi bi-arrow-up-right"></i></div>
                                <div class="stats-number">{{ number_format($stats['active_loans'] ?? 0) }}</div>
                                <div class="stats-label">Sedang Dipinjam</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Results Section -->
    <section id="searchResults" class="container" style="display: none;">
        <h2 class="section-title">Hasil Pencarian</h2>
        <div class="row g-4" id="searchResultsContent">
            <!-- Results will be loaded here -->
        </div>
    </section>

    <!-- Categories Section -->
    <section id="kategori" class="category-section">
        <div class="container">
            <h2 class="section-title">Jelajahi Kategori</h2>
            <div class="row g-4">
                @foreach($displayCategories as $category)
                <div class="col-md-2 col-sm-4 col-6">
                    <a href="#" class="category-card" onclick="filterByCategory('{{ $category['name'] }}'); return false;">
                        <div class="category-icon text-{{ $category['color'] }}">
                            <i class="bi bi-{{ $category['icon'] }}"></i>
                        </div>
                        <h5>{{ $category['name'] }}</h5>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Popular Books Section -->
    <section id="katalog" class="popular-books-section">
        <div class="container">
            <h2 class="section-title">Buku Populer</h2>
            <div class="loading-spinner active" id="booksLoading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat data buku...</p>
            </div>
            <div class="row g-4" id="booksContainer">
                @if($popularBooks->count() > 0)
                    @foreach($popularBooks as $book)
                    <div class="col-md-3 col-sm-6">
                        <div class="book-card">
                            <div class="book-cover" style="background-image: url('https://picsum.photos/seed/{{ urlencode($book->title) }}/300/400.jpg');"></div>
                            <div class="book-info">
                                @if($book->category)
                                <span class="book-category">{{ $book->category }}</span>
                                @endif
                                <h5 class="book-title">{{ $book->title }}</h5>
                                <p class="book-author">{{ $book->author }}</p>
                                @if($book->quantity > 0)
                                <span class="book-status status-available">
                                    <i class="bi bi-check-circle me-1"></i> Tersedia ({{ $book->quantity }})
                                </span>
                                @else
                                <span class="book-status status-empty">
                                    <i class="bi bi-x-circle me-1"></i> Kosong
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <!-- Fallback static books if database is empty -->
                    @php
                    $fallbackBooks = [
                        ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'category' => 'Fiksi', 'stock' => 3],
                        ['title' => 'Bumi', 'author' => 'Tere Liye', 'category' => 'Fiksi', 'stock' => 2],
                        ['title' => 'Sang Pemimpi', 'author' => 'Andrea Hirata', 'category' => 'Fiksi', 'stock' => 0],
                        ['title' => 'Pulang', 'author' => 'Tere Liye', 'category' => 'Fiksi', 'stock' => 5],
                        ['title' => 'Negeri Para Bedebah', 'author' => 'Tere Liye', 'category' => 'Fiksi', 'stock' => 1],
                        ['title' => 'Senja', 'author' => 'Pidi Baiq', 'category' => 'Fiksi', 'stock' => 4],
                        ['title' => 'Ranah 3 Warna', 'author' => 'A. Fuadi', 'category' => 'Fiksi', 'stock' => 0],
                        ['title' => 'Cantik Itu Luka', 'author' => 'Eka Kurniawan', 'category' => 'Fiksi', 'stock' => 2],
                    ];
                    @endphp
                    @foreach($fallbackBooks as $book)
                    <div class="col-md-3 col-sm-6">
                        <div class="book-card">
                            <div class="book-cover" style="background-image: url('https://picsum.photos/seed/{{ urlencode($book['title']) }}/300/400.jpg');"></div>
                            <div class="book-info">
                                <span class="book-category">{{ $book['category'] }}</span>
                                <h5 class="book-title">{{ $book['title'] }}</h5>
                                <p class="book-author">{{ $book['author'] }}</p>
                                @if($book['stock'] > 0)
                                <span class="book-status status-available">
                                    <i class="bi bi-check-circle me-1"></i> Tersedia ({{ $book['stock'] }})
                                </span>
                                @else
                                <span class="book-status status-borrowed">
                                    <i class="bi bi-x-circle me-1"></i> Dipinjam (0)
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- Recent Books Section -->
    <section class="recent-books-section" style="margin-top: 40px;">
        <div class="container">
            <h2 class="section-title">Buku Terbaru</h2>
            <div class="row g-4" id="recentBooksContainer">
                @if($recentBooks->count() > 0)
                    @foreach($recentBooks as $book)
                    <div class="col-md-3 col-sm-6">
                        <div class="book-card">
                            <div class="book-cover" style="background-image: url('https://picsum.photos/seed/{{ urlencode($book->title . '_new') }}/300/400.jpg');"></div>
                            <div class="book-info">
                                @if($book->category)
                                <span class="book-category">{{ $book->category }}</span>
                                @endif
                                <h5 class="book-title">{{ $book->title }}</h5>
                                <p class="book-author">{{ $book->author }}</p>
                                @if($book->quantity > 0)
                                <span class="book-status status-available">
                                    <i class="bi bi-check-circle me-1"></i> Tersedia ({{ $book->quantity }})
                                </span>
                                @else
                                <span class="book-status status-empty">
                                    <i class="bi bi-x-circle me-1"></i> Kosong
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" style="margin-top: 60px; padding: 60px 0; background-color: white;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="section-title">Tentang PUSTAKA-KLIK</h2>
                    <p class="text-muted">PUSTAKA-KLIK adalah sistem perpustakaan digital modern untuk sekolah-sekolah di Indonesia. Platform ini dirancang untuk memudahkan siswa dan staf dalam mengakses koleksi perpustakaan secara online.</p>
                    
                    <div class="row mt-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded p-2">
                                        <i class="bi bi-book text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Koleksi Lengkap</h6>
                                    <small class="text-muted">Ribuan buku dari berbagai kategori</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded p-2">
                                        <i class="bi bi-clock-history text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Akses 24/7</h6>
                                    <small class="text-muted">Kapan saja dan di mana saja</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 rounded p-2">
                                        <i class="bi bi-search text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Pencarian Cepat</h6>
                                    <small class="text-muted">Temukan buku dalam hitungan detik</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 rounded p-2">
                                        <i class="bi bi-bell text-info fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Notifikasi</h6>
                                    <small class="text-muted">Ingatkan tanggal jatuh tempo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <img src="https://picsum.photos/seed/library/600/400" alt="Perpustakaan" class="img-fluid rounded shadow" style="max-width: 100%;">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">E-Perpus Sekolah</h5>
                    <p>Platform perpustakaan digital untuk sekolah-sekolah di Indonesia. Memudahkan akses buku dan literasi untuk siswa.</p>
                    <div class="mt-3">
                        <a href="#" class="footer-link me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="footer-link me-3"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="footer-link me-3"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="footer-link"><i class="bi bi-youtube fs-5"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="mb-3">Menu</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Beranda</a></li>
                        <li class="mb-2"><a href="#katalog" class="footer-link">Koleksi</a></li>
                        <li class="mb-2"><a href="#kategori" class="footer-link">Kategori</a></li>
                        <li class="mb-2"><a href="#tentang" class="footer-link">Tentang</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Layanan</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Panduan</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">FAQ</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Kebijakan Privasi</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Kontak</h5>
                    <p><i class="bi bi-geo-alt me-2"></i>Jl. Pendidikan No. 123, Jakarta</p>
                    <p><i class="bi bi-telephone me-2"></i>(021) 1234-5678</p>
                    <p><i class="bi bi-envelope me-2"></i>info@eperpussekolah.sch.id</p>
                </div>
            </div>
            <hr class="my-4" style="background-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} E-Perpus Sekolah. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        function searchBooks() {
            const query = document.getElementById('searchInput').value.trim();
            if (query.length < 2) {
                alert('Masukkan minimal 2 karakter untuk mencari');
                return;
            }
            
            // Show loading
            document.getElementById('searchResults').style.display = 'block';
            document.getElementById('booksLoading').classList.add('active');
            
            // Scroll to results
            document.getElementById('searchResults').scrollIntoView({ behavior: 'smooth' });
            
            // Fetch search results via API
            fetch(`/api/books/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('searchResultsContent');
                    
                    if (data.length > 0) {
                        let html = '';
                        data.forEach(book => {
                            const statusClass = book.quantity > 0 ? 'status-available' : 'status-empty';
                            const statusText = book.quantity > 0 ? `Tersedia (${book.quantity})` : 'Kosong';
                            const statusIcon = book.quantity > 0 ? 'check-circle' : 'x-circle';
                            
                            html += `
                                <div class="col-md-3 col-sm-6">
                                    <div class="book-card">
                                        <div class="book-cover" style="background-image: url('https://picsum.photos/seed/${encodeURIComponent(book.title)}/300/400.jpg');"></div>
                                        <div class="book-info">
                                            ${book.category ? `<span class="book-category">${book.category}</span>` : ''}
                                            <h5 class="book-title">${book.title}</h5>
                                            <p class="book-author">${book.author}</p>
                                            <span class="book-status ${statusClass}">
                                                <i class="bi bi-${statusIcon} me-1"></i> ${statusText}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-search display-1 text-muted"></i>
                                <h4 class="mt-3">Tidak ada hasil</h4>
                                <p class="text-muted">Coba kata kunci lain</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    document.getElementById('searchResultsContent').innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-exclamation-circle display-1 text-danger"></i>
                            <h4 class="mt-3">Terjadi kesalahan</h4>
                            <p class="text-muted">Silakan coba lagi</p>
                        </div>
                    `;
                })
                .finally(() => {
                    document.getElementById('booksLoading').classList.remove('active');
                });
        }
        
        // Filter by category
        function filterByCategory(category) {
            document.getElementById('searchResults').style.display = 'block';
            document.getElementById('searchResults').scrollIntoView({ behavior: 'smooth' });
            
            const container = document.getElementById('searchResultsContent');
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat buku kategori "${category}"...</p>
                </div>
            `;
            
            fetch(`/api/books/category/${encodeURIComponent(category)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let html = `<div class="col-12 mb-3"><h5>Kategori: ${category}</h5></div>`;
                        data.forEach(book => {
                            const statusClass = book.quantity > 0 ? 'status-available' : 'status-empty';
                            const statusText = book.quantity > 0 ? `Tersedia (${book.quantity})` : 'Kosong';
                            const statusIcon = book.quantity > 0 ? 'check-circle' : 'x-circle';
                            
                            html += `
                                <div class="col-md-3 col-sm-6">
                                    <div class="book-card">
                                        <div class="book-cover" style="background-image: url('https://picsum.photos/seed/${encodeURIComponent(book.title)}/300/400.jpg');"></div>
                                        <div class="book-info">
                                            ${book.category ? `<span class="book-category">${book.category}</span>` : ''}
                                            <h5 class="book-title">${book.title}</h5>
                                            <p class="book-author">${book.author}</p>
                                            <span class="book-status ${statusClass}">
                                                <i class="bi bi-${statusIcon} me-1"></i> ${statusText}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-book display-1 text-muted"></i>
                                <h4 class="mt-3">Tidak ada buku</h4>
                                <p class="text-muted">Tidak ada buku dalam kategori ini</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Category filter error:', error);
                    container.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-exclamation-circle display-1 text-danger"></i>
                            <h4 class="mt-3">Terjadi kesalahan</h4>
                        </div>
                    `;
                });
        }
        
        // Enter key search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchBooks();
            }
        });
        
        // Hide search results when clicking elsewhere
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-box') && !e.target.closest('#searchResults')) {
                document.getElementById('searchResults').style.display = 'none';
            }
        });
    </script>
</body>
</html>

