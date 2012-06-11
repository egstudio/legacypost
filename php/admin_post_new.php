<div class="wrap" id="legacy-posts-new-wrapper">
	<h2><span class="icon"></span>Add New Legacy Post</h2>
	<div id="legacy-post-new">
	<form id="legacy-post-new-form" action="" method="post">
		<fieldset>
			<p>
				<input type="text" name="title" value="<?php echo (isset($data) ? $data->title : ''); ?>" id="title" placeholder="Enter title here"><br />
			</p>
			<div class="legacy-content">
				<textarea name="content" rows="8" cols="40"><?php echo (isset($data) ? $data->content : ''); ?></textarea><br />
			</div>
			<p>
				<label for="img_title">Image Title</label>
				<input type="text" class="txt" name="img_title" value="<?php echo (isset($data) ? $data->img_title : ''); ?>" id="img_title"><br />
			</p>
			<p>
				<label for="img">Image Link</label>
				<input type="text" class="txt" name="img" value="<?php echo (isset($data) ? $data->img : ''); ?>" id="img"><br />
			</p>
			<p>
				<label for="link">Post Link</label>
				<input type="text" class="txt" name="link" value="<?php echo (isset($data) ? $data->link : ''); ?>" id="link"><br />
			</p>
			
			<label for="category">Category</label>
			<?php 
				$args = array('hide_empty' => 0, 'name' => 'category', 'hierarchical' => true);
				if (isset($data))
					$args['selected'] = $data->category;
				wp_dropdown_categories($args); 
			?>
		</fieldset>
		<p>
			<input type="submit" name="legacy-post-submit" value="Publish" id="legacy-post-submit" class="button-primary"/>
		</p>
		<div class="clear"></div>
	</form>
	</div>
</div>