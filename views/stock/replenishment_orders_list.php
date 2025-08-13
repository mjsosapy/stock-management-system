<?php $title = 'Historial de Pedidos de Reposición'; include 'views/templates/header.php'; ?>

<h2 class="mb-4">📝 Historial de Pedidos de Reposición</h2>
<p class="text-muted">
  Aquí se listan todos los pedidos de reposición que has creado.
</p>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th style="width: 15%;">ID Pedido</th>
            <th style="width: 40%;">Fecha de Creación</th>
            <th style="width: 25%;">Estado</th>
            <th style="width: 20%;" class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($data['orders']) && $data['orders']->num_rows > 0): ?>
            <?php while ($order = $data['orders']->fetch_assoc()): ?>
              <tr>
                <td class="fw-bold">#<?php echo htmlspecialchars($order['id']); ?></td>
                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                <td>
                  <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($order['status']); ?></span>
                </td>
                <td class="text-center">
                    <a href="<?php echo BASE_URL; ?>stock/viewReplenishmentOrder/<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                        👁️ Ver Detalle
                    </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
              <tr>
                <td colspan="4" class="text-center text-muted">
                  <em>No se han creado pedidos de reposición todavía.</em>
                </td>
              </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'views/templates/footer.php'; ?>  