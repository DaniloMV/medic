(function($) {

    // List account links 
    $('.list_links').on('click',function(){
        if($('.acount_links').hasClass('active')){
            $('.acount_links').removeClass('active');
            $('.acount_links').hide(100);
        }else {
          $('.acount_links').addClass('active');
          $('.acount_links').show(200);
        }
    });
    $(document).click(function(event) {
        if ($(event.target).closest(".list_links, .acount_links").length) return;
        $(".acount_links").hide("slow");
        $('.acount_links').removeClass('active');
        event.stopPropagation();
    });
    

    $(".signIn").on('click', function (){
        $(this).children('a').toggleClass('open');
        var $content = $(this).next(".signInForm");
        $content.stop().slideToggle('slow');
    });
	
	$(window).load(function(){ 
		var $news = $('.newsBlock');
		$news.imagesLoaded(function(){
		  $news.masonry({
			itemSelector: '.newsBlock_item',
			singleMode: true,
			isResizable: true,
			isAnimated: true,
			animationOptions: {
				queue: false,
				duration: 500
			}
		  });
		});
	 });
	


    //support
    var question = $('.questions p');
    if (question.length) {
        question.on('click', function () {
            var parent = $(this).closest('.questions'),
                div = parent.find('.answer');

            if (!parent.hasClass('active')) {
                parent.addClass('active')
                div.slideDown(300);
            } else {
                parent.removeClass('active');
                div.slideUp(300);
            }

            return false;
        })
    };

    $(".more-button").on('click', function (e) {
        e.preventDefault()
        var button = $(this),
            div = $('.more');

        if (!button.hasClass('active')) {
            button.addClass('active')
            div.slideDown(300);
        } else {
            button.removeClass('active');
            div.slideUp(300);
        }

        $('.newsBlock').masonry({
            itemSelector: '.newsBlock_item',
            singleMode: true,
            isResizable: true,
            isAnimated: true,
            animationOptions: {
                queue: false,
                duration: 500
            }
        });
        return false;
    })


    $(".showAll").on('click', function (){
        var $content = $(this).closest('.form').nextAll(".hidden");
        $content.stop().fadeIn('slow');
    });

    $(".showFormTransparent").on('click', function (){
        var $content = $(this).parent('.label').next('.transparent');
        $content.stop().toggleClass('visible');
    });



    var openSelectField = $('.openSelectField');
    var addFotoForm = $('.addFotoForm');
    var closeSelectField = $('.closeSelectField');

    if (openSelectField.attr("checked","checked")) {
        var $content = openSelectField.closest('.js_half').next(".hidden");
        $content.fadeIn('slow');
    }else if(addFotoForm.attr("checked","checked")){
        var $content = addFotoForm.closest('.js_wrap_half').next(".hidden");
        $content.fadeIn('slow');
    }else{
        var $content = closeSelectField.closest('.js_half').next(".hidden");
        $content.fadeOut('slow');
    }

    $(".openSelectField").on('click', function (){
        if (openSelectField.attr("checked","checked")) {
            var $content = $(this).closest('.js_half').next(".hidden");
            var $contentHide = addFotoForm.closest('.js_wrap_half').next(".hidden");
            $content.fadeIn('slow');
            $contentHide.fadeOut();
        }
    });
    $(".addFotoForm").on('click', function (){
        if(addFotoForm.attr("checked","checked")){
            var $content = $(this).closest('.js_wrap_half').next(".hidden");
            var $contentHide = openSelectField.closest('.js_half').next(".hidden");
            $content.fadeIn('slow');
            $contentHide.fadeOut();
        }
    });
    $(".closeSelectField").on('click', function (){
        if (closeSelectField.attr("checked","checked")) {
            var $contentHide = $(this).closest('.js_half').next(".hidden");
            $contentHide.fadeOut();
        }
    });




    $(".addForm").on('click', function (e){
        e.preventDefault()
        var $content = $(this).next(".hidden");
        $content.stop().slideToggle('slow');
    });

    $(".showForm").on('click', function (){
        var $content = $(this).parent().next('.hidden');
        $content.stop().slideToggle('slow');
    });

    $(".js_period").on('click', function (){
        var $content = $(this).parent().next().children('.transparent');
        $content.stop().toggleClass('visible');
    });


    $(".form_field_add .add").on('click', function (e){
        e.preventDefault()
        var $content = $(this).next('.transparent');
        var $title = $(this).parent('.form_field_add').prev('.form_field').children('.transparent');
        $title.stop().toggleClass('visible');
        $content.stop().toggleClass('visible');
    });


    $(".add_comment").on('click', function (e){
        e.preventDefault()
        var $content = $(this).prev('.form_field').children("textarea").val();
        $(this).prev('.form_field').children("textarea").val('');
        if ($content.length > 0) {
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            var output = ((''+day).length<2 ? '0' : '') + day + '/' +
                ((''+month).length<2 ? '0' : '') + month + '/' + d.getFullYear();

            var divDate = $('<div class="comment_date"></div>').text(output);
            var divText = $('<div class="comment_text"></div>').text($content);
            var comment = $('<div class="comment"></div>').append(divDate,divText);
            $(this).parent('form').find('.js_comments').append(comment);
        }
    });

    $(".managerCalendarForm .reset").on('click', function (e){
        e.preventDefault()
        var myForm = $(this).closest('.managerCalendarForm');
        $(myForm).find('.niceSelect').select2("val", "");
        $(':checkbox',myForm).removeAttr('checked');
        $(myForm).find('.search').find('input').val('');
        $(myForm).find('.calendar').find('input').val('').datetimepicker({mask:true});
    });

    $(".js_reset_btn .reset").on('click', function (e){
        e.preventDefault()
        var myForm = $(this).closest('.js_reset_btn').siblings('.js_reset_content');
        $(myForm).find('.niceSelect').select2("val", "");
        $(':checkbox',myForm).removeAttr('checked');
        $(myForm).find('.search').find('input').val('');
        $(myForm).find('.calendar').find('input').val('').datetimepicker({mask:true});
    });

    $("#ajaxform").submit(function(){
        var form = $(this);
        var data = form.serialize();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            data: data,
            success: function(data){
                $(".popup_form").hide();
                $(".popup_form_result").show();
            },
        });
        return false;
    });


    var SITE = SITE || {};
    SITE.fileInputs = function() {
        var $this = $(this),
            $val = $this.val(),
            valArray = $val.split('\\'),
            newVal = valArray[valArray.length-1],
            $button = $this.siblings('.button'),
            $fakeFile = $this.siblings('.file-holder');
        if(newVal !== '') {
            $button.text('');
            $button.removeClass('button').removeClass('active').addClass('edit');
            if($fakeFile.length === 0) {
                $button.before('<span class="file-holder">' + newVal + '</span>');
            } else {
                $fakeFile.text(newVal);
            }
        }
    };

    $('.file-wrapper input[type=file]').bind('change focus click', SITE.fileInputs);
    $('.file-wrapper input[type=file]').hover(function() {
        $(this).next(".button").addClass('active');
    },function(){
        $(this).next(".button").removeClass('active');
    });


    $('.productDetail_images_small').find('a').on('click', function(e){
        e.preventDefault()
        $('.productDetail_images_small').find('.active').removeClass('active');
        $(this).addClass('active');
        var href =  $(this).attr('href');
        $('.productDetail_images_main').children('img').fadeOut(function() {
            $(this).attr('src',href).fadeIn();
        });
    });


    $(".leftMenu > li > a").on('click', function (){
        if($(this).parent('li').attr('class') != 'active'){

            $('.leftMenu ul').each(function(){
                if($(this) && $(this).hasClass("not_hide")){

                }else{
                    $(this).slideUp('slow');
                }
            });

            $('.leftMenu li').removeClass('active');
        }
        $(this).next().slideToggle('slow');
        $(this).parent('li').toggleClass('active');
    });


    $('.cart .js_quantity').each(function() {
        var asd  = $(this);
        var form = $('#cart-update');

        asd.find('a.minus').on('click', function () {
            var data = asd.find('input').val();
            if(data > 0) {
                asd.find('input').val(parseInt(data) - 1);
            }
            form.append('<input type="hidden" name="update_cart_action" value="update_qty"/>');
            form.submit();
            return false
        });
        asd.find('a.plus').on('click', function () {
            var data = asd.find('input').val();
            asd.find('input').val(parseInt(data) + 1);
            form.append('<input type="hidden" name="update_cart_action" value="update_qty"/>');
            form.submit();
            return false
        });
    });

    $('.cart .js_quantity input[type=text]').on('keydown', function(e) {
        var form = $('#cart-update');
        var code = e.which;

        if(code == 13) {
            form.append('<input type="hidden" name="update_cart_action" value="update_qty"/>');
            form.submit();
            return;
        }
    });

    $('.cart .del-all').on('click', function(e) {
        e.preventDefault();

        var sellerId = $(this).attr('data-id');
        var form     = $('#cart-update');

        form.append('<input type="hidden" name="update_cart_action" value="empty_seller"/>');
        form.append('<input type="hidden" name="seller_id" value="'+sellerId+'"/>');
        form.submit();
    });


    $(document).ready(function(){
        $(".niceSelectC").each(function(){
            $(this).select2("destroy");
        });
        $(".niceSelectC").select2({
            minimumResultsForSearch: 5
        });
    });
    $('.fancybox').click(function(){
        $('#forgotpass_form_email').val('');
    })

    //Add drop class for top menu
    $('.submenu').prev('a').addClass('drop');


})($j);
