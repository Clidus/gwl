<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>   
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/blog">Blog</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title">Archive</span></li>
</ul>

<div class="row">
	<div class="col-sm-8">
		<h2>Blog Archive</h2>
		<?php
			foreach($months as $month)
			{
				echo "<p><a href='/blog/archive/" . $month->Year . "/" . $month->Month . "'>" . $month->MonthName . " " . $month->Year . "</p>";
			}
		?>
	</div>