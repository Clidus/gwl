<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>   
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/admin">Admin</a></span></li>    
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<h2><?php echo $pagetitle ?></h2>

<div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><b>New Post</b></h3>
    </div>
    <div class="panel-body">
      <p class="pull-left">Start a new blog post.</p>
      <a class="btn btn-default btn-primary pull-right" href="/admin/blog/new" role="button">New Post</a>
    </div>
  </div>

<?php
  foreach($posts as $post)
  {
?>
    <div class="panel panel-<?php echo $post->Published ? 'primary' : 'warning' ?>">
      <div class="panel-heading">
        <h3 class="panel-title">
          <?php 
            echo "<b>" . $post->Title . "</b>";
            if(!$post->Published) echo " (DRAFT)";
          ?>
        </h3>
      </div>
      <div class="panel-body">
        <div class="pull-left">
          <p><?php echo $post->Deck ?></p>
          <p><?php echo date_format(date_create($post->Date . " " . $post->Time), 'jS F, Y g:ia') ?></p>
        </div>
        <a class="btn btn-default pull-right" href="/admin/blog/edit/post/<?php echo $post->PostID ?>" role="button">Edit Post</a>
      </div>
    </div>
<?php
  }		
?>
<ul class="pager">
  <?php
    $previousPage = $page - 1;
    $nextPage = $page + 1;

    if($previousPage > 0) 
    {
      echo "<li class='pull-left'><a href='/admin/blog/edit/" . $previousPage . "'>Previous</a></li>";
    }
    echo "<li class='pull-right'><a href='/admin/blog/edit/" . $nextPage . "'>Next</a></li>";
  ?>
</ul>