<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>   
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>index.php/admin">Admin</a></span></li>    
    <?php if($post->PostID > 0) { ?>
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>index.php/admin/blog/edit">Edit Blog Post</a></span></li>    
    <?php } ?>
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<h2><?php echo $pagetitle ?></h2>

<?php 
	if($formSuccess) {
		echo "<div class='alert alert-success'>Success! Your post is up!</div>";
	} else {
		echo validation_errors(); 
	}
?>
<form action="<?php echo $baseUrl ?>index.php/admin/blog/<?php echo $formType ?>" method="post" accept-charset="utf-8" role="form">
	<div class="form-group"> 
	    <label for="title">Title</label>
	    <input class="form-control" type="text" name="title" value="<?php echo $post->Title ?>">
	</div>
    <div class="form-group"> 
		<label for="url">URL</label>
    	<input class="form-control" type="text" name="url" value="<?php echo $post->URL ?>">
    </div>
    <div class="form-group"> 
      <div id="wysihtml5-toolbar" class="pr-inputRow pr-editorToolbar" style="display: none;">
        <a data-wysihtml5-command="bold"></a>
        <a data-wysihtml5-command="italic"></a>
        <a data-wysihtml5-command="createLink"></a>
        <a data-wysihtml5-command="insertImage"></a>
        <a data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h1"></a>
        <a data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2"></a>
        <a data-wysihtml5-command="insertUnorderedList"></a>
        <a data-wysihtml5-command="insertOrderedList"></a>
        <a data-wysihtml5-action="change_view"></a>
        <div data-wysihtml5-dialog="createLink" style="display: none;">
          <label>Link: <input data-wysihtml5-dialog-field="href" value="http://" class="text"></label>
          <a data-wysihtml5-dialog-action="save">OK</a> <a data-wysihtml5-dialog-action="cancel">Cancel</a>
        </div>
        <div data-wysihtml5-dialog="insertImage" style="display: none;">
          <label>
            Image:
            <input data-wysihtml5-dialog-field="src" value="http://">
          </label>
          <label>
            Align:
            <select data-wysihtml5-dialog-field="className">
              <option value="">default</option>
              <option value="wysiwyg-float-left">left</option>
              <option value="wysiwyg-float-right">right</option>
            </select>
          </label>
          <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
        </div>
    </div>
    	<textarea id="wysihtml5-textarea" placeholder="Enter your text ..." class="form-control" name="post"><?php echo $post->Post ?></textarea>
    </div>
    <button type="submit" class="btn btn-default">Post</button>
    <?php if($post->PostID > 0) { ?>
    <a onclick="javascript:deleteBlogPost(<?php echo $post->PostID ?>);"><div class="btn btn-danger pull-right">Delete</div></a>
    <?php } ?>
</form>

<!-- wysihtml5 parser rules -->
<script src="<?php echo $baseUrl ?>/wysihtml5/parser_rules/advanced.js"></script>
<!-- Library -->
<script src="<?php echo $baseUrl ?>/wysihtml5/dist/wysihtml5-0.3.0.min.js"></script>
<script>
var editor = new wysihtml5.Editor("wysihtml5-textarea", { // id of textarea element
  toolbar:      "wysihtml5-toolbar", // id of toolbar element
  parserRules:  wysihtml5ParserRules, // defined in parser rules set 
  stylesheets: ["<?php echo $baseUrl ?>css/editor.css"]
});
</script>