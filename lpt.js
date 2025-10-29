jQuery(document).ready(function($) {
    $(document).on('click', '.liveprtr-fav-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var productId = btn.data('product');
        $.post(liveprtr_ajax.ajax_url, {
            action: 'liveprtr_toggle_favorite',
            nonce: liveprtr_ajax.nonce,
            product_id: productId
        }, function(response) {
            if (response.success) {
                let newText = btn.text() === 'Dodaj u omiljene' ? 'Ukloni iz omiljenih' : 'Dodaj u omiljene';
                btn.text(newText);
            } else {
                alert(response.data || 'Greška.');
            }
        });
    });
});
