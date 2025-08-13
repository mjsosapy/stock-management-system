<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Script para manejar las notificaciones "toast"
document.addEventListener('DOMContentLoaded', function() {
    <?php
    // Revisa si hay un mensaje flash en la sesión de PHP
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        // Escapa los datos para usarlos de forma segura en JavaScript
        $text = json_encode($message['text']);
        $type = json_encode($message['type']);
        
        // Imprime la llamada a la función de JS para mostrar la notificación
        echo "showToast({$text}, {$type});";

        // Limpia el mensaje de la sesión para que no se muestre de nuevo
        unset($_SESSION['message']);
    }
    ?>

    // Función que crea y muestra la notificación
    function showToast(text, type) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        const toastId = 'toast-' + Math.random().toString(36).substr(2, 9);
        toast.id = toastId;
        
        const icon = type === 'success' ? '✔️' : '❌';
        const title = type === 'success' ? 'Éxito' : 'Error';
        
        toast.className = `custom-toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-header">
                <span>${icon} ${title}</span>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('${toastId}').remove()" aria-label="Close"></button>
            </div>
            <div class="toast-body mt-2">
                ${text}
            </div>
        `;
        
        container.appendChild(toast);

        // Forzar un reflow para que la animación de entrada se ejecute
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Ocultar y eliminar la notificación después de 5 segundos
        setTimeout(() => {
            toast.classList.remove('show');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 5000);
    }
});
</script>

  </div>
</body>
</html>
