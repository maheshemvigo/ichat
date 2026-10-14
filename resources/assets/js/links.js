$(document).on('click','#hide-show-class-p', function(){
   $('.mediaBoard').scrollTop("300px");
    if($('#link-section-p').hasClass('d-none')){
       $('#link-section-p').removeClass('d-none');
    }else{
       $('#link-section-p').addClass('d-none');
    }   
 });
 $(document).on('click', '.btn-delete-link', function (event) {
   event.preventDefault();
   let modal = $("#confirm_modal");
   $('.modal-body').html("Are you sure you want to delete this record?");
   modal.find(".btn-danger").html('Yes, delete');
   modal.find(".btn-danger").addClass('btn-delete-this-file');
   modal.find(".btn-danger").attr('file-id',$(this).data('id'));
   $("#confirm_modal").modal("show");
});

$(document).on('click', '.btn-delete-this-file', function () {
   let file_id = $(this).attr('file-id');
   window.location.href = "/link_delete/"+file_id;
});

$(document).on('click', '.btn-edit-link', function (event) {
   event.preventDefault();
   let id = $(this).data('id');
   $('a.btn-edit-link[data-id='+id+']').addClass('d-none');
   $('td#'+id+' span.link-name').addClass('d-none');
   $('td#'+id+' input').removeClass('d-none');
   $('a.btn-save-link[data-id='+id+']').removeClass('d-none');
});

$(document).on('click', 'a.btn-save-link', function (event) {
   event.preventDefault();
   let id = $(this).data('id');
   let token = $('meta[name="csrf-token"]').attr('content');
   let linkname = $('#link-name-'+id).val();
   if(linkname != null && id != null){
      $.ajax({
          type: 'post',
          url: '/linkupdate/',
          async: false,
          dataType: "json",
          data: {
              'linkname': linkname,
              'id': id,
              '_token': token,
          },success: function (data) {
            if(data.status == 1) {
               $('td#'+id+' span.link-name').html(linkname)
               $('a.btn-edit-link[data-id='+id+']').removeClass('d-none');
               $('td#'+id+' span.link-name').removeClass('d-none');
               $('td#'+id+' input').addClass('d-none');
               $('a.btn-save-link[data-id='+id+']').addClass('d-none');
               $('#link-success').removeClass('d-none');
               setTimeout(function() {
                  $('#link-success').addClass('d-none');
              }, 2000);
            }
          }
      });
  }
});

$(document).on('ready', function() {
   setTimeout(function() {
      $(".alert").fadeOut();
   }, 3000); 
});