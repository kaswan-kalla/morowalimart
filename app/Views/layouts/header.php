<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta_title ?? 'Marketplace - Multi Vendor' ?></title>
    <meta name="description" content="<?= $meta_description ?? 'Marketplace Multi Vendor - Belanja Online Terpercaya' ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= asset_url('asset/pavicon.ico') ?>" type="image/x-icon">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --warning: #ffc107;
            --danger: #dc3545;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .product-card img {
            border-radius: 12px 12px 0 0;
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .badge-discount {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--danger);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-wishlist {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-wishlist:hover,
        .btn-wishlist.active {
            color: var(--danger);
        }

        .price-original {
            text-decoration: line-through;
            color: var(--secondary);
            font-size: 0.85rem;
        }

        .price-current {
            font-weight: 700;
            color: var(--danger);
            font-size: 1.1rem;
        }

        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 240px;
            height: calc(100vh - 70px);
            max-height: calc(100vh - 70px);
            background: white;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
            z-index: 100;
        }

        .main-content {
            margin-left: 240px;
        }

        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
                position: fixed;
                top: 70px;
                left: 0;
                width: 280px;
                height: calc(100vh - 70px);
                max-height: calc(100vh - 70px);
                z-index: 1051;
                box-shadow: 2px 0 12px rgba(0, 0, 0, 0.2);
            }

            .sidebar.show {
                display: block;
            }

            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 70px;
                left: 0;
                width: 100%;
                height: calc(100vh - 70px);
                background: rgba(0, 0, 0, 0.5);
                z-index: 1050;
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .sidebar-toggle {
                display: flex;
                position: fixed;
                top: 78px;
                left: 10px;
                z-index: 1040;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border: none;
                border-radius: 10px;
                background: var(--primary);
                color: white;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }

            .main-content {
                margin-left: 0;
            }
        }

        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 8px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }

        /* Preview cetak kertas F4 */
        .print-preview .paper {
            width: 100%;
            max-width: 794px;
            min-height: 1248px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .print-preview .paper .print-kop {
            display: block !important;
            text-align: center;
            margin-bottom: 10px;
        }

        .print-preview .paper .print-kop h2 {
            font-size: 22px;
            margin: 0;
            font-weight: bold;
        }

        .print-preview .paper .print-kop p {
            font-size: 11px;
            margin: 2px 0;
        }

        .print-preview .paper .print-kop hr {
            border-top: 2px solid #000;
            margin-bottom: 8px;
        }

        .print-preview .paper .print-title {
            display: block !important;
            text-align: center;
            font-size: 14pt;
            margin-bottom: 15px;
        }

        .print-preview .paper .btn {
            display: none !important;
        }

        .print-preview .paper .d-flex.justify-content-between {
            display: none !important;
        }

        .print-preview .paper .card:has(form) {
            display: none !important;
        }

        @media (max-width: 768px) {
            .print-preview .paper {
                min-height: auto;
                padding: 12px;
            }
        }

        /* Capture: abaikan ukuran kertas, semua elemen laporan */
        .print-preview .paper.paper-capture {
            max-width: none;
            min-height: 0;
            padding: 16px;
            box-shadow: none;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9998;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        .img-placeholder {
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 2rem;
        }

        footer {
            background: #343a40;
            color: #adb5bd;
            padding: 40px 0 20px;
            margin-top: 60px;
        }

        footer a {
            color: #adb5bd;
            text-decoration: none;
        }

        footer a:hover {
            color: white;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body>