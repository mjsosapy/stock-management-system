<?php $title = 'Historial de Salidas'; include 'views/templates/header.php'; ?>
<h2 class="mb-4">📤 Historial de Salidas</h2>

<?php if ($history && $history->num_rows > 0): ?>
<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr><th>Artículo</th><th>Cantidad</th><th>Entregado a</th><th>Departamento</th><th>Fecha</th><th>Acciones</th></tr>
  </thead>
  <tbody>
    <?php while ($row = $history->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model'] . ' ' . $row['color']); ?></td>
        <td><?php echo abs($row['quantity_change']); ?></td>
        <td><?php echo htmlspecialchars($row['recipient_name'] ?? 'No especificado'); ?></td>
        <td><?php echo htmlspecialchars($row['department_name'] ?? 'No especificado'); ?></td>
        <td><?php echo date('d/m/Y H:i', strtotime($row['movement_date'])); ?></td>
        <td>
          <?php if ($row['is_reversed'] == 0): ?>
            <a href="<?php echo BASE_URL; ?>history/reverse" 
               onclick="event.preventDefault(); if(confirm('¿Anular esta salida? El stock será restaurado.')) { var form = document.createElement('form'); form.method = 'POST'; form.action = '<?php echo BASE_URL; ?>history/reverse'; var input1 = document.createElement('input'); input1.type = 'hidden'; input1.name = 'movement_id'; input1.value = '<?php echo $row['id']; ?>'; var input2 = document.createElement('input'); input2.type = 'hidden'; input2.name = 'reason'; input2.value = 'Reversión manual desde historial de salidas'; var token = document.createElement('input'); token.type = 'hidden'; token.name = 'csrf_token'; token.value = '<?php echo $_SESSION['csrf_token'] ?? ''; ?>'; form.appendChild(input1); form.appendChild(input2); form.appendChild(token); document.body.appendChild(form); form.submit(); }" 
               class="btn btn-sm btn-warning">Anular</a>
          <?php else: ?>
            <span class="badge bg-secondary">Anulada</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php else: ?>
<div class="alert alert-info">
  <h5>📝 No hay salidas registradas</h5>
  <p>No se encontraron salidas de stock en el sistema.</p>
  <a href="<?php echo BASE_URL; ?>stock/issue" class="btn btn-primary">Registrar Primera Salida</a>
</div>
<?php endif; ?>

<div class="mt-4">
  <a href="<?php echo BASE_URL; ?>stock/dashboard" class="btn btn-secondary">Volver al Dashboard</a>
  <a href="<?php echo BASE_URL; ?>stock/issue" class="btn btn-primary">Registrar Nueva Salida</a>
  <a href="<?php echo BASE_URL; ?>history/movements" class="btn btn-info">Ver Historial Completo</a>
</div>

<?php include 'views/templates/footer.php'; ?>