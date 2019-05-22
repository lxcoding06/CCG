<?php
$old_password = array(
    'name' => 'old_password',
    'id' => 'old_password',
    'value' => set_value('old_password'),
    'size' => 30,
);

$new_password = array(
    'name' => 'new_password',
    'id' => 'new_password',
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 30,
);

$confirm_new_password = array(
    'name' => 'confirm_new_password',
    'id' => 'confirm_new_password',
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<?php $this->load->view('header'); ?>
<!--  BO :heading -->
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2> Change Password </h2>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo base_url() . 'admin/' ?>">Dashboard</a>
            </li>
            <li class="active">
                <strong>Change Password </strong>
            </li>
        </ol>
    </div>
    <div class="col-sm-8">
        <div class="title-action">
        </div>
    </div>
</div>
<!--  EO :heading -->
<div class="row">
    <div class="col-lg-12 row wrapper ">
        <div class="ibox ">
            <div class="ibox-title" >
                <h5>Edit <small></small></h5>
                <div class="ibox-tools">
                </div>
            </div>
            <!-- ............................................................. -->
            <!-- BO : content  -->
            <div class="col-sm-12 white-bg ">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">  </h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form action="" id="" class="form-horizontal " method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">


                        <div class="row container col-sm-4">
                        </div>
                        <div class="row container col-sm-4">
                            <?php
                            if (count($_POST)) {
                                echo '<div class="alert alert-danger">
                      <strong>Error!</strong> Change Password.
                    </div>';
                            }
                            ?>
                            <div class="form-group">
                                <label>Old Password</label>  
                                <input type="password" id="old_password" name="old_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>New Password</label>  
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>  
                                <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" id="change" name="change" class="btn btn-info" value="Change Password">
                            </div>

                        </div>
                    </form>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
            </div>
            <!-- /.box-footer -->
            </form>
        </div>
        <!-- /.box -->
        <br><br><br><br><br><br>
    </div>
    <!-- EO : content  -->
    <!-- ...................................................................... -->
    <?php $this->load->view('footer'); ?>