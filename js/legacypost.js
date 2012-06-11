jQuery(document).ready(function($) {
	$('#legacy-post-actions').find('select[name="show_category"]').bind('change', function() {
		$('#legacy-post-show-category-form').submit();
	});
	
	$('td.content').each(function() {
		var $this = $(this),
			content = $this.text(),
			flag = false;
		$this.empty();
		$this.append('<a href="/">Open / Close Content</a>');
		$this.find('a').bind('click', function() {
			if (!flag) {
				flag = true;
				$this.append(content);
			}
			else {
				flag = false;
				$this.empty();
				$this.append('<a href="/">Open / Close Content</a>');
			}
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
	
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};
	
	$('#legacy-posts-sortable .content').sortable({
		placeholder: 'content-highlight',
		containment: 'parent',
		helper: fixHelper,
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