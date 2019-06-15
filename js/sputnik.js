jQuery(document).ready(function () {
    jQuery('.btn-delete').click(function () {
        if (!confirm('¿Confirma la eliminación de este elemento?')) {
            return false;
        }
    })
})
