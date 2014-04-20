<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>   
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>index.php/admin">Admin</a></span></li>    
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<h2><?php echo $pagetitle ?></h2>

<?php
  foreach($posts as $post)
  {
?>
  <ul>
    <li><a href="<?php echo $baseUrl . "admin/blog/edit/" . $post->PostID ?>"><?php echo $post->Title ?></a></li>
  </ul>
<?php
  }
?>
