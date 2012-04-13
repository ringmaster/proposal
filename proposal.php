<article id="post-<?php echo $content->id; ?>" class="post proposal">
	<div class="postcontrols">
	<?php if($post->get_access(User::identify())->edit): ?>
	<a class="edit_post" href="<?php URL::out('admin', array('page'=>'publish', 'id'=>$content->id)); ?>">Edit Proposal</a>
	<?php endif; ?>
	</div>

	<header>
		<h2>Project Proposal</h2>
		<h1><?php echo $content->title_out; ?></h1>

		<section>
			<b>Prepared For</b><br>
			<?php echo $content->client_contact->displayname; ?><br>
			<?php echo $content->client_contact->client->title; ?>
		</section>

		<section>
			<b>By</b><br>
			<div class="by_name"><?php echo $content->staff->displayname; ?></div>
			<div class="by_company"><?php echo $content->staff->client->title; ?></div>
			<div class="contact_phone"><?php echo $content->staff->info->phone; ?></div>
			<div class="contact_email"><?php echo $content->staff->email; ?></div>
		</section>
	</header>

	<?php echo $content->content_out; ?>

	<?php if($request->display_entry): ?>
	<section class="comments" itemprop="comment">
		<h1 id="comments">Comments</h1>
		<?php if($content->comments->moderated->count == 0): ?>
			<p><?php _e('There are no comments on this post.'); ?>
		<?php else: ?>
			<?php foreach($content->comments->moderated->comments as $comment): ?>
				<?php echo $theme->content($comment); ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if($post->info->comments_disabled): ?>
			<p><?php _e('Sorry, commenting on this post is disabled.'); ?>
		<?php else: ?>
		<?php $post->comment_form()->out(); ?>
		<?php endif; ?>
	</section>
	<?php endif; ?>

</article>
