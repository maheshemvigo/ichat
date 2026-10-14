var maxFilesLimit = 5; // Set the maximum files limit
Dropzone.options.dropzoneForm =
    {
        paramName: "profile",
        timeout:999999999,
        maxFilesize: 300,
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFiles: maxFilesLimit,
        parallelUploads: 5,
        acceptedFiles: ".mp4,.mkv",
        addRemoveLinks: true,
        autoProcessQueue: false,
        renameFile: function(file) {
            var dt = new Date();
            var time = dt.getTime();
            return time+file.name;
        },
       
        // timeout: 5000,
        init:function(){
            // var submitButton = document.querySelector('#submit-all');
            // myDropzone = this;
            // submitButton.addEventListener("click",function(e){
            //     e.preventDefault();
            //     myDropzone.processQueue();
            //     $("#upload-section-p").attr("disabled", "disabled").off('click');
            //     $(document).find(".dz-hidden-input").prop("disabled",true);
            //     myDropzone.options.clickable = false; 
                

            //     var count = 0;
            //     $('.dz-file-preview').each(function (index, value){
            //         count++;
            //         if(count > 5){
            //         // setTimeout(function(){
            //         $(this).remove();
            //         // }, 1000);
            //         }
            //     });
            // });
            var uploadButton = document.getElementById("submit-all");
            var cancelUploadButton = document.getElementById("cancelUploadButton");
            var self = this;

              this.on("success", function (file, response) {
                console.log("File uploaded successfully!", response);
                checkUploadStatus();
              });
    
              this.on("error", function (file, errorMessage) {
                console.error("Error uploading file:", errorMessage);
                checkUploadStatus();
              });

              this.on("maxfilesexceeded", function (file) {
                event.preventDefault();
                var modalFooter = '<div class="modal-body">   <h4>Maximum files limit reached. You can only upload'+maxFilesLimit+'</h4>' +
                '  <div class="si__btnGrp"</div>\n' +
                '            </div>';
                var modal = $("#confirm_modal");
                modal.find(".modal-title").remove();
                modal.find(".modal-footer").remove();
                modal.find(".modal-header").remove();
                modal.find(".modal-body").html("").html(modalFooter);
                $("#confirm_modal").modal("show");
                this.removeFile(file); // Remove the exceeded file from the queue
                // return null;
              });

              function checkUploadStatus() {
                if (self.getQueuedFiles().length === 0 && self.getUploadingFiles().length === 0) {
                  uploadButton.style.display = "block";
                  cancelUploadButton.style.display = "none";
                } else {
                  uploadButton.style.display = "none";
                  cancelUploadButton.style.display = "block";
                }
              }
    
              // External button click event handler
              uploadButton.addEventListener("click", function () {
                $('.dz-remove').remove();
                self.processQueue(); // Trigger file upload
                uploadButton.style.display = "none";
                cancelUploadButton.style.display = "block";
              });
    
              // Cancel upload button click event handler
              cancelUploadButton.addEventListener("click", function () {
                if (self.getQueuedFiles().length > 0 || self.getUploadingFiles().length > 0) {
                  event.preventDefault();
                  var modal = $("#confirm_modal");
                  $('.modal-body').html("Are you sure you want to cancel the upload?");
                  modal.find(".btn-danger").html('Yes, cancel');
                  modal.find(".btn-danger").addClass('btn-delete-this-file');
                  modal.find(".btn-danger").attr('file-id',$(this).data('id'));
                  $("#confirm_modal").modal("show");
                }
              });

              $(document).on('click', '.btn-delete-this-file', function () {
                self.removeAllFiles(true);
              });

            this.on("complete", function(){
                self.options.autoProcessQueue = true; 
                // Cache.delete();
                if(this.getQueuedFiles().length==0){
                    var _this = this;    
                    _this.removeAllFiles();
                    self.options.autoProcessQueue = false; 
                }
                location.reload();
            });                   
        },
    };

   $(document).on('dblclick','.get-click', function(){
      $(this).find(".savename-p").addClass('name-send-p').removeAttr("disabled").focus(); 
   });

   $(document).on('blur', '.name-send-p', function(){
        var token = $('meta[name="csrf-token"]').attr('content');
        if($(document).find('.name-send-p')){
            var filename = $('.name-send-p').val();
            var item_id = $('.name-send-p').data("cr"); 
            if(filename != null && item_id != null){
                $.ajax({
                    type: 'post',
                    url: '/nameupdate/',
                    async: false,
                    dataType: "json",
                    data: {
                        'filename': filename,
                        'itemid': item_id,
                        '_token': token,
                    },success: function (data) {
                        $(document).find('.name-send-p').val(data.value);
                        $(document).find('.name-send-p').attr("disabled", 'disabled'); 
                        $(document).find('.savename-p').removeClass('name-send-p');
                        console.log("New Task"+data.value);
                    }
                });
            }
        }
   });

   $(document).on('click','#hide-show-class-p', function(){
    $('.mediaBoard').scrollTop("300px");
      if($('#upload-section-p').hasClass('d-none')){
         $('#upload-section-p').removeClass('d-none');
      }else{
         $('#upload-section-p').addClass('d-none');
      }     
   });
   