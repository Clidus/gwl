<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title">Blog</span></li>
</ul>

<div class="row">
	<div class="col-sm-8">
		<?php
			foreach($posts as $post)
			{
		?>
				<div class="blog-post">
					<h2><a href="/blog/<?php echo $post->URL ?>"><?php echo $post->Title ?></a></h2>
					
					<?php if($post->Image != null) { ?>
						<img src="<?php echo $post->Image ?>" class="responsiveImage" title="<?php echo $post->Title ?>" alt="<?php echo $post->Title ?>" />
					<?php } ?>

					<div class="clearfix">
						<?php echo $post->Post ?>
					</div>

					<p class="postFooter">
						Posted by <a href="/user/<?php echo $post->UserID; ?>"><?php echo $post->Username ?></a> on the <?php echo date_format(date_create($post->Date . " " . $post->Time), 'jS F, Y g:ia') ?>. 
						<span class="pull-right">
							<a href="/blog/<?php echo $post->URL ?>#comments"><?php echo $post->CommentsLabel; ?></a>.
						</span>
					</p>
				</div>

				<hr />
		<?php
			}
		?> 
		<ul class="pager">
			<?php
				$previousPage = $page - 1;
				$nextPage = $page + 1;

				if($previousPage > 0) 
				{
					echo "<li class='pull-left'><a href='/blog/";
					if($previousPage > 1) 
						echo "page/" . $previousPage;
					echo "'>Previous</a></li>";
				}
				echo "<li class='pull-right'><a href='/blog/page/" . $nextPage . "'>Next</a></li>";
			?>
		</ul>
	</div>