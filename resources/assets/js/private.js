$(document).on("click", ".chatUser", function (e) {
	e.preventDefault();
	$(".chatUser").removeClass("selectedcolor");
	$(this).addClass("selectedcolor");
	$('.welcomePage2').addClass('d-none');
	$('.botarea').removeClass('d-none');
});

$(document).on('click', '.redirect-back-p', function(){
	window.location.href = "/login";
});

$(document).on('click', '.check-box-clicked-p', function () {
	var checkbox = $(this).find('input[type=checkbox]');
	if (checkbox.prop("checked")) {
		checkbox.prop("checked", false);
	} else {
		checkbox.prop("checked", true);
	}
});

$(document).on('click', '#doc-toggle', function () {
    toggleContent('documents');
});

$(document).on('click', '#image-toggle', function () {
    toggleContent('images');
});

$(document).on('click', '#link-toggle', function () {
    toggleContent('links');
});

function toggleContent(contentType) {
    $('#list-unstyled-P').removeClass('open');
    $('#mediaMenuCont-p').addClass('open');
    $('.videoBox').css('display', contentType === 'video' ? 'block' : 'none');
    $('.documentsBox').css('display', contentType === 'documents' ? 'block' : 'none');
    $('.imagesBox').css('display', contentType === 'images' ? 'block' : 'none');
    $('.linksBox').css('display', contentType === 'links' ? 'block' : 'none');
}

$(document).on('click', '.diasbleplace-p', function(event){
	event.preventDefault();
	$('#list-unstyled-P').addClass('open');
	$('#mediaMenuCont-p').removeClass('open');
	$(document).find('.mediaMenu').addClass('media-panel-p')
});	

$(document).on('click','#new-remove-p', function(e){
	e.preventDefault();
	$('#append-fev-p > .newmediabox-p').remove();
	$('#typingfiled-p').removeClass('active');
	$('#privateMessageBox').removeClass('hight__c');
	$('#privateMessageBox').removeClass('hight__cDoc');
});

$(document).on('click','#new-remove-link', function(e){
	e.preventDefault();
	$('#append-fev-p > .newmediabox-p').remove();
	$('li.ip_li textarea').val("");
	$('#typingfiled-p').removeClass('active');
	$('#privateMessageBox').removeClass('hight__c');
	$('#privateMessageBox').removeClass('hight__cDoc');
});

$(document).on('click','.closebtn', function(){
	$(document).find('.regenrate-pin-p').addClass('d-none');	
	$(document).find('.endsimulation-p').addClass('d-none');
});