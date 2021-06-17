<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--  This file has been downloaded from bootdey.com    @bootdey on twitter -->
    <!--  All snippets are MIT license http://bootdey.com/license -->
    <title>Chat Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
    body {
        background: #f9f9f9;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #BA68C8
    }

    .profile-button {
        background: rgb(99, 39, 120);
        box-shadow: none;
        border: none
    }

    .profile-button:hover {
        background: #682773
    }

    .profile-button:focus {
        background: #682773;
        box-shadow: none
    }

    .profile-button:active {
        background: #682773;
        box-shadow: none
    }

    .back:hover {
        color: #682773;
        cursor: pointer
    }

    .labels {
        font-size: 11px
    }

    .add-experience:hover {
        background: #BA68C8;
        color: #fff;
        cursor: pointer;
        border: solid 1px #BA68C8
    }

    .error {
        color: red;
    }
    </style>

<?php
<?php
if (!$this->session->userdata('is_logged')) {
    redirect('home');
}

$user_image = base_url()."assets/images/user.png";
if ($this->session->userdata('user_image')) {
    $user_image = base_url()."uploads/".$this->session->userdata('user_image');
}
?>

    <div class="offset-1 col-md-10 mt-5" style="text-align: right;">
        <a class="btn btn-success" href="<?php echo base_url('chat'); ?>"><i class="fa fa-comments mr-2"></i> Chat</a>
        <a class="btn btn-primary" href="<?php echo base_url('home/logout'); ?>"><i class="fa fa-power-off mr-2"></i> Logout</a>
    </div>

    <div class="offset-1 col-md-10">&nbsp;
        <?php if ($this->session->userdata('success')) { ?>
            <div class="col-md-12 alert alert-success">
                <?php
                    echo $this->session->userdata('success');
                    $this->session->unset_userdata('success');
                ?>
            </div>
        <?php } elseif ($this->session->userdata('error')) { ?>
            <div class="col-md-12 alert alert-danger">
                <?php
                    echo $this->session->userdata('error');
                    $this->session->unset_userdata('error');
                ?>
            </div>
        <?php } ?>
    </div>

    <div class="container rounded bg-white mb-5">
        <div class="row">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <img class="rounded-circle mt-5" width="150px" src="<?php echo $user_image; ?>">
                    <span class="font-weight-bold"><?php echo $userData['us_first_name']." ".$userData['us_last_name']; ?></span>
                    <span class="text-black-50"><?php echo $userData['us_email']; ?></span>
                    <span> </span>
                </div>
            </div>

            <div class="col-md-5 border-right">
                <div class="p-3 py-5">
                    <form action="<?php echo base_url('home/updateProfile'); ?>" method="POST" onsubmit="return profileValidation();" enctype="multipart/form-data">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Profile Settings</h4>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12"><label class="labels">First Name</label><input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter First Name" value="<?php echo $userData['us_first_name']; ?>"></div>
                            <span class="col-md-12 error" id="err_first_name"></span>
                            <div class="col-md-12"><label class="labels">Last Name</label><input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter Last Name" value="<?php echo $userData['us_last_name']; ?>"></div>
                            <span class="col-md-12 error" id="err_last_name"></span>
                            <div class="col-md-12"><label class="labels">Profile Image</label><input type="file" name="profile" id="profile" class="form-control" style="border: 0px;"></div>
                        </div>
                        <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit">Save Profile</button></div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <?php if (!$this->session->userdata('social_login')) { ?>
                <div class="p-3 py-5">
                    <form action="<?php echo base_url('home/updatePassword'); ?>" method="POST" onsubmit="return passwordValidation();">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Change Password</h4>
                        </div>

                        <div class="col-md-12"><label class="labels">Current Password</label><input type="password" name="current_password" id="current_password" class="form-control" placeholder="Current Password"></div>
                        <span class="col-md-12 error" id="err_current_password"></span>
                        <div class="col-md-12"><label class="labels">Password</label><input type="password" name="password" id="password" class="form-control" placeholder="Password"></div>
                        <span class="col-md-12 error" id="err_password"></span>
                        <div class="col-md-12"><label class="labels">Confirm Password</label><input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password"></div>
                        <span class="col-md-12 error" id="err_confim_pasword"></span>

                        <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit">Change Password</button></div>
                    </form>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script src="<?php echo base_url() ?>assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/bootstrap.bundle.min.js"></script>

    <script> 
     $(function () {
        $('input').keypress(function(){
            var iid = $(this).attr('id');
            $('#err_'+iid).text('');
        });
    });

    function profileValidation(){
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();

        var error = 0;

        var alpha_regex = /^[a-zA-Z]*$/;
    
        if(!first_name){
            $('#err_first_name').text('Please enter first name');
            error = 1;
        } else if(!alpha_regex.test(first_name)){
            $('#err_first_name').text('Please enter a valid first name');
            error = 1;
        } 

        if(!last_name){
            $('#err_last_name').text('Please enter last name');
            error = 1;
        } else if(!alpha_regex.test(last_name)){
            $('#err_last_name').text('Please enter a valid last name');
            error = 1;
        } 

        if(error){
            return false;
        } else {
            return true;
        }
    }

    function passwordValidation(){
        var current_password = $('#current_password').val();
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();

        var error = 0;

        if(!current_password){
            $('#err_current_password').text('Please enter current password');
            error = 1;
        }

        if(!password){
            $('#err_password').text('Please enter password');
            error = 1;
        }

        if(!confirm_password){
            $('#err_confim_pasword').text('Please enter confirm password');
            error = 1;
        }

        if(password && confirm_password && password != confirm_password){
            $('#err_password').text("Entered password doesn't match with the confirm password");
            error = 1;
        }

        if(error){
            return false;
        } else {
            return true;
        }
    }
    </script>