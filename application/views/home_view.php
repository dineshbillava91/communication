<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Chat Application</title>
        <link href="<?php echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url() ?>assets/css/font-awesome.min.css" rel="stylesheet">
    </head>

    <style>
    /*
    *
    * ==========================================
    * FOR DEMO PURPOSES
    * ==========================================
    *
    */
    body {
        min-height: 100vh;
    }

    .form-control:not(select) {
        padding: 1.5rem 0.5rem;
    }

    select.form-control {
        height: 52px;
        padding-left: 0.5rem;
    }

    .form-control::placeholder {
        color: #ccc;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .form-control:focus {
        box-shadow: none;
    }

    .error {
        color: red;
        padding-left: 0px;
        padding-right: 0px;
    }

    .alert {
        text-align: center;
    }
    </style>

    <body>
        <!-- Navbar-->
        <header class="header">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <div class="container">
                    <!-- Navbar Brand -->
                    <a href="#" class="navbar-brand" style="font-weight: bold; font-size: 36px;"> 
                        CHAT APPLICATION
                    </a>
                </div>
            </nav>
        </header>


        <div class="container">
            <div class="row py-5 mt-4 align-items-center">
                <!-- For Demo Purpose -->
                <div class="col-md-5 pr-lg-5 mb-5 mb-md-0">
                    <img src="https://res.cloudinary.com/mhmd/image/
                    upload/v1569543678/form_d9sh6m.svg" alt="" class="img-fluid mb-3 d-none d-md-block">
                    <h1>Create an Account</h1>
                </div>

                <!-- Registeration Form -->
                <div class="col-md-7 col-lg-6 ml-auto">
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
                    <form action="<?php echo base_url('home/register'); ?>" method="POST" 
                    onsubmit="return validation();">
                        <div class="row">

                            <!-- First Name -->
                            <div class="input-group col-lg-6 mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white px-4 border-md border-right-0">
                                        <i class="fa fa-user text-muted"></i>
                                    </span>
                                </div>
                                <input id="firstname" type="text" name="firstname" placeholder="First Name" 
                                class="form-control bg-white border-left-0 border-md">
                                <span class="col-md-12 error" id="err_firstname"></span>
                            </div>

                            <!-- Last Name -->
                            <div class="input-group col-lg-6 mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white px-4 border-md border-right-0">
                                        <i class="fa fa-user text-muted"></i>
                                    </span>
                                </div>
                                <input id="lastname" type="text" name="lastname" placeholder="Last Name" 
                                class="form-control bg-white border-left-0 border-md">
                                <span class="col-md-12 error" id="err_lastname"></span>
                            </div>

                            <!-- Email Address -->
                            <div class="input-group col-lg-12 mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white px-4 border-md border-right-0">
                                        <i class="fa fa-envelope text-muted"></i>
                                    </span>
                                </div>
                                <input id="email" type="email" name="email" placeholder="Email Address" 
                                class="form-control bg-white border-left-0 border-md">
                                <span class="col-md-12 error" id="err_email"></span>
                            </div>

                            <!-- Password -->
                            <div class="input-group col-lg-6 mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white px-4 border-md border-right-0">
                                        <i class="fa fa-lock text-muted"></i>
                                    </span>
                                </div>
                                <input id="password" type="password" name="password" placeholder="Password" 
                                class="form-control bg-white border-left-0 border-md">
                                <span class="col-md-12 error" id="err_password"></span>
                            </div>

                            <!-- Password Confirmation -->
                            <div class="input-group col-lg-6 mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white px-4 border-md border-right-0">
                                        <i class="fa fa-lock text-muted"></i>
                                    </span>
                                </div>
                                <input id="confirm_password" type="password" name="confirm_password" 
                                placeholder="Confirm Password" class="form-control bg-white border-left-0 border-md">
                                <span class="col-md-12 error" id="err_confirm_password"></span>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group col-lg-12 mx-auto mb-0">
                                <input type="submit" class="btn btn-primary btn-block py-2" value="Create your account">
                            </div>

                            <!-- Divider Text -->
                            <div class="form-group col-lg-12 mx-auto d-flex align-items-center my-4">
                                <div class="border-bottom w-100 ml-5"></div>
                                <span class="px-2 small text-muted font-weight-bold text-muted">OR</span>
                                <div class="border-bottom w-100 mr-5"></div>
                            </div>

                            <!-- Social Login -->
                            <div class="form-group col-lg-12 mx-auto">
                                <a href="<?php echo $authUrl; ?>" class="btn btn-danger btn-block py-2">
                                    <i class="fa fa-google mr-2"></i>
                                    <span class="font-weight-bold">Continue with Gmail</span>
                                </a>
                            </div>

                            <!-- Already Registered -->
                            <div class="text-center w-100">
                                <p class="text-muted font-weight-bold">Already Registered? 
                                <a href="<?php echo site_url('home/login'); ?>" class="text-primary ml-2">Login</a></p>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="<?php echo base_url() ?>assets/js/jquery-3.3.1.slim.min.js"></script>
        <script src="<?php echo base_url() ?>assets/js/bootstrap.bundle.min.js"></script>

        <script>
        // For Demo Purpose [Changing input group text on focus]
        $(function () {
            $('input').on('focus', function () {
                var iid = $(this).attr('id');
                $('#err_'+iid).text('');

                $(this).parent().find('.input-group-text').css('border-color', '#80bdff');
            });

            $('input').on('blur', function () {
                $(this).parent().find('.input-group-text').css('border-color', '#ced4da');
            });

            $('input').keypress(function(){
                var iid = $(this).attr('id');
                $('#err_'+iid).text('');
            });
        });

        function validation(){
            var first_name = $('#firstname').val();
            var last_name = $(' #lastname').val();
            var email = $('#email').val();
            var password = $('#password').val();
            var confirm_password = $('#confirm_password').val();

            var error = 0;

            var alpha_regex = /^[a-zA-Z]*$/;
            var email_regex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if(!first_name){
                $('#err_firstname').text('Please enter first name');
                error = 1;
            } else if(!alpha_regex.test(first_name)){
                $('#err_firstname').text('Please enter a valid first name');
                error = 1;
            } 

            if(!last_name){
                $('#err_lastname').text('Please enter last name');
                error = 1;
            } else if(!alpha_regex.test(last_name)){
                $('#err_lastname').text('Please enter a valid last name');
                error = 1;
            } 

            if(!email){
                $('#err_email').text('Please enter email address');
                error = 1;
            } else if(!email_regex.test(email)){
                $('#err_email').text('Please enter a valid email address');
                error = 1;
            } 

            if(!password){
                $('#err_password').text('Please enter password');
                error = 1;
            }

            if(!confirm_password){
                $('#confirm_password').text('Please enter confirm password');
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
    </body>
</html>