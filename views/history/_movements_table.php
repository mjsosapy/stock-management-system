<div class="table-responsive">
  <table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
      <tr>
        <th>Fecha y Hora</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Color</th>
        <th>Tipo de Movimiento</th>
        <th>Cambio</th>
        <th>Detalles</th>
        <th>Referencia / Motivo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($data['movements']->num_rows > 0): ?>
        <?php while ($row = $data['movements']->fetch_assoc()): ?>
          <?php 
            $rowClass = ($row['is_reversed'] > 0 || $row['movement_type'] == 'SALE_REVERSAL' || $row['movement_type'] == 'DEACTIVATED') ? 'text-muted' : ''; 

            // Lógica mejorada para mostrar detalles
            $main_detail = '';
            $secondary_detail = '';
            $movement_type = $row['movement_type'];

            if ($movement_type == 'SALE') {
                $main_detail = '<strong>Entregado a:</strong> ' . htmlspecialchars($row['recipient_name'] ?? 'N/A');
                $secondary_detail = '<strong>Dpto:</strong> ' . htmlspecialchars($row['department_name'] ?? 'N/A');
            } elseif ($movement_type == 'REPLENISHMENT') {
                $parts = explode('. Referencia:', $row['reason']);
                $main_detail = htmlspecialchars($parts[0]);
                if (isset($parts[1])) {
                    $secondary_detail = htmlspecialchars(trim($parts[1]));
                }
            } elseif ($movement_type == 'SALE_REVERSAL') {
                $parts = explode('. (Anula', $row['reason']);
                $main_detail = htmlspecialchars($parts[0]);
                if (isset($parts[1])) {
                    $secondary_detail = '(Anula' . htmlspecialchars($parts[1]);
                }
            } else {
                $main_detail = htmlspecialchars($row['reason'] ?? 'N/A');
            }
          ?>
          <tr class="<?php echo $rowClass; ?>">
            <td><?php echo date('d/m/Y H:i', strtotime($row['movement_date'])); ?></td>
            <td><?php echo htmlspecialchars($row['brand'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['model'] ?? ''); ?></td>
            <td>
                <?php
                $color = htmlspecialchars($row['color'] ?? 'Sin especificar');
                if ($color !== 'Sin especificar') {
                    $color_class = 'bg-secondary';
                    if ($color === 'Negro') {
                        $color_class = 'badge-negro';
                    } elseif ($color === 'Color') {
                        $color_class = 'badge-gradient-color';
                    }
                    ?>
                    <span class="badge <?php echo $color_class; ?>"><?php echo $color; ?></span>
                    <?php
                } else {
                    ?>
                    <span class="text-muted">-</span>
                    <?php
                }
                ?>
            </td>
            <td>
              <?php 
                $badge_class = 'bg-secondary'; $translated_type = 'DESCONOCIDO';
                switch ($row['movement_type']) {
                    case 'INITIAL_STOCK': $badge_class = 'bg-primary'; $translated_type = 'STOCK INICIAL'; break;
                    case 'REPLENISHMENT': $badge_class = 'bg-info text-dark'; $translated_type = 'REPOSICIÓN'; break;
                    case 'SALE': $badge_class = 'bg-success'; $translated_type = 'SALIDA / ENTREGA'; break;
                    case 'RETURN_DEFECTIVE': $badge_class = 'bg-danger'; $translated_type = 'DEVOLUCIÓN POR FALLA'; break;
                    case 'SALE_REVERSAL': $badge_class = 'bg-dark'; $translated_type = 'REVERSIÓN DE SALIDA'; break;
                    case 'DEACTIVATED': $badge_class = 'bg-secondary'; $translated_type = 'DADO DE BAJA'; break;
                }
              ?>
              <span class="badge <?php echo $badge_class; ?>"><?php echo $translated_type; ?></span>
            </td>
            <td class="fw-bold <?php echo ($row['quantity_change'] > 0) ? 'text-success' : ($row['quantity_change'] < 0 ? 'text-danger' : ''); ?>">
              <?php echo ($row['quantity_change'] > 0) ? '+' : ''; ?><?php echo $row['quantity_change']; ?>
            </td>
            <td>
                <?php echo $main_detail; ?>
                <?php if ($row['is_reversed'] > 0): ?><strong class="text-danger d-block">(Revertido)</strong><?php endif; ?>
            </td>
            <td>
                <?php echo $secondary_detail; ?>
            </td>
            <td>
              <?php if (($row['movement_type'] == 'SALE' || $row['movement_type'] == 'INITIAL_STOCK') && $row['is_reversed'] == 0): ?>
                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#reversalModal" data-movement-id="<?php echo $row['id']; ?>">
                  Revertir
                </button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="9" class="text-center">No se encontraron movimientos con los filtros seleccionados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if (isset($data['total_pages']) && $data['total_pages'] > 1): ?>
<nav>
  <ul class="pagination justify-content-center">
    <?php 
      $query_params = http_build_query(array_filter($data['filters']));
      $page = $data['page'];
      $total_pages = $data['total_pages'];
    ?>
    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
      <a class="page-link" href="<?php echo BASE_URL; ?>history/ajaxFilter?page=<?php echo $page - 1; ?>&<?php echo $query_params; ?>">Anterior</a>
    </li>
    <?php
    $start_page = max(1, $page - 2);
    $end_page = min($total_pages, $page + 2);
    if ($start_page > 1): ?>
      <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>history/ajaxFilter?page=1&<?php echo $query_params; ?>">1</a></li>
      <?php if ($start_page > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
    <?php endif; ?>
    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
      <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
        <a class="page-link" href="<?php echo BASE_URL; ?>history/ajaxFilter?page=<?php echo $i; ?>&<?php echo $query_params; ?>"><?php echo $i; ?></a>
      </li>
    <?php endfor; ?>
    <?php if ($end_page < $total_pages): ?>
      <?php if ($end_page < $total_pages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
      <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>history/ajaxFilter?page=<?php echo $total_pages; ?>&<?php echo $query_params; ?>"><?php echo $total_pages; ?></a></li>
    <?php endif; ?>
    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
      <a class="page-link" href="<?php echo BASE_URL; ?>history/ajaxFilter?page=<?php echo $page + 1; ?>&<?php echo $query_params; ?>">Siguiente</a>
    </li>
  </ul>
</nav>
<?php endif; ?>