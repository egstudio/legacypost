<div class="wrap" id="legacy-posts-wrapper">
<h2><span class="icon"></span>Legacy Posts <a href="admin.php?page=legacy-post-new" class="add-new-h2">Add new</a></h2>
<div id="legacy-post-actions" class="tablenav top">
<p class="alignleft">View by category: </p>
<form id="legacy-post-show-category-form" action="" method="post">
<?php 
$args = array('show_option_all' => 'All Categories', 'hide_empty' => 0, 'name' => 'show_category', 'hierarchical' => true);
if (isset($_POST['show_category']))
	$args['selected'] = $_POST['show_category'];
wp_dropdown_categories($args); 
?>
</form>
</div>
<div id="legacy-post-show-all">
<table id="legacy-posts<?php if (isset($_POST['show_category']) && $_POST['show_category'] != '0') echo '-sortable'; ?>" class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Content</th>
			<th>Image URL</th>
			<th>Image Title</th>
			<th>Post link</th>
			<th>Category</th>
			<th>Order</th>
			<th>Tags</th>
			<th>Created</th>
			<th>Modified</th>
			<th>Actions</th>
			
		</tr>
	</thead>		
	<tbody class="content">
<?php
foreach ($posts as $post) :
?>
		<tr>
			<td class="post-id" id="<?php echo $post->id; ?>"><?php echo $post->id; ?></td>
			<td><?php echo $post->title; ?></td>
			<td class="content"><?php echo $post->content; ?></td>
			<td><?php echo $post->img; ?></td>
			<td><?php echo $post->img_title; ?></td>
			<td><?php echo $post->link; ?></td>
			<td><?php echo get_cat_name($post->category); ?></td>
			<td class="post-position" id="<?php echo $post->position; ?>"><?php echo $post->position; ?></td>
			<td><?php 
				if ($post->tags != 0) {
					$post->tags = explode(',', $post->tags);
					foreach ($post->tags as $tag) :
				 	echo get_tag($tag)->name,", ";
					endforeach;
				}
				else
					echo 'No tags defined';
			?></td>
			<td><?php echo $post->created; ?></td>
			<td><?php echo $post->modified; ?></td>
			<td><a href="?page=legacy-post-new&id=<?php echo $post->id; ?>">Edit</a> | <a href="<?php echo $post->id; ?>" class="delete">Delete</a></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
</div>
</div>