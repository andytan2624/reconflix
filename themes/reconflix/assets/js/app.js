(function($) {
    $('#MoviesFilter').on('change', 'input', 'select', function() {
        var $form = $(this).closest('form');
        $form.request();
    });
    $('.moviedropdown').change( function() {
        var $form = $(this).closest('form');
        $form.request();
    });
    $('body').on('change', '.pagedropdown', function() {
        var $form = $(this).closest('form');
        $form.request();
    });
})(jQuery);