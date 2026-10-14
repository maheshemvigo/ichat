var handlepageslbss = (function () {

	var createsimulation = function () {

		$('body').on('click', '#startsimulation-p,#savesimulation-p', function(){
			var token = $('meta[name="csrf-token"]').attr("content");
			$simulation_name=$('#Simulationion_name').val();
			$.ajax({
				type: "post",
				url: "/simulationnamecheck/",
				async: false,
				dataType: "json",
				data: {
					_token: token,
					simulation_name:$simulation_name
				},
				success: function(data) {
					checkvalidation(data.message);
				}
			});
			
		});
	
		$('body').on('click','.video-select-p',function(){
			videolistingpopup();
		});

		$('body').on('click','.doc-select-p',function(){
			documentlistingpopup();
		});

		$('body').on('click','.image-select-p',function(){
			imagelistingpopup();
		});

		$('body').on('click','.link-select-p',function(){
			linklistingpopup();
		});
		
		$(document).ready(function () {		
			var user_color_length = $("body").find(".remove-color-picker-p").length;
			$("body").on("change", "#user_number", function () {
				var terminal_count = $(this).val();
				var colors = [
					"#45B251",
					"#900C3F",
					"#5DA218",
					"#187CA2",
					"#182BA2",
					"#7028A6",
					"#A21846",
					"#895C6D",
					"#e8985a",
					"#c7e85a",
					"#5adae8",
					"#c918d6",
					"#d6188d",
					"#C0C0C0",
					"#808000",
					"#FFFF00",
					"#a28089",
					"#e1b382",
					"#F7882F",
					"32d545e",
					"#f43a09",
					"#7A9D96",
					"#a4893d"
				];
				var i;
				if (user_color_length < terminal_count) {
					var terminal_add_count = terminal_count - user_color_length;
					for (i = 0; i < terminal_add_count; i++) {
						terminal_name = parseInt(user_color_length) + 1;
						user_color_length = terminal_name;
						terminals =
							'<div class="clrBox remove-color-picker-p">' +
							"<label>T" +
							terminal_name +
							"</label>" +
							'<input class="form-control" type="color" id="head-' +
							user_color_length +
							'" data-id="T' +
							terminal_name +
							'" name="mainuser_color[]" value="' +
							colors[user_color_length] +
							'" required>' +
							'<input type="hidden" id="user-' +
							user_color_length +
							'"  name="mainuser[]" value="T' +
							terminal_name +
							'">' +
							'</div>';
						$("body").find("#terminal-p").append(terminals);
					}
				}
				if (user_color_length > terminal_count) {
					var terminal_add_count = user_color_length - terminal_count;
					terminal_name = parseInt(user_color_length) - terminal_add_count;
					user_color_length = terminal_name;
					for (i = 0; i < terminal_add_count; i++) {
						$("body").find("#terminal-p > .remove-color-picker-p:last-child").remove();
					}
				}
			});
			$("body").on("click", "#add-user-button-p", function () {
				var user_tr_length = $("body").find(".user-tr-p").length;
				console.log(user_tr_length);
				var $i = 0;
				if (user_tr_length >= 1) {
					$i = user_tr_length + 1;
				} else if (user_tr_length == 0) {
					$i = 1;
				} else {
					$i = 1;
				}
				var main_user = $("#user-cre-p").val();
				var user_table_row =
					'<tr class="user-tr-p">' +
					"<td>" +
					'<input type="text" class="form-control name-p" placeholder="Enter Name" name="Simulationion_user_name[]" required>' +
					'<small class="field-validation-name d-none error"></small>'+
					"</td>" +
					"<td>" +
					'<input type="text" class="form-control role-p" data-user="' +
					main_user +
					'" placeholder="Enter Simulation Role" name="Simulationion_role[]" required>' +
					'<small class="field-validation-rolname d-none error"></small>'+
					"</td>" +
					"<td class='imagestyle-p'>" +
					'<div class="s-new-img-p">'+
					"<span class='span-remove-org-p'>" +
					'<img class="img-fluid chack-update-p upload-id-click-p" src="/userassets/images/Group-125.png"/>' +
					"</span>" +
					'<input type="file" data-user="profile-img-tag' + $i + '" class="validation-check-p profilepic-p profile-img d-none" name="profilepic[]" required="">' +
					'<input class="profile-img-tag" type="hidden" name="profilepicname[]" value="">'+
					"<span class='span-remove-dem-p d-none'>" +
					'<img class="img-fluid upload-id-click-p" src="" id="profile-img-tag' + $i + '"style="height:100%"/>' +
					"</span>"+
					'</div>'+
					'<small class="field-validation-pic d-none error"></small>'+
					"</td>" +
					'<td class="delete-user-p">' +
					'<img src="userassets/images/dlt.svg">' +
					"</td>" +
					"</tr>";
				$("body").find("#user-listing-p").prepend(user_table_row);
			});

			$("body").on("click", ".upload-id-click-p", function (t) {
				$(this).parents(".user-tr-p").find('.profile-img').click();
				// $(this).find('.profile-img').trigger('click');           
			});

			$("body").on("click", ".delete-user-p", function () {
				$(this).parents(".user-tr-p").remove();
			});

			function readURL(input, $image_id) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();

					reader.onload = function (e) {
						$("#" + $image_id).attr("src", e.target.result);
					};
					reader.readAsDataURL(input.files[0]);
				}
			}

			$("body").on("change", ".profile-img", function () {
				$(this).parents('.user-tr-p > .imagestyle-p').find('.span-remove-org-p').addClass('d-none');
				$(this).parents('.user-tr-p > .imagestyle-p').find('.span-remove-dem-p').removeClass('d-none');
				$(this).attr('name', 'profilepic[]');
				$(this).next('.profile-img-tag').val('');
				$image_id = $(this).attr("data-user");
				readURL(this, $image_id);
			});
		});
	};
	//video
	var simulationvideolisting = function () {
		var base_url = window.location.origin;
		
		$("#target").on("keyup", function () {
			var video_id =[];
			var result_array = [];	
			var search_value = $(this).val();
			var token = $('meta[name="csrf-token"]').attr('content');

			$('#mediaboxwrp-p > div > input[type=hidden]').each(function (index, value){
				video_id += $(this).val()+',';
			});
			
			$.ajax({
				type: 'post',
				url: '/videosearch/',
				async: false,
				dataType: "json",
				data: {
					'search': search_value,
					'_token': token,
					'video_ids': video_id

				},
				success: function (data) {
					
					$(data.search_result).each(function (index, value) {
					
							result_array += '<li class="listing-elements-p">' +
							'<label>' + value.name +
							'<input class="vdo-details-hidden-p" type="checkbox">' +
							'<input class="vdo-details-p" type="hidden" data-id="' + value.id + '" data-originalname="' + value.name + '" value="' + value.path_name + '">' +
							'<span class="checkmark"></span>' +
							'</label>' +
							'</li>';

					});
					$("body").find("#searchresult-panel-p").html(result_array);
				}
			});

		});
		
		$('body').on('click', '#video-selection-p', function () {
			var ipaddress= $('#ipaddress-set-p').val();
			if((ipaddress != null) && (ipaddress != undefined) && ipaddress != 0 )
			{
				var base_link ='http://'+ipaddress;

			}else{
				var base_link =window.location.origin;
			}
			var new_vdo = '';
			$('body').find('.vdo-details-hidden-p').each(function (index, value) {
				if ($(this).is(":checked")) {
					var originalname = $(this).next('.vdo-details-p').attr("data-originalname");
					var name_val = $(this).next('.vdo-details-p').val();
					var data_id = $(this).next('.vdo-details-p').attr('data-id');
					new_vdo = '<div class="mediaBox remove-box-p">' +
						'<video class="vdo-stayle-class-p">' +
						'<source src="' +base_link+ '/uploads/videoupload/' + name_val + '">' +
						'</video>' +
						'<input type="hidden" name="docid[]" value="' + data_id + '"></input>' +
						'</div>';
					$("body").find("#mediaboxwrp-p").append(new_vdo);
				} else {

				}
			})
		});
		$('body').on('click', '.remove-box-p', function () {
			$(this).remove();
			videolistingpopup();
		});
	}
	//image
	var simulationimagelisting = function () {
		var base_url = window.location.origin;
		$("#target-image").on("keyup", function () {
			var image_id =[];
			var result_array = [];
			var search_value = $(this).val();
			var token = $('meta[name="csrf-token"]').attr('content');
			$('#mediaboxwrpimage-p > div > input[type=hidden]').each(function (index, value){
				image_id += $(this).val()+',';
			});

			$.ajax({
				type: 'post',
				url: '/imagesearch/',
				async: false,
				dataType: "json",
				data: {
					'search': search_value,
					'_token': token,
					'image_ids': image_id

				},
				success: function (data) {
					$(data.search_result).each(function (index, value) {
						result_array += '<li class="listing-elements-p">' +
							'<label>' + value.name +
							'<input class="image-details-hidden-p" type="checkbox">' +
							'<input class="image-details-p" type="hidden" data-id="' + value.id + '" data-originalname="' + value.name + '" value="' + value.path_name + '">' +
							'<span class="checkmark"></span>' +
							'</label>' +
							'</li>';
					});
					$("body").find("#img-searchresult-panel-p").html(result_array);
				}
			});

		});
		$('body').on('click', '#image-selection-p', function () {
			var ipaddress= $('#ipaddress-set-p').val();
			if((ipaddress != null) && (ipaddress != undefined) && ipaddress != 0 )
			{
				var base_link='http://'+ipaddress;

			}else{
				var base_link=window.location.origin;
			}

			var new_image = '';
			$('body').find('.image-details-hidden-p').each(function (index, value) {
				if ($(this).is(":checked")) {
					var originalname = $(this).next('.image-details-p').attr("data-originalname");
					var name_val = $(this).next('.image-details-p').val();
					var data_id = $(this).next('.image-details-p').attr('data-id');


					new_image = '<div class="mediaBox remove-box-p">'+
						'<img class="vdo-stayle-class-p" src="' +base_link+ '/uploads/imageupload/' + name_val + '" alt="'+originalname+'">'+
						'<input type="hidden" name="docid[]" value="' + data_id + '"></input>' +
						'</div>';
					$("body").find("#mediaboxwrpimage-p").append(new_image);
				} else {

				}
			})
		});
		$('body').on('click', '.remove-box-p', function () {
			$(this).remove();
			imagelistingpopup();
			
		});
	}
	//document
	var simulationdocumentlisting = function () {
		var base_url = window.location.origin;
		$("#target-doument").on("keyup", function () {
			var doc_id =[];
			var result_array_doc = [];
			var search_value = $(this).val();
			var token = $('meta[name="csrf-token"]').attr('content');
			$('#mediaboxwrpdoc-p > li > input[type=hidden]').each(function (index, value){
				doc_id += $(this).val()+',';
			});


			$.ajax({
				type: 'post',
				url: '/documentsearch/',
				async: false,
				dataType: "json",
				data: {
					'search': search_value,
					'_token': token,
					'doc_ids': doc_id
				},
				success: function (data) {
					$(data.search_result).each(function (index, value) {
						result_array_doc += '<li class="listing-elements-p">' +
							'<label>' + value.name +
							'<input class="doc-details-hidden-p" type="checkbox">' +
							'<input class="doc-details-p" type="hidden" data-id="' + value.id + '" data-originalname="' + value.name + '" value="' + value.path_name + '">' +
							'<span class="checkmark"></span>' +
							'</label>' +
							'</li>';
					});
					$("body").find("#doc-searchresult-panel-p").html(result_array_doc);
				}
			});

		});
		$('body').on('click', '#doc-selection-p', function () {
			
				var base_link=window.location.origin;
			var new_image = '';
			$('body').find('.doc-details-hidden-p').each(function (index, value) {
				if ($(this).is(":checked")) {
					var originalname = $(this).next('.doc-details-p').attr("data-originalname");
					var name_val = $(this).next('.doc-details-p').val();
					var data_id = $(this).next('.doc-details-p').attr('data-id');

					new_image = '<li class="remove-box-p">'+originalname+
					'<img class="remove-box-p" src="'+base_link+'/userassets/images/close.svg">'+
					'<input type="hidden" name="docid[]" value="'+ data_id +'">'+
					'</li>';	
					$("body").find("#mediaboxwrpdoc-p").append(new_image);
				} else {

				}
			})
		});
		$('body').on('click', '.remove-box-p', function () {
			$(this).remove();
			documentlistingpopup();
		});
	}
	//link
	var simulationlinklisting = function () {
		var base_url = window.location.origin;
		$("#target-link").on("keyup", function () {
			var link_id =[];
			var result_array_link = [];
			var search_value = $(this).val();
			var token = $('meta[name="csrf-token"]').attr('content');
			$('#mediaboxwrplink-p > li > input[type=hidden]').each(function (index, value){
				link_id += $(this).val()+',';
			});


			$.ajax({
				type: 'post',
				url: '/linksearch/',
				async: false,
				dataType: "json",
				data: {
					'search': search_value,
					'_token': token,
					'link_ids': link_id
				},
				success: function (data) {
					$(data.search_result).each(function (index, value) {
						result_array_link += '<li class="listing-elements-p">' +
							'<label>' + value.name +
							'<input class="link-details-hidden-p" type="checkbox">' +
							'<input class="link-details-p" type="hidden" data-id="' + value.id + '" data-originalname="' + value.name + '" value="' + value.path_name + '">' +
							'<span class="checkmark"></span>' +
							'</label>' +
							'</li>';
					});
					$("body").find("#link-searchresult-panel-p").html(result_array_link);
				}
			});

		});
		$('body').on('click', '#link-selection-p', function () {
			var base_link = window.location.origin;
			var new_link = '';
			$('body').find('.link-details-hidden-p').each(function (index, value) {
				if ($(this).is(":checked")) {
					var originalname = $(this).next('.link-details-p').attr("data-originalname");
					var name_val = $(this).next('.link-details-p').val();
					var data_id = $(this).next('.link-details-p').attr('data-id');

					new_link = '<li class="remove-box-p">'+originalname+
					'<img class="remove-box-p" src="'+base_link+'/userassets/images/close.svg">'+
					'<input type="hidden" name="linkid[]" value="'+ data_id +'">'+
					'</li>';	
					$("body").find("#mediaboxwrplink-p").append(new_link);
				} else {

				}
			})
		});
		$('body').on('click', '.remove-box-p', function () {
			$(this).remove();
			documentlistingpopup();
		});
	}

	var updatesimulation = function () {
		$('body').on('click', '#startsimulation-p,#savesimulation-p', function(){
			var token = $('meta[name="csrf-token"]').attr("content");
			var simulationid = $('#simulation-id-p').val();
			$simulation_name=$('#Simulationion_name').val();
			$.ajax({
				type: "post",
				url: "/updatesimulationnamecheck/",
				async: false,
				dataType: "json",
				data: {
					id:simulationid,
					_token: token,
					simulation_name:$simulation_name
				},
				success: function(data) {
						checkvalidation(data.message);
				}
			});
		});
		$('body').on('click','.video-select-p',function(){
			videolistingpopup();
		});
		$('body').on('click','.doc-select-p',function(){
			documentlistingpopup();
		});
		$('body').on('click','.image-select-p',function(){
			imagelistingpopup();
		});
		$('body').on('click','.link-select-p',function(){
			linklistingpopup();
		});
		$(document).ready(function () {
			var terminals = '';
			var user_color_length = $("body").find(".remove-color-picker-p").length;
			$('#user_number').find('option[value=' + user_color_length + ']').attr('selected', 'selected');
			$("body").on("change", "#user_number", function () {
				var terminal_count = $(this).val();
				var colors = [
					"#45B251",
					"#900C3F",
					"#5DA218",
					"#187CA2",
					"#182BA2",
					"#7018A2",
					"#A21848",
					"#895C6D"
				];
				var i;
				if (user_color_length < terminal_count) {
					var terminal_add_count = terminal_count - user_color_length;
					for (i = 0; i < terminal_add_count; i++) {
						terminal_name = parseInt(user_color_length) + 1;
						user_color_length = terminal_name;
						terminals =
							'<div class="clrBox remove-color-picker-p">' +
							"<label>T" +
							terminal_name +
							"</label>" +
							'<input class="form-control" type="color" id="head-' +
							user_color_length +
							'" data-id="T' +
							terminal_name +
							'" name="mainuser_color[]" value="' +
							colors[user_color_length] +
							'" required>' +
							'<input type="hidden" id="user-' +
							user_color_length +
							'"  name="mainuser[]" value="T' +
							terminal_name +
							'">' +
							'</div>';
						$("body").find("#terminal-p").append(terminals);
					}
				}
				if (user_color_length > terminal_count) {
					var terminal_add_count = user_color_length - terminal_count;
					terminal_name = parseInt(user_color_length) - terminal_add_count;
					user_color_length = terminal_name;
					for (i = 0; i < terminal_add_count; i++) {
						$("body").find("#terminal-p > .remove-color-picker-p:last-child").remove();
					}
				}
			});
			$("body").on("click", "#add-user-button-p", function () {
				var url = window.location.origin;
				var user_tr_length = $("body").find(".user-tr-p").length;
				var $i = 0;
				if (user_tr_length >= 1) {
					$i = user_tr_length + 1;
				} else if (user_tr_length == 0) {
					$i = 1;
				} else {
					$i = 1;
				}
				var main_user = $("#user-cre-p").val();
				var user_table_row =
					'<tr class="user-tr-p">' +
					"<td>" +
					'<input type="text" class="form-control name-p" placeholder="Enter Name" name="Simulationion_user_name[]" required>' +
					'<small class="field-validation-name d-none error"></small>'+
					"</td>" +
					"<td>" +
					'<input type="text" class="form-control role-p" data-user="' +
					main_user +
					'" placeholder="Enter Simulation Role" name="Simulationion_role[]" required>' +
					'<small class="field-validation-rolname d-none error"></small>'+
					"</td>" +
					"<td class='imagestyle-p'>" +
					"<span class='span-remove-org-p'>" +
					'<img class="img-fluid chack-update-p upload-id-click-p" src="/userassets/images/Group-125.png"/>' +
					"</span>" +
					'<input type="file" data-user="profile-img-tag' + $i + '" class="validation-check-p profilepic-p profile-img d-none" name="profilepic[]" required="">' +
					'<input class="profile-img-tag" type="hidden" name="profilepicname[]" value="">'+
					"<span class='span-remove-dem-p demo d-none'>" +
					'<img class="img-fluid upload-id-click-p" src="" id="profile-img-tag' + $i + '"style="height:100%"/>' +
					"</span>" +
					'<small class="field-validation-pic d-none error"></small>'+
					"</td>" +
					'<td class="delete-user-p">' +
					'<img src="' + url + '/userassets/images/dlt.svg">' +
					"</td>" +
					"</tr>";

				$("body").find("#user-listing-p").prepend(user_table_row);
			});

			$("body").on("click", ".upload-id-click-p", function (t) {
				$(this).parents(".user-tr-p").find('.profile-img').click();
				// $(this).find('.profile-img').trigger('click');           
			});


			$("body").on("click", ".delete-user-p", function () {
				$(this).parents(".user-tr-p").remove();
			});

			function readURL(input, $image_id) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();

					reader.onload = function (e) {
						e.preventDefault();
						$("#" + $image_id).attr("src", e.target.result);
					};
					reader.readAsDataURL(input.files[0]);
				}
			}
			$("body").on("change", ".profile-img", function (e) {
				e.preventDefault();
				$(this).attr('name', 'profilepic[]');
				$(this).next('.profile-img-tag').val('');
				// $(this).next('.profile-img-tag').remove();
				$(this).parents('.user-tr-p > .imagestyle-p').find('.span-remove-org-p').addClass('d-none');
				$(this).parents('.user-tr-p > .imagestyle-p').find('.span-remove-dem-p').removeClass('d-none');
				
	

				$image_id = $(this).attr("data-user");
				readURL(this, $image_id);
			});
		});

		function readURL(input, $image_id) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					$("#" + $image_id).attr("src", e.target.result);
				};
				reader.readAsDataURL(input.files[0]);
			}
		}

		// $("body").on("change", ".profile-img", function () {
		//     $image_id = $(this).attr("data-user");
		//     $(this).parents('.user-tr-p > .imagestyle-p').find('.chack-update-p').addClass('d-none').removeClass('upload-id-click-p');
		//     $(this).parents('.user-tr-p > .imagestyle-p').find('.remove-span-p').addClass('d-none');
		//     readURL(this, $image_id);
		// });
	};
	//validation
	function checkvalidation(data) {
		var $err=[];
			var $terminalnumber = $('#user_number').children("option:selected").val();
			var $simulationion_name = $('#Simulationion_name').val();

			
			$('.user-tr-p').each(function (index, value){
				var $name = $(this).find('.name-p').val();
				var $rol_name = $(this).find('.role-p').val();
				var $profilepic = $(this).find('.validation-check-p').val();
				if($name !='' && $name!=null){
					$err['name']= true;
					$(this).find('.field-validation-name').addClass('d-none')
				}else{
					$err['noname']= false;
					$(this).find('.field-validation-name').removeClass('d-none').text("*Please Enter Name");
				}
				if($rol_name != '' && $rol_name!=null){
					$err['rol_name']= true;
					$(this).find('.field-validation-rolname').addClass('d-none')
				}else{
					$err['norol_name']= false;
					$(this).find('.field-validation-rolname').removeClass('d-none').text("*Please Enter Role");
				}

				if((($profilepic !='' && $profilepic != null && $profilepic.match(/\.(jpg|jpeg|png)$/i))||($profilepic == undefined))){
					$err['prifilepic']= true;
					$(this).find('.field-validation-pic').addClass('d-none');
				}else{
					$err['noprifilepic']= false;
					$(this).find('.field-validation-pic').removeClass('d-none').text("*Please Add Valid Profile Picture");
				}
			});

			if(($terminalnumber > '0' || $terminalnumber > 0) && ($terminalnumber != 'AL')){
				$err['terminalnumber']= true;
				$('body').find('.field-validation-terminalcount').addClass('d-none');
			}else{

				$err['noterminalnumber']= false;
				$('body').find('.field-validation-terminalcount').removeClass('d-none').text("*Select Terminals");
			}

			if($simulationion_name !='' && $simulationion_name!=null){
				$err['simulationion_name']= true;
				$('.field-validation-simname').addClass('d-none');
				if(data === 'success'){
					$err['simulationion_name-chk']= true;
					$('body').find('.field-validation-simname-p').addClass('d-none');
				}else{
					$err['nosimulationion_name-chk']= false;
					$('body').find('.field-validation-simname-p').removeClass('d-none').text("*The simulation name has already been taken");
				}
			}else{
				$err['nosimulationion_name']= false;
				$('.field-validation-simname').removeClass('d-none').text("*Please Enter Simulation Name");
			}

			if(($err['simulationion_name-chk'] == true)&&($err['name']==true && $err['name']!=false)&&($err['rol_name']==true && $err['norol_name']!=false)&&($err['prifilepic']==true&&$err['noprifilepic']!=false)&&($err['terminalnumber']==true && $err['noterminalnumber']!=false)&&($err['simulationion_name']==true && $err['nosimulationion_name']!=false)) {
				$('#createform-p').submit();
			}else{
				return false;
			}

	}

	//pop search listing
	function videolistingpopup() {
		var base_url = window.location.origin;
			var video_id = [];
			var result_array = [];	
			var search_value = '';
			var token = $('meta[name="csrf-token"]').attr('content');

			$('#mediaboxwrp-p > div > input[type=hidden]').each(function (index, value){
				video_id += $(this).val()+',';
			});
			
			$.ajax({
				type: 'post',
				url: '/videosearch/',
				async: false,
				dataType: "json",
				data: {
					'search': search_value,
					'_token': token,
					'video_ids': video_id

				},
				success: function (data) {
					
					console.log(video_id);
					$(data.search_result).each(function (index, value) {
					
							result_array += '<li class="listing-elements-p">' +
							'<label>' + value.name +
							'<input class="vdo-details-hidden-p" type="checkbox">' +
							'<input class="vdo-details-p" type="hidden" data-id="' + value.id + '" data-originalname="' + value.name + '" value="' + value.path_name + '">' +
							'<span class="checkmark"></span>' +
							'</label>' +
							'</li>';

					});
					$("body").find("#searchresult-panel-p").html(result_array);
				}
			});
	}
	function imagelistingpopup() {

		var base_url = window.location.origin;
			var image_id =[];
			var result_array = [];
			var search_value = '';
			var token = $('meta[name="csrf-token"]').attr('content');
			$('#mediaboxwrpimage-p > div > input[type=hidden]').each(function (index, value){
				image_id += $(this).val()+',';
			});

			$.ajax({
				type: 'post',
				url: '/imagesearch/',
				async: false,
				dataType: "json",
				data: {
					'search': search_value,
					'_token': token,
					'image_ids': image_id

				},
				success: function (data) {
					$(data.search_result).each(function (index, value) {
						result_array += '<li class="listing-elements-p">' +
							'<label>' + value.name +
							'<input class="image-details-hidden-p" type="checkbox">' +
							'<input class="image-details-p" type="hidden" data-id="' + value.id + '" data-originalname="' + value.name + '" value="' + value.path_name + '">' +
							'<span class="checkmark"></span>' +
							'</label>' +
							'</li>';
					});
					$("body").find("#img-searchresult-panel-p").html(result_array);
				}
			});
	}
	function documentlistingpopup() {
			var base_url = window.location.origin;
			var doc_id =[];
			var result_array_doc = [];
			var search_value = '';
			var token = $('meta[name="csrf-token"]').attr('content');
			$('#mediaboxwrpdoc-p > li > input[type=hidden]').each(function (index, value){
				doc_id += $(this).val()+',';
			});

			$.ajax({
				type: 'post',
				url: '/documentsearch/',
				async: false,
				dataType: "json",
				data: {
					'search': search_value,
					'_token': token,
					'doc_ids': doc_id
				},
				success: function (data) {
					$(data.search_result).each(function (index, value) {
						result_array_doc += '<li class="listing-elements-p">' +
							'<label>' + value.name +
							'<input class="doc-details-hidden-p" type="checkbox">' +
							'<input class="doc-details-p" type="hidden" data-id="' + value.id + '" data-originalname="' + value.name + '" value="' + value.path_name + '">' +
							'<span class="checkmark"></span>' +
							'</label>' +
							'</li>';
					});
					$("body").find("#doc-searchresult-panel-p").html(result_array_doc);
				}
			});

	}
	function linklistingpopup() {
		var base_url = window.location.origin;
		var link_id =[];
		var result_array_link = [];
		var search_value = '';
		var token = $('meta[name="csrf-token"]').attr('content');
		$('#mediaboxwrplink-p > li > input[type=hidden]').each(function (index, value){
			link_id += $(this).val()+',';
		});

		$.ajax({
			type: 'post',
			url: '/linksearch/',
			async: false,
			dataType: "json",
			data: {
				'search': search_value,
				'_token': token,
				'link_ids': link_id
			},
			success: function (data) {
				$(data.search_result).each(function (index, value) {
					result_array_link += '<li class="listing-elements-p">' +
						'<label>' + value.name +
						'<input class="link-details-hidden-p" type="checkbox">' +
						'<input class="link-details-p" type="hidden" data-id="' + value.id + '" data-originalname="' + value.name + '" value="' + value.name + '">' +
						'<span class="checkmark"></span>' +
						'</label>' +
						'</li>';
				});
				$("body").find("#link-searchresult-panel-p").html(result_array_link);
			}
		});

	}
	return {
		initCreateSimulation: function () {
			createsimulation();
			simulationvideolisting();
			simulationimagelisting();
			simulationdocumentlisting();
			simulationlinklisting();
		},
		initUpdateSimulation: function () {
			updatesimulation();
			simulationvideolisting();
			simulationimagelisting();
			simulationdocumentlisting();
			simulationlinklisting();
		}
	};
})(jQuery);