<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<?php 
    echo validation_errors(); 
    if($errorMessage != '')
    {
        echo '<div class="alert alert-danger">' . $errorMessage . '<a class="close" data-dismiss="alert" href="#">&times;</a></div>';
    } else if($successMessage != '') {
        echo '<div class="alert alert-success">' . $successMessage . '<a class="close" data-dismiss="alert" href="#">&times;</a></div>';
    } else {
        echo '<div class="alert alert-warning">Please provide your username and we will send you a password reset email.</div>';
    }
        
    echo form_open('forgot'); 
?>
        <div class="form-group">
            <label for="usernameInput">Username</label>
            <input type="text" class="form-control" maxlength="50" name="username" id="usernameInput" placeholder="Username">
        </div>
        <button type="submit" name="forgotPassword" class="btn btn-default">Submit</button>
    </form>