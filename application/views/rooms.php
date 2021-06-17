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
</head>

<?php
if (!$this->session->userdata('is_logged')) {
    redirect('home');
}
?>

<body>
    <div class="container bootstrap snippets pt-5">
        <div class="col-md-12 padding-10 mb20">
            <div class="col-md-6" style="float: left; font-size: 32px;">Welcome <font color="#007bff"><?php echo $this->session->userdata('name'); ?></font></a></div>
            <div class="col-md-6" style="text-align: right;display: inline-block;">
                <a class="btn btn-success" href="<?php echo site_url('chat'); ?>"><i class="fa fa-comments mr-2"></i> Chat</a>
                <a class="btn btn-info" href="<?php echo site_url('home/profile'); ?>"><i class="fa fa-user mr-2"></i> Profile</a>
                <a class="btn btn-primary" href="<?php echo site_url('home/logout'); ?>"><i class="fa fa-power-off mr-2"></i> Logout</a>
            </div>
        </div>

        <div class="col-md-12">&nbsp;
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

        <div class="row pt-5">
            <div class="col-md-12 text-right">
                <a class="btn btn-info" href="<?php echo base_url('chat/createRoom'); ?>">Create Room</a>
            </div>

            <div class="row d-flex justify-content-between align-items-center mb-3">
                <h4 class="col-md-12 text-center">Room Management</h4>
            </div>

            <table class="table" border="1" cellpadding="5" cellspacing="5">
                <tr>
                    <th class="text-center">SI No.</th>
                    <th class="text-center">Room Name</th>
                    <th class="text-center">Room Users</th>
                    <th class="text-center">Actions</th>
                </tr>

                <?php
                $count = 0;
                if (!empty($rooms)) {
                    foreach ($rooms as $room) {
                        $count++;
                        ?>
                    <tr>
                        <td class="text-center"><?php echo $count; ?></td>
                        <td><?php echo $room['rm_name']; ?></td>
                        <td>
                            <?php
                            $room_users = json_decode($room['rm_users'], true);

                            $userList = array();
                            foreach ($room_users as $user) {
                                $userList[] = $users[$user]['us_first_name']." ".$users[$user]['us_last_name'];
                            }
                            echo implode(', ', $userList);
                            ?>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-info" href="<?php echo site_url('chat/editRoom/').$room['rm_id']; ?>"><i class="fa fa-pencil mr-2"></i> Edit</a>
                            <a class="btn btn-danger" href="<?php echo site_url('chat/deleteRoom/').$room['rm_id']; ?>"><i class="fa fa-trash mr-2"></i> Delete</a>
                            <a class="btn btn-success" data-toggle="modal" data-target="#shareModal" style="color: #fff;cursor: pointer;"><i class="fa fa-share-alt mr-2"></i> Share Link</a>
                        </td>
                    </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td class="text-center" colspan="4"><b>Rooms Not Available</b></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h3 style="color: #007bff;">Copy Link</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 form-group">
                    <input class="form-control" type="text" value="<?php echo site_url('chat/chatRoom/').urlencode(base64_encode($room['rm_id'])); ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url() ?>assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html