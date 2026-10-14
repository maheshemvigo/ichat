$(document).on('change','.check-all-m',function() {
    if(this.checked) {
      selectAll(this.value)
    }else{
        UnSelectAll(this.value);
    }
});

function selectAll(data) {
    var items = document.getElementsByClassName(data+'Child');
    for (var i = 0; i < items.length; i++) {
        if (items[i].type == 'checkbox')
            items[i].checked = true;
    }
}

function UnSelectAll(data) {
    var items = document.getElementsByClassName(data+'Child');
    for (var i = 0; i < items.length; i++) {
        if (items[i].type == 'checkbox')
            items[i].checked = false;
    }
}	
function form_submission($value) {
    var value =$value;
   //Assign value to text field for identify its for download
    document.getElementById("indication_key").value =value;

    let checked_value=false;
    $('.child-check-box-m').each(function (index, value) {
       
        if($(this).is(':checked'))
        {
            checked_value=true;
            
        }
        
    });
    if(checked_value==true)
        {
            document.getElementById("form_download").submit();// Form submission
        }
        else{
            alert('Please select atleast one simulation');
        }     
}
function delete_validation()
{
let checked_value=false;
    $('.child-check-box-m').each(function (index, value) {

       
        if($(this).is(':checked'))
        {
            checked_value=true;
            
        }
        
    });
    if(checked_value==true)
        {
            $('#exampleModalleaveusers').modal('toggle');
            $('#exampleModalleaveusers').modal('show');
        }
        else{
            alert('Please select atleast one simulation');
        }     

}
function form_submission_delete(){


}