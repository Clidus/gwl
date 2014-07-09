<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title">Blog</span></li>
</ul>

<div class="row">
	<div class="col-sm-8">
		<?php
			foreach($recentPosts as $post)
			{
		?>
				<h2><a href="/blog/<?php echo $post->URL ?>"><?php echo $post->Title ?></a></h2>
				
				<?php if($post->Image != null) { ?>
					<img src="<?php echo $post->Image ?>" class="responsiveImage" title="<?php echo $post->Title ?>" alt="<?php echo $post->Title ?>" />
				<?php } ?>

				<div class="clearfix">
					<?php echo $post->Post ?>
				</div>

				<p class="postFooter">
					Posted by <a href="/user/<?php echo $post->UserID; ?>"><?php echo $post->Username ?></a> on the <?php echo date_format(date_create($post->Date), 'jS F, Y') ?>. 
					<span class="pull-right">
						<a href="/blog/<?php echo $post->URL ?>#comments"><?php echo $post->CommentsLabel; ?></a>.
					</span>
				</p>

				<hr />
		<?php
			}
		?>
	</div>