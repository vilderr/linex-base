$(function(){
    $('body').on('click', '[data-action="delete"]', function() {
        $('#delete-confirmation').attr('data-url', $(this).attr('href')).attr('data-items', '').modal('show');
        return false;
    });
    $('#delete-confirmation [data-action="confirm"]').click(function() {
        var $modal = $(this).parents('.modal').eq(0);
        var data =  typeof($modal.attr('data-items')) == "string" && $modal.attr('data-items').length > 0
            ? {'items': $modal.attr('data-items').split(',')}
            :{};
        $.ajax({
            'url': $modal.attr('data-url'),
            'type': 'post',
            'data': data,
            'success': function (data) {
                location.reload();
            }
        });
        return true;
    });
});
