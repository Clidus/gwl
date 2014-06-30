<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>  
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/blog">Blog</a></span></li>  
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $post->Title ?></span></li>   
</ul>

<div class="row">
	<div class="col-sm-8">
		<h2><?php echo $post->Title ?></h2>
		
		<?php if($post->YouTube != null) { ?>
			<div class="embed-responsive embed-responsive-16by9 responsiveVideo">
				<iframe class="embed-responsive-item" src="<?php echo $post->YouTube ?>" allowfullscreen></iframe>
			</div>
		<?php } else if($post->Image != null) { ?>
			<img src="<?php echo $post->Image ?>" class="responsiveImage" title="<?php echo $post->Title ?>" alt="<?php echo $post->Title ?>" />
		<?php } ?>

		<div class="clearfix">
			<?php echo $post->Post ?>
		</div>

		<p class="postFooter">Posted by <a href="/user/<?php echo $post->UserID; ?>"><?php echo $post->Username ?></a> on the <?php echo date_format(date_create($post->Date), 'jS F, Y') ?>.</p>
	</div>