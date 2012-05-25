$(function () {
    $("[rel=tooltip]").tooltip();

    $('.add_spending_user').on('click', function() {
        $('#' + $(this).attr('data-related-form')).show();
    });
    $('.add_advance_user').on('click', function() {
        $('#' + $(this).attr('data-related-form')).show();
    });

    $('a.delete_spending').confirmDelete('Etes-vous sûr de vouloir supprimer cette dépense ?');
    $('a.delete_advance').confirmDelete('Etes-vous sûr de vouloir supprimer cette avance ?');
});