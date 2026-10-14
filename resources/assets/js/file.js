$(document).on('click', '.btn-delete-file', function (event) {
    event.preventDefault();
    var modal = $("#confirm_modal");
    $('.modal-body').html("Are you sure you want to delete this record?");
    modal.find(".btn-danger").html('Yes, delete');
    modal.find(".btn-danger").addClass('btn-delete-this-file');
    modal.find(".btn-danger").attr('file-id',$(this).data('id'));
    $("#confirm_modal").modal("show");
});

$(document).on('click', '.btn-delete-this-file', function () {
    var file_id = $(this).attr('file-id');
    window.location.href = "/file_delete/"+file_id;
});

$(document).on('ready', function() {
    setTimeout(function() {
       $(".alert").fadeOut();
    }, 3000); 
 });