<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>   
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/blog">Blog</a></span></li>
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/blog/archive">Archive</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $title ?></span></li>
</ul>

<div class="row">
	<div class="col-sm-8">
		<h2><?php echo $title ?></h2>
		<?php
			foreach($posts as $post)
			{
				echo "<p><a href='/blog/" . $post->URL . "'>" . $post->Title . "</a> - " . date_format(date_create($post->Date), 'jS F, Y') . ".</p>";
			}
		?>
	</div>