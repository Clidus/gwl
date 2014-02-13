<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $game->name ?></span></li>
</ul>

<h2><?php echo $game->name ?></h2>

<div class="row">
    <div class="col-sm-4">
        <img class="gameBoxArt gamePageBoxArt" src="<?php if(is_object($game->image)) echo $game->image->small_url; ?>">
        <h4>Game Details</h4>
        <ul class="list-group">
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>First Release Date:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php 
                        if(property_exists($game, "original_release_date") && $game->original_release_date != null)
                            echo date_format(date_create($game->original_release_date), 'jS F, Y') ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Developer:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "developers") && $game->developers != null)
                            foreach($game->developers as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Publisher:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "publishers") && $game->publishers != null)
                            foreach($game->publishers as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Genre:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "genres") && $game->genres != null)
                            foreach($game->genres as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Theme:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "themes") && $game->themes != null)
                            foreach($game->themes as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Franchise:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "franchises") && $game->franchises != null)
                            foreach($game->franchises as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
        </ul>
    </div>
    <div class="col-sm-8">
        <div class="panel panel-default"> 
            <div class="panel-body">
              <?php echo $game->deck; ?>             
            </div>  
            <div class="panel-footer">
                <?php    
                    if(property_exists($game, "platforms") && $game->platforms != null)
                        foreach($game->platforms as $gbPlatform)
                            echo "<span class='label label-info'>" . $gbPlatform->name . "</span> ";    
                ?>  
            </div>                                                   
        </div>                 
        <div class="pull-right">  
            <div class="btn-group searchResultButton">
            <?php 
                if($sessionUserID > 0) 
                {            
                    if($game->listID > 0)
                    {
                        switch($game->listID)
                        {
                            case 1:
                                $buttonLabel = "Own";
                                $buttonStyle = "success";
                                break;
                            case 2:
                                $buttonLabel = "Want";
                                $buttonStyle = "warning";
                                break;
                            case 3:
                                $buttonLabel = "Borrowed";
                                $buttonStyle = "info";
                                break;
                            case 4:
                                $buttonLabel = "Played";
                                $buttonStyle = "primary";
                                break;
                        }
                        echo "<button id='gameButton" . $game->id . "' data-toggle='dropdown' class='btn btn-" . $buttonStyle . " dropdown-toggle'>" . $buttonLabel . " <span class='caret'></span></button>";
                    }  
                    else
                    {           
                        echo "<button id='gameButton" . $game->id . "' data-toggle='dropdown' class='btn btn-default dropdown-toggle'>Add to Collection <span class='caret'></span></button>";
                    } 
            ?>
                <ul class="dropdown-menu">
                    <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url . "'," . $game->listID ?>, 1);">Own</a></li>
                    <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url . "'," . $game->listID ?>, 2);">Want</a></li>
                    <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url . "'," . $game->listID ?>, 3);">Borrowed</a></li>
                    <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url . "'," . $game->listID ?>, 4);">Played</a></li>
                </ul>
            </div> 
            <a href="<?php echo $game->site_detail_url; ?>" target="_blank" class="btn btn-default searchResultButton">Read More</a>
            <?php } ?>   
        </div>
    </div>
</div>