(function($){
	
	$.fn.placeholder = function(options){
		
		/*
		
			Usage
			=====
			
			var options = {
				'attr'		:	'title',
				'focusin'	:	'focusin',
				'focusout'	:	'focusout'
			}
			
			$(selector).placeholder(options);
			
		*/
		
		var settings = $.extend( {
			'attr' 		:	'title',
			'focusin'	:	'focusin',
			'focusout'	:	'focusout'
		}, options);
				
		return this.each(function(){
			
			var $this = $(this);
			var placeholder = $this.attr(settings['attr']);
						
			// Set placeholder
			if ($this.val() == '' || $this.val() == $this.attr(settings['attr']) ){
				// Add focus out class initially
				$this.addClass(settings['focusout']);
				$this.val(placeholder);
			}

			// Focus in, focus out
			$($this).on('focusin focusout', function(event){
				
				$(this).removeClass(settings['focusin']);
				$(this).removeClass(settings['focusout']);
				
				if (event.type == 'focusin'){
					
					$(this).addClass(settings['focusin']);
									
					if ($(this).val() == placeholder){
						$(this).val('');						
					}

				} else {
				
					if ($(this).val() == ''){
						$(this).val(placeholder);
						$(this).addClass(settings['focusout']);
					}					
					
				}
				
			});
		
		});
		
	}
	
})(jQuery)
