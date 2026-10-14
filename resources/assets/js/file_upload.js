var i=1;
Dropzone.options.dropzoneForm =
    {
      paramName: "profile",
      timeout:999999999,
      maxFilesize: 300,
      autoProcessQueue: false,
      uploadMultiple: true,
      maxFiles: 10,
      parallelUploads: 10,
      renameFile: function(file) {
        var dt = new Date();
        var time = dt.getTime();
        return time+'-'+file.name;
    },
      previewTemplate: `
      <div class="dz-preview dz-file-preview">
       <div class="dz-image"><img data-dz-thumbnail /></div>
       <div class="dz-details">
         <div class="dz-size"><span data-dz-size></span></div>
       </div>
       <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
       <div class="dz-error-message"><span data-dz-errormessage></span></div>
       <input type="text" name="filename" class="upload-filename"  placeholder="filename" id="filename">
      </div>'
      `,
        acceptedFiles: ".pdf,.doc,.docx",
        addRemoveLinks: true,
        autoProcessQueue: false,
      // timeout: 5000,
      init:function(){
         var uploadButton = document.getElementById("submit-all");
         var cancelUploadButton = document.getElementById("cancelUploadButton");
         var self = this;

         //  submitButton.addEventListener("click",function(){
         //      myDropzone.processQueue();
         //      $(document).find(".dz-hidden-input").prop("disabled",true);
         //      myDropzone.options.clickable = false; 
         //  });

          this.on("sending", function(file, xhr, formData) {
            // file name  added using  input field
            let inputs = file.previewElement.querySelectorAll('input')
            let str = inputs[0].value;
            formData.append("filename"+i,  inputs[0].value);
            i++;
          });
         
         this.on("success", function (file, response) {
            console.log("File uploaded successfully!", response);
            checkUploadStatus();
          });

          this.on("error", function (file, errorMessage) {
            console.error("Error uploading file:", errorMessage);
            checkUploadStatus();
          });

          this.on("maxfilesexceeded", function (file) {
            alert("Maximum files limit reached. You can only upload " + maxFilesLimit + " files.");
            this.removeFile(file); // Remove the exceeded file from the queue
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