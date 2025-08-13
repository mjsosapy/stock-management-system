<?php $title = 'Panel Principal'; include 'views/templates/header.php'; ?>
<div class="container mt-4">
  <div class="row align-items-md-stretch">
    <div class="col-md-6 mb-4">
      <div class="h-100 p-4 text-white bg-success rounded-3 shadow">
        <h2><a href="<?php echo BASE_URL; ?>stock/add" class="text-white text-decoration-none">➕ Agregar Producto</a></h2>
        <p>Crea un nuevo artículo en el maestro de inventario con su stock inicial.</p>
        <a href="<?php echo BASE_URL; ?>stock/add" class="btn btn-outline-light">Ir a Agregar</a>
      </div>
    </div>
    <div class="col-md-6 mb-4">
      <div class="h-100 p-4 bg-warning border rounded-3 shadow">
        <h2><a href="<?php echo BASE_URL; ?>stock/issue" class="text-dark text-decoration-none">📤 Registrar Salida</a></h2>
        <p>Registra la entrega de un artículo a un destinatario y departamento.</p>
        <a href="<?php echo BASE_URL; ?>stock/issue" class="btn btn-outline-dark">Ir a Registrar</a>
      </div>
    </div>
  </div>

  <div class="row align-items-md-stretch">
    <div class="col-md-6 mb-4">
        <div class="h-100 p-4 bg-info text-dark rounded-3 shadow">
            <h2><a href="<?php echo BASE_URL; ?>stock/replenish" class="text-dark text-decoration-none">🔄 Reponer Stock</a></h2>
            <p>Registra la entrada de nueva mercancía desde un proveedor.</p>
            <a href="<?php echo BASE_URL; ?>stock/replenish" class="btn btn-outline-dark">Ir a Reponer</a>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="h-100 p-4 bg-danger text-white border rounded-3 shadow">
            <h2><a href="<?php echo BASE_URL; ?>stock/returnDefective" class="text-white text-decoration-none">↪️ Devolución por Falla</a></h2>
            <p>Registra la devolución de un producto defectuoso al proveedor.</p>
            <a href="<?php echo BASE_URL; ?>stock/returnDefective" class="btn btn-outline-light">Registrar Devolución</a>
        </div>
    </div>
  </div>
</div>
<?php include 'views/templates/footer.php'; ?>
