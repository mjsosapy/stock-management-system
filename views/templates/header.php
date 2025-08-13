<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title ?? 'Gestión de Stock'; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
        --primary-color: #343a40;
        --accent-color: #007bff;
        --text-color: #ffffff;
        --text-hover-bg: #495057;
        --border-radius: 12px;
    }
    body { 
        padding-top: 90px;
        background-color: #f8f9fa; 
    }
    .navbar.modern-nav {
        background-color: var(--primary-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 0.8rem 1.5rem;
        transition: all 0.3s ease-in-out;
    }
    .navbar.modern-nav .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--text-color);
    }
    .navbar.modern-nav .nav-link {
        color: var(--text-color);
        font-weight: 500;
        padding: 0.6rem 1rem;
        border-radius: var(--border-radius);
        transition: all 0.2s ease-in-out;
        position: relative;
        margin: 0 0.2rem;
    }
    .navbar.modern-nav .nav-link:hover,
    .navbar.modern-nav .nav-link.active {
        background-color: var(--text-hover-bg);
        color: var(--text-color);
    }
    .navbar-user-info .navbar-text {
        color: var(--text-color);
        font-weight: 600;
        margin-right: 1rem !important;
    }
    .btn-logout {
        background-color: #6c757d;
        border: none;
        color: white;
        font-weight: 600;
        border-radius: var(--border-radius);
        transition: all 0.2s ease-in-out;
    }
    .btn-logout:hover {
        background-color: #5a6268;
        transform: scale(1.05);
    }
    .nav-link i {
        margin-right: 8px;
    }
    .toast-container {
        position: fixed; top: 80px; right: 20px; z-index: 1050; display: flex;
        flex-direction: column; gap: 10px;
    }
    .custom-toast {
        min-width: 320px; color: white; border-radius: 0.5rem; padding: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); opacity: 0;
        transform: translateX(100%);
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    }
    .custom-toast.show { opacity: 1; transform: translateX(0); }
    .toast-header {
        display: flex; justify-content: space-between; align-items: center; font-weight: bold;
    }
    .toast-success { background-color: #28a745; }
    .toast-error { background-color: #dc3545; }
    .badge-negro { background-color: #212529 !important; color: white; }
    .badge-gradient-color {
        background-image: linear-gradient(to right, #00aeef, #ec008c, #fff200);
        color: white;
    }
    
    /* Dashboard Analítico Styles */
    .dashboard-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: none;
        border-radius: 15px;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .kpi-card {
        background: linear-gradient(135deg, var(--card-bg-start), var(--card-bg-end));
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .kpi-card.bg-primary {
        --card-bg-start: #007bff;
        --card-bg-end: #0056b3;
    }
    .kpi-card.bg-warning {
        --card-bg-start: #ffc107;
        --card-bg-end: #e0a800;
    }
    .kpi-card.bg-success {
        --card-bg-start: #28a745;
        --card-bg-end: #1e7e34;
    }
    .kpi-card.bg-danger {
        --card-bg-start: #dc3545;
        --card-bg-end: #c82333;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin: 10px 0;
    }
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<?php if (isset($_SESSION['user'])): ?>
  <nav class="navbar navbar-expand-lg fixed-top modern-nav">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?php echo BASE_URL; ?>stock/dashboard"><i class="fas fa-cubes-stacked"></i> Gestión Stock</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>stock/dashboard"><i class="fas fa-home"></i>Panel</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>stock/report"><i class="fas fa-box-open"></i>Stock</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>stock/lowStockReport"><i class="fas fa-triangle-exclamation"></i>Alertas</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>stock/replenishmentOrders"><i class="fas fa-clipboard-list"></i>Pedidos</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>stock/returnDefective"><i class="fas fa-undo"></i>Devoluciones</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>history/movements"><i class="fas fa-history"></i>Historial</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>cost-analysis/index"><i class="fas fa-money-bill-trend-up"></i>Costos</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>report/detailed"><i class="fas fa-chart-pie"></i>Reportes</a></li>
        </ul>
        <div class="d-flex align-items-center navbar-user-info">
            <span class="navbar-text">Sistema de Gestión de Stock</span>
        </div>
      </div>
    </div>
  </nav>
<?php endif; ?>

<div id="toast-container" class="toast-container"></div>
<div class="container py-5">