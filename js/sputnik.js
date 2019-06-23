jQuery(document).ready(function () {
    jQuery('.btn-delete').click(function () {
        if (!confirm('Are you sure?')) {
            return false;
        }
    })
})
