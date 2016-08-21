<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>   
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/admin">Admin</a></span></li>    
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<h2><?php echo $pagetitle ?></h2>

<?php 
	echo validation_errors(); 
    echo form_open('admin/blog/new'); 
?>
	<div class="form-group"> 
	    <label for="title">Title</label>
	    <input class="form-control" type="text" name="title" value="<?php echo set_value('title'); ?>">
	</div>
    <div class="form-group"> 
		<label for="url">Deck</label>
    	<input class="form-control" type="text" name="deck" value="<?php echo set_value('deck'); ?>">
    </div>
    <div class="form-group"> 
		<label for="post">Post</label>
    	<textarea data-provide="markdown" id="blogPostTextArea" placeholder="Enter your text ..." class="form-control" name="post"><?php echo set_value('post'); ?></textarea>
    </div>
    <button type="submit" class="btn btn-default pull-right">Save</button>
    <p><i>This post will be saved as a draft.</i></p>
</form>