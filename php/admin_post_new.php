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
			<div id="categorydiv" class="postbox ">
				<h3 class="hndle">
					<span>Categories</span>
				</h3>
				<div class="inside">
				<?php				
					$walker = new Walker_Category_Checklist;
					$taxonomy = 'category';
					$categories = (array) get_terms($taxonomy, array('get' => 'all'));
					$args = array();
					$args['selected_cats'] = (isset($data->category) ? explode(',', $data->category) : array());
					$args['popular_cats'] = array();
				?>
					<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
						<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
							<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
							<?php 
								echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
							?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			
			<div id="tagdiv" class="postbox ">
				<h3 class="hndle">
					<span>Tags</span>
				</h3>
				<div class="inside">
				<?php
					$walker = new Walker_Category_Checklist;
					$taxonomy = 'post_tag';
					$categories = (array) get_terms($taxonomy, array('get' => 'all'));
					$args = array('taxonomy' => 'post_tag');
					$args['selected_cats'] = (isset($data->tags) ? explode(',', $data->tags) : array());
					$args['popular_cats'] = array();
				?>
					<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
						<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
							<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
							<?php
								echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
							?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			
		</fieldset>
		<p>
			<input type="submit" name="legacy-post-submit" value="Publish" id="legacy-post-submit" class="button-primary"/>
		</p>
		<div class="clear"></div>
	</form>
	</div>
</div>