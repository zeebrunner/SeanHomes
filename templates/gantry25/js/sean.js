// JavaScript Document
jQuery(function() {
	

	// Save Initial Values
	var duration = 1000;
	// Logo
	var initialLogoWidth = 300;
	var logoProportions = 0.4233;
	
	
	

	var adjustlayout = function() {
		
		// New Logo Size
		jQuery('#rt-logo').css('height',jQuery('#rt-logo').width()*logoProportions+'px');
		

		if(jQuery(window).width() >= 768) {
			
			jQuery("body").css("height",Math.max(jQuery(document).height(), jQuery(window).height())+"px");
			
			
			var scaleRatio = jQuery('#rt-logo').width()/initialLogoWidth;
			
			// Titles
			//jQuery('.title, .component-content h2').css('fontSize',(jQuery('.rt-block.logo-block').height()*1.35)+'px');
			//jQuery('.title, .component-content h2').css('lineHeight',(jQuery('.rt-block.logo-block').height()*1.35)+'px');
			//jQuery('.title, .component-content h2').css('letterSpacing',(-jQuery('.rt-block.logo-block').height()*0.035)+'px');
			//jQuery('.component-content .itemListCategoriesBlock.learn h2, .component-content .itemListCategoriesBlock.shop h2').css('fontSize',(jQuery('.rt-block.logo-block').height()*0.65)+'px');
			//jQuery('.component-content .itemListCategoriesBlock.learn h2, .component-content .itemListCategoriesBlock.shop h2').css('lineHeight',(jQuery('.rt-block.logo-block').height()*1.35)+'px');
			//jQuery('.component-content .itemListCategoriesBlock.learn h2, .component-content .itemListCategoriesBlock.shop h2').css('letterSpacing',(-jQuery('.rt-block.logo-block').height()*0.025)+'px');

			// Headers
			//jQuery('h3').css('fontSize',(jQuery('.rt-block.logo-block').height()*0.25)+'px');
			//jQuery('h3').css('lineHeight',(jQuery('.rt-block.logo-block').height()*0.35)+'px');
			// Classes
				// Lead
				//jQuery('.lead').css('fontSize',(jQuery('.rt-block.logo-block').height()*0.2)+'px');
				//jQuery('.lead').css('lineHeight',(jQuery('.rt-block.logo-block').height()*0.25)+'px');
				// blockQuote
				jQuery('.blockQuote').css('fontSize',(jQuery('.rt-block.logo-block').height()*0.25)+'px');
				jQuery('.blockQuote').css('lineHeight',(jQuery('.rt-block.logo-block').height()*0.35)+'px');
			
			
			// Main Menu
			jQuery('.dropdown.columns-1').css('minWidth',jQuery('#rt-sidebar-b').width()+'px');
			jQuery('.gf-menu .dropdown ul.l2').css('width',jQuery('#rt-sidebar-b').width()+'px');
			
			// Breadcrumbs
			jQuery('.rt-block.seanBreadcrumbs').css('height',jQuery('.rt-block.logo-block').height()+'px');
			jQuery('.rt-block.seanBreadcrumbs .breadcrumbs.seanBreadcrumbs').css('fontSize',(jQuery('.rt-block.logo-block').height()*0.5)+'px');
			jQuery('.rt-block.seanBreadcrumbs .breadcrumbs.seanBreadcrumbs').css('paddingTop',(jQuery('.rt-block.logo-block').height() - jQuery('.rt-block.seanBreadcrumbs .breadcrumbs.seanBreadcrumbs').height()*1.25)+'px');
	
			// Social Buttons
			// jQuery('.rt-social-buttons').css('marginLeft',(jQuery('#rt-sidebar-b').width()-jQuery('.rt-social-buttons').width())/2+'px');
	
			
			// Content Height
			//if(jQuery('#rt-content-top')) {
				//jQuery('#rt-content-top + .rt-block').height(jQuery('body').innerHeight() - jQuery('#rt-content-top .rt-block.seanBreadcrumbs').innerHeight());
				//jQuery('#rt-content-top + .rt-block').height(jQuery('body').innerHeight() - jQuery('#rt-content-top').innerHeight() - jQuery('#rt-content-bottom').innerHeight());
			//}
	
			// Home Page
			jQuery('.subCategoryContainer.learn .subCategory  h3').css('height',jQuery('.subCategoryContainer.shop .subCategory h3').height()+'px');
			
			// Sean
			jQuery('.itemListCategory.sean #read_1').height(jQuery('.itemListCategory.sean').height()-jQuery('.itemListCategory.sean h2').height()-70);
			
			// Gian Shift
			var sidebarFreeSpace = jQuery('#rt-sidebar-b').outerHeight(true) - jQuery('.rt-block.logo-block').outerHeight(true) - jQuery('.rt-block.registerNow').outerHeight(true) - jQuery('.rt-block.giantShift.hidden-phone').outerHeight(false);
			jQuery('.rt-block.giantShift.hidden-phone').css('marginTop',((sidebarFreeSpace >0)?(sidebarFreeSpace-10):0) +'px');
			
			// Learn or Shop Sidebar
			//if(jQuery('.itemListCategoriesBlock .linkBack').hasClass('linkBack')){
				//jQuery('.itemListCategoriesBlock .linkBack').css('height',jQuery('.component-content').height()+'px');
			//}
			if(jQuery('.customLearnSidebar').hasClass('customLearnSidebar')){
				jQuery('.customLearnSidebar').css('height',jQuery('.component-content').height()+'px');
				jQuery('.customLearnSidebar').css('width',(jQuery('.component-content').width()*0.05)+'px');
				jQuery('.customLearnSidebar').css('top',jQuery('#rt-content-top').height()+'px');
				jQuery('.component-content').css('width',(jQuery('#rt-mainbody').width()-jQuery('.customLearnSidebar').width()-70)+'px');
			};
			
			
			

			
			 
			
		} else {
			
			jQuery('body').removeAttr('style');
			// Titles
			jQuery('.title, .component-content h2').removeAttr('style');
			// Headers
			jQuery('h3').removeAttr('style');
			// Classes
				// Lead
				jQuery('.lead').removeAttr('style');
				// blockQuote
				jQuery('.blockQuote').removeAttr('style');

			// Breadcrumbs
			jQuery('.rt-block.seanBreadcrumbs').removeAttr('style');
			jQuery('.rt-block.seanBreadcrumbs .breadcrumbs.seanBreadcrumbs').removeAttr('style');
			
			// Content Height
			if(jQuery('#rt-content-top')) {
				jQuery('#rt-content-top + .rt-block').removeAttr('style');
			} 
			// Learn or Shop Sidebar
			//jQuery('.itemListCategoriesBlock .linkBack').removeAttr('style');
			jQuery('.customLearnSidebar').removeAttr('style');
			
			jQuery('.component-content').removeAttr('style');
			
			

		}
		

		
		if(navigator.userAgent.match(/(iPhone|iPod|iPad)/) ) {
			
		} else if(navigator.userAgent.match(/(Android)/)) {
			
		}
		
	}
	var asksean = function() {
		// Ask Sean
		var directcall = location.search.split('askSean=')[1];
		//alert(directcall);
		
		if(jQuery('#askSean .formError').text() || jQuery('.rt-block.askSeanModule #thankYouMessage p').text() || directcall == 'ask'){
			jQuery('.rt-block.askSeanModule').css('display','block');
		} else {
			jQuery('.rt-block.askSeanModule').css('display','none');
		}
		jQuery('#sidemenu .item149 a, span#askSeanClose').click(openasksean); 
	}
	var openasksean = function() {
		if(jQuery(window).height() < jQuery('.rt-block.askSeanModule').height()){
			(jQuery('.rt-block.askSeanModule').detach()).appendTo('body');
		} else {
			(jQuery('.rt-block.askSeanModule').detach()).appendTo('#rt-sidebar-b');
		};
		//closemobilemenu();
		// Hide jChat
		if(jQuery('#sidemenu .item150').hasClass('active')){
			jQuery('#sidemenu .item150').removeClass('active');
			jQuery('.rt-block.jChatContainer').fadeOut(duration);
		}
		
		jQuery('#askSean input.rsform-reset-button').click();
		jQuery('#sidemenu .item149').toggleClass('active');
		
		jQuery('.rt-block.askSeanModule').animate({
			opacity:[ "toggle", "swing" ]
		},duration);
		
		//return false;
	}
	
	var jchat = function() {
		jQuery('#sidemenu .item150 a, #jChatClose').click(function() {
			jQuery('#jchat_base, #jchat_optionsbutton_popup, #jchat_userstab_popup').attr('style', 'display: none !important');	
			//closemobilemenu();
			// Hide AskSean
			if(jQuery('#sidemenu .item149').hasClass('active')) {
				jQuery('#sidemenu .item149').removeClass('active');
				jQuery('.rt-block.askSeanModule').fadeOut(duration);
			}

			jQuery('#sidemenu .item150').toggleClass('active');
			jQuery('.rt-block.jChatContainer').animate({
				opacity:[ "toggle", "swing" ]
			},duration);
			return false;
		}); 
	}
	var closemobilemenu = function() {
		if(jQuery('body').hasClass('gf-sidemenu-size-marginleft')){
			jQuery('body').removeClass('gf-sidemenu-size-marginleft');
			jQuery('div.gf-menu-device-wrapper-sidemenu').removeClass('gf-sidemenu-size-left');
			jQuery('div.gf-menu-toggle').removeClass('active');
		}
	}
	
	var readmore = function() {
		jQuery('.readOn').click(function(e) {
				var idNumber = jQuery(e.target).attr('id').substr(9)
				jQuery(e.target).parent().slideUp(duration);
				jQuery('#read_'+idNumber).show(duration);
				scrollToAnchor('descrTop');
				return false;
		});
	}
	
	var shopsoon = function() {
		jQuery('.shop .itemListSubCategories .subCategoryContainer .subCategory h2 a').click(function(e) {
			return false;
		});
		jQuery('.shop .itemListSubCategories .subCategoryContainer').removeAttr('onclick');
	}
	var scrollToAnchor = function(aid){
		var aTag = $("a[name='"+ aid +"']");
		$('html,body').animate({scrollTop: aTag.offset().top},duration);
	}

	jQuery('.rt-block.jChatContainer').css('display','none');
	jQuery(window).load(adjustlayout);
	jQuery(window).resize(adjustlayout);
	jQuery('#openAskSean').click(openasksean);
	
	
	if(jQuery('#regConfirmation').attr('id')){
		jQuery('.component-content').addClass('regConfirmation');
		(jQuery('#rt-drawer').detach()).appendTo('.component-content');
	}
	
	
	//jQuery('div#rt-mainbody div.component-content input.rsform-input-box').attr('placeholder','');
	
	
	//asksean();
	jchat();
	readmore();
	shopsoon();
	
	//VIP page background and img visibility
	console.log(jQuery('.thnxMsg').css('display'));
	if(jQuery('.thnxMsg').length) {
		jQuery('.thnxMsg').removeAttr('style');
		jQuery('#ModuleChrome173').css('display','none'); 
		jQuery('#rt-main').css('background', '#000 url(' + 'images/vip/redCarpetThankYou.png' + ') no-repeat scroll');
		jQuery('#rt-main').css('background-size', '100% 80%');
		jQuery('#rt-main').css('background-position', 'bottom center');
		}
	else { jQuery('#ModuleChrome173').css('display','block');}
	if(jQuery(document).width()<=386&&jQuery('.thnxMsg').length) {jQuery('#rt-main').css('background-size', '130% 82%');}
	
	 else if	(jQuery(document).width()<=767&&jQuery('.thnxMsg').length) {jQuery('#rt-main').css('background-size', '130% 71%');}
	jQuery(window).resize(function() {
		if(jQuery('.thnxMsg').length) {
			if(jQuery(document).width()<=386) {jQuery('#rt-main').css('background-size', '130% 82%');}
			else if	(jQuery(document).width()<=767) {jQuery('#rt-main').css('background-size', '130% 71%');}
			else {
			jQuery('#rt-main').css('background', '#000 url(' + 'images/vip/redCarpetThankYou.png' + ') no-repeat scroll');
			jQuery('#rt-main').css('background-size', '100% 80%');
			jQuery('#rt-main').css('background-position', 'bottom center');	
			}
			
			
	}
	
	});
});
