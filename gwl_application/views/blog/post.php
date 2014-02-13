<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>  
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $post->Title ?></span></li>   
</ul>

<h2><?php echo $post->Title ?></h2>
<p>By <?php echo $post->username ?></p>

<div class="clearfix">
	<?php echo $post->Post ?>
</div>

<p class="postFooter">Posted <?php echo date_format(date_create($post->Date), 'jS F, Y') ?></p>