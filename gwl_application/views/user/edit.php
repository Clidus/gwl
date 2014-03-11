<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl . "user/" . $sessionUserID ?>"><?php echo $sessionUsername ?></a></span></li>
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title">Edit Profile</span></li>
</ul>

<div class="row">
    <div class="col-sm-12">
		<h2>Edit Profile</h2>

		<?php echo $error;?>

			<?php echo form_open_multipart('user/save');?>

			<input type="file" name="userfile" size="20" />

			<br /><br />

			<input type="submit" value="upload" />

		</form>
	</div>
</div>