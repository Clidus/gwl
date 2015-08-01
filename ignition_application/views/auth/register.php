<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<?php 
    echo validation_errors(); 
    if($errorMessage != '')
    {
        echo '<div class="alert alert-danger">' . $errorMessage . '<a class="close" data-dismiss="alert" href="#">&times;</a></div>';
    } 
?>

<div class="alert alert-warning">
    Please use a unique, strong password for this site. We recommend <a href="https://lastpass.com/" target="_blank">LastPass</a>.
</div>

<?php echo form_open('register'); ?>
    <div class="form-group">
        <label for="emailInput">Email</label>
        <input type="email" class="form-control" maxlength="100" id="emailInput" name="email" placeholder="Email">
    </div>
    <div class="form-group">
        <label for="usernameInput">Username</label>
        <input type="text" class="form-control" maxlength="50" name="username" id="usernameInput" placeholder="Username">
    </div>
    <div class="form-group">
        <label for="passwordInput">Password</label>
        <input type="password" class="form-control" maxlength="50" name="password" id="passwordInput" placeholder="Password">
    </div>
    <div class="form-group">
        <label for="conpasswordInput">Confirm Password</label>
        <input type="password" class="form-control" maxlength="50" name="conpassword" id="conpasswordInput" placeholder="Confirm Password">
    </div>
    <button type="submit" name="register" class="btn btn-default">Submit</button>
</form>