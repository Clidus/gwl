<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title">Home</span></li>   
</ul>

<?php
	foreach($posts->result() as $post)
	{
?>
	<h2><a href="<?php echo $baseUrl . "blog/" . $post->URL ?>"><?php echo $post->Title ?></a></h2>
	<p>By <?php echo $post->Username ?></p>

	<div class="clearfix">
		<?php echo $post->Post ?>
	</div>

	<p class="postFooter">Posted <?php echo date_format(date_create($post->Date), 'jS F, Y') ?></p>
<?php
	}
?>