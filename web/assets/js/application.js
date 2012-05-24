$(function () {
    $("button[rel=tooltip]").tooltip();

    $('.add_spending_user').on('click', function() {
        console.log($(this).attr('data-related-form'));
        $('#' + $(this).attr('data-related-form')).show();
    });
});