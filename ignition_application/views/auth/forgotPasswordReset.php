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
        echo '<div class="alert alert-warning">Please enter a new password.</div>';
        echo form_open('forgotReset'); 
?>
        <div class="form-group">
            <label for="passwordInput">New Password</label>
            <input type="password" class="form-control" maxlength="50" name="newPassword" id="newPassword" placeholder="New Password">
            <input type="hidden" name="code" id="code" value="<?php echo $code ?>">
        </div>
        <button type="submit" name="forgotPassword" class="btn btn-default">Submit</button>
    </form>

<?php
    }
?>