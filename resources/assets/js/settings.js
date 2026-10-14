$(document).on('click','.closebtn', function(){
    $(this).parents('#pusher-p').addClass('d-none');
    $(this).parents('#generate-zip-p').addClass('d-none');
    $(this).parents('#ip-config-p').addClass('d-none');
    $(this).parents('#resetip-config-p').addClass('d-none');
 });
    $("body").on("click", "#genarate-zip-p", function() {
       $('#generate-loder-p').removeClass('d-none');
        $(this).attr("disabled", "disabled");
        var token = $('meta[name="csrf-token"]').attr("content");
        setTimeout(function() {
            $.ajax({
                type: "post",
                url: "/handlezip/",
                async: false,
                dataType: "json",
                data: {
                    _token: token
                },
                success: function(data) {
                    if (data.message == "success") {
                      $('#generate-loder-p').addClass('d-none');
                        $("#genarate-zip-p").removeAttr("disabled");
                        $("#download-zip-p").removeClass("d-none");
                        $("#generate-zip-p").removeClass('d-none');
                         setTimeout(function(){ 
                         $("#generate-zip-p").addClass('d-none');
                         }, 3000);
                    }
                }
            });
        }, 500);
    });
    $("body").on("click", "#find-pusher-con-p", function() {
       
        var token = $('meta[name="csrf-token"]').attr("content");
        var appidnew = $("#appidnew-p").val();
        var keynew = $("#keynew-p").val();
        var secretnew = $("#secretnew-p").val();
        var clusternew = $("#cluster-new-p").val();
        var error=[];
            if(appidnew !='' && appidnew!=null){
               error['appidnew-p']= true;
               $('#field-validation-appidnew').addClass('d-none');
            }else{
               error['appidnew-p']= false;
               $('#field-validation-appidnew').removeClass('d-none').text("*Please Enter App id");
            }
            if(keynew !='' && keynew!=null){
               error['keynew-p']= true;
               $('#field-validation-keynew').addClass('d-none')
            }else{
               error['keynew-p']= false;
               $('#field-validation-keynew').removeClass('d-none').text("*Please Enter Key");
            }
            if(secretnew !='' && secretnew!=null){
               error['secretnew-p']= true;
               $('#field-validation-secretnew').addClass('d-none')
            }else{
               error['secretnew-p']= false;
               $('#field-validation-secretnew').removeClass('d-none').text("*Please Enter Secret");
            }
            if(clusternew !='' && clusternew!=null){
               error['cluster-new-p']= true;
               $('#field-validation-clusternew').addClass('d-none')
            }else{
               error['cluster-new-p']= false;
               $('#field-validation-clusternew').removeClass('d-none').text("*Please Enter Cluster");
            }
            if((error['appidnew-p']==true) && (error['keynew-p']==true) && (error['secretnew-p']==true) && (error['cluster-new-p']==true)){
                $.ajax({
                   type: "post",
                   url: "/pushercre/",
                   async: false,
                   dataType: "json",
                   data: {
                         app_id: appidnew,
                         app_key: keynew,
                         secret: secretnew,
                         cluster: clusternew,
                         _token: token
                   },
                   success: function(data) {
                         $("#appidnew-p").val(data.app_id);
                         $("#keynew-p").val(data.app_key);
                         $("#secretnew-p").val(data.secret);
                         $("#cluster-new-p").val(data.cluster);
                         $("#pusher-p").removeClass('d-none');
                         setTimeout(function(){ 
                         $("#pusher-p").addClass('d-none');
                         }, 3000);
                   }
                });  
          }else{
             return false;
          }
 
    });
    $("body").on("click", "#find-ip-address-p", function() {
        var token = $('meta[name="csrf-token"]').attr("content");
        var ipaddress = $("#ipaddress-new-p").val();
        var error=[];
            if(ipaddress !='' && ipaddress!=null){
               error['ipaddress']= true;
               $('#field-validation-ipaddress').addClass('d-none');
            }else{
               error['appidnew-p']= false;
               $('#field-validation-ipaddress').removeClass('d-none').text("*Please Enter App id");
            }
       if(error['ipaddress']==true){
   
        $.ajax({
            type: "post",
            url: "/saveipaddress/",
            async: false,
            dataType: "json",
            data: {
                ipaddress: ipaddress,
                _token: token
            },
            success: function(data) {
                $("#ipaddress-new-p").val(data.ipaddress);
                $("#ip-config-p").removeClass('d-none');
                         setTimeout(function(){ 
                         $("#ip-config-p").addClass('d-none');
                         }, 3000);
            }
        });
        
       }else{
          return false;
       }
    });
    $(document).on('click','#reset-ip-address-p', function(){
       var token = $('meta[name="csrf-token"]').attr("content");
       $.ajax({
            type: "post",
            url: "/resetipaddress/",
            async: false,
            dataType: "json",
            data: {
                _token: token
            },
            success: function(data) {
                console.log(data.ipaddress);
                $("#ipaddress-new-p").val('');
                $("#resetip-config-p").removeClass('d-none');
                         setTimeout(function(){ 
                         $("#resetip-config-p").addClass('d-none');
                         }, 3000);
            }
        });
 
    });