<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--  This file has been downloaded from bootdey.com    @bootdey on twitter -->
    <!--  All snippets are MIT license http://bootdey.com/license -->
    <title>Chat Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/css/font-awesome.min.css" rel="stylesheet">

    <style>
        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container bootstrap snippets pt-2">
        <div class="col-md-12 padding-10 mb20">
            <div class="col-md-6" style="float: left; font-size: 32px;">Welcome <font color="#007bff"><?php echo $this->session->userdata('name'); ?></font></a></div>
            <div class="col-md-6" style="text-align: right;display: inline-block;">
                <a class="btn btn-success" href="<?php echo site_url('chat'); ?>"><i class="fa fa-comments mr-2"></i> Chat</a>
                <a class="btn btn-info" href="<?php echo site_url('home/profile'); ?>"><i class="fa fa-user mr-2"></i> Profile</a>
                <a class="btn btn-primary" href="<?php echo site_url('home/logout'); ?>"><i class="fa fa-power-off mr-2"></i> Logout</a>
            </div>
        </div>

        <div class="container rounded bg-white mb-5 pt-5">
            <div class="offset-2 col-md-8">
                <div class="p-3">
                    <form action="<?php echo site_url('chat/saveRoom'); ?>" method="POST" onsubmit="return validation();">
                        <input type="hidden" name="room_id" value="<?php echo isset($room['rm_id'])?$room['rm_id']:0; ?>">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="col-md-12 text-center">Room Creation</h4>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Room Name</label>
                                <input type="text" name="room_name" id="room_name" class="form-control" placeholder="Enter Room Name" value="<?php echo $room['rm_name']; ?>">
                            </div>
                            <span class="col-md-12 error" id="err_room_name"></span>
                        </div>

                        <?php
                        $room_users = array();
                        if ($room['rm_users']) {
                            $room_users = json_decode($room['rm_users'], true);
                        }
                        ?>

                        <div class="row mt-3">
                            <div class="col-md-12"><label class="labels"><b>Add Members</b></label>
                            <table width="100%">
                              <tr>
                                  <td width="45%">
                                    <label>All Users</label><br/> 
                                    <select name="multi_list1" id="select1" multiple="multiple" class="form-control">
                                    <?php
                                    if (!empty($users)) {
                                        foreach ($users as $user) {
                                            if ((!empty($room_users) && !in_array($user['us_id'], $room_users)) || empty($room_users)) {
                                    ?>
                                      <option value="<?php echo $user['us_id']; ?>"><?php echo $user['us_first_name']." ".$user['us_last_name']; ?></option>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </select>
                                  </td>
                                  <td class="text-center" width="10%" style="vertical-align: bottom;"> 
                                      <a href="#" class="btn btn-info" id="add">&gt;&gt;</a> <br/><br/>
                                      <a href="#" class="btn btn-info" id="remove">&lt;&lt;</a>
                                  </td>
                                  <td width="45%">   
                                      <label>Selected Users</label><br/> 
                                    <select name="users[]" id="select2" multiple="multiple" class="form-control">
                                    <?php
                                    if ($room_users) {
                                        foreach ($room_users as $user) {
                                    ?>
                                    <option value="<?php echo $user; ?>" Selected><?php echo $users[$user]['us_first_name']." ".$users[$user]['us_last_name']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                    </select>  
                                  </td>
                              </tr>
                          </table>
                            <span class="col-md-12 error" id="err_select2"></span>
                        </div>
                        <div class="col-md-12 mt-5 text-center">
                          <button class="btn btn-primary profile-button" type="submit">
                          <?php if ($room['rm_id']) { ?>
                          Update Room
                          <?php } else { ?>
                          Create Room
                            <?php } ?>
                          </button>

                          <a class="btn btn-info" href="<?php echo base_url('chat/rooms'); ?>"> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url() ?>assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">  
    $().ready(function() {
        $('input').keypress(function() {
            var iid = $(this).attr('id');
            $('#err_'+iid).text('');
        });

        $('#add').click(function() {
            return !$('#select1 option:selected').remove().appendTo('#select2');  
        });

        $('#remove').click(function() {
            return !$('#select2 option:selected').remove().appendTo('#select1');  
        });
    });

    function validation() {
        var room_name = $('#room_name').val();
        var users = $('#select2 > option').length;

        var error = 0;

        if(!room_name){
            $('#err_room_name').text('Please enter room name');
            error = 1;
        }

        if(!users){
            $('#err_select2').text('Please select atleast one user');
            error = 1;
        }

        if(error){
            return false;
        } else {
            $("#select2 option").prop("selected", true);
            return true;
        }
    }
    </script>
</body>
</html>