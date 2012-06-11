jQuery(document).ready(function($) {
	$('#legacy-post-actions').find('select[name="show_category"]').bind('change', function() {
		$('#legacy-post-show-category-form').submit();
	});
	
	$('#legacy-post-actions').find('select[name="show_tag"]').bind('change', function() {
		$('#legacy-post-show-tag-form').submit();
	});
	
	$('td.content').each(function() {
		var $this = $(this),
			content = $this.text(),
			$link = $("<a href='/'>Expand / Contract</a>");
		$this.find('div.content').hide();
		$this.append($link);
		$link.bind('click', function() {
			$this.find('div.content').toggle();
			return false;
		});
	});
	
	$('a.delete').each(function() {
		var that = this;
		$(this).click(function(event) {
			var answer = confirm("Are you sure you want to delete this item?");
			if (answer) {
				$.post(ajaxurl, {
					'action': 'delete_post_submit',
					'post-id': $(this).attr('href'),
					'nonce': LegacyPost.legacyNonce
				},
				function() {
					$(that).parent().parent().animate({opacity: 0}, 'slow', function() {
						$(that).parent().parent().remove();	
					});
				});
			}
			return false;
		})
	});
	
	$('#legacy-posts-sortable .content').sortable({
		placeholder: 'content-highlight',
		containment: 'parent',
		helper: function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		},
		start: function(event, ui) {
			$(ui.item).css('width','100%');
		},
		cursor: 'move',
		stop: function (event, ui) {
			var id = $(ui.item).find('.post-id').attr('id'),
				position = ui.item.prevAll().length+1;
			$.post(ajaxurl, {
				'action': 'update_position_submit',
				'post-id': id,
				'post-position': position,
				'nonce': LegacyPost.legacyNonce
			}, function(data) {
				$(ui.item).effect('highlight', {color: '#c4df9b'});
			});
		}
	});
	$('#legacy-posts-sortable .content').disableSelection();
});