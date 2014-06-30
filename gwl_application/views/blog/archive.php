<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>   
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/blog">Blog</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title">Archive</span></li>
</ul>

<div class="row">
	<div class="col-sm-8">
		<?php
			foreach($posts as $post)
			{
		?>
				<h2><a href="/blog/<?php echo $post->URL ?>"><?php echo $post->Title ?></a></h2>

				<p class="postFooter">Posted by <a href="/user/<?php echo $post->UserID; ?>"><?php echo $post->Username ?></a> on the <?php echo date_format(date_create($post->Date), 'jS F, Y') ?>.</p>

				<hr />
		<?php
			}
		?>
	</div>