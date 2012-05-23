$(function () {
    $("button[rel=tooltip]").tooltip();

    $('#add_spending_user1').on('click', function() {
        $('.new_spending').show();
    });
});