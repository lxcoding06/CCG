<?php
if ($use_username) {

    $username = array(
        'name' => 'username',
        'id' => 'username',
        'value' => set_value('username'),
        'maxlength' => $this->config->item('username_max_length', 'tank_auth'),
        'size' => 30,
    );
}

$email = array(
    'name' => 'email',
    'id' => 'email',
    'value' => set_value('email'),
    'maxlength' => 80,
    'size' => 30,
);

$password = array(
    'name' => 'password',
    'id' => 'password',
    'value' => set_value('password'),
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 30,
);

$confirm_password = array(
    'name' => 'confirm_password',
    'id' => 'confirm_password',
    'value' => set_value('confirm_password'),
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 30,
);

// $captcha = array(
// 	'name'	=> 'captcha',
// 	'id'	=> 'captcha',
// 	'maxlength'	=> 8,
// );
?>

<?php echo form_open($this->uri->uri_string()); ?>

<!DOCTYPE html>

<html>

    <head>



        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">



        <title>EASY RETURN | REGISTRATION</title>



        <link href="<?php echo $this->config->item('accet_url') ?>css/bootstrap.min.css" rel="stylesheet">

        <link href="<?php echo $this->config->item('accet_url') ?>font-awesome/css/font-awesome.css" rel="stylesheet">



        <link href="<?php echo $this->config->item('accet_url') ?>css/animate.css" rel="stylesheet">

        <link href="<?php echo $this->config->item('accet_url') ?>css/style.css" rel="stylesheet">



    </head>



    <body class="gray-bg">

        <!-- Fixed navbar -->

        <nav class="navbar navbar-default navbar-fixed-top">

            <div class="container">

                <center><h1><b>

                            <script language="JavaScript1.2">

                                var message = "EASY RETURN REGISTRATION"

                                var neonbasecolor = "white"

                                var neontextcolor = "gray"

                                var flashspeed = 200  //in milliseconds



                                ///No need to edit below this line/////

                                var n = 0

                                if (document.all || document.getElementById) {

                                    document.write('<font color="' + neonbasecolor + '">')

                                    for (m = 0; m < message.length; m++)
                                        document.write('<span id="neonlight' + m + '">' + message.charAt(m) + '</span>')

                                    document.write('</font>')

                                } else
                                    document.write(message)

                                function crossref(number) {

                                    var crossobj = document.all ? eval("document.all.neonlight" + number) : document.getElementById("neonlight" + number)

                                    return crossobj

                                }

                                function neon() {

                                    //Change all letters to base color

                                    if (n == 0) {

                                        for (m = 0; m < message.length; m++)
                                            //eval("document.all.neonlight"+m).style.color=neonbasecolor

                                            crossref(m).style.color = neonbasecolor

                                    }

                                    //cycle through and change individual letters to neon color

                                    crossref(n).style.color = neontextcolor

                                    if (n < message.length - 1)
                                        n++

                                    else {

                                        n = 0

                                        clearInterval(flashing)

                                        setTimeout("beginneon()", 1500)

                                        return

                                    }

                                }

                                function beginneon() {

                                    if (document.all || document.getElementById)
                                        flashing = setInterval("neon()", flashspeed)

                                }

                                beginneon()

                            </script>

                        </b></h1></center>

            </div>

        </nav>

        <br><br><br><br>

        <div class="middle-box text-center loginscreen animated fadeInDown">

            <div>

                <div>









                    <br><br><br><br>

                </div>



            </div>

        </div>

        <style>

            .err-msg

            {

                color:red;

            }

        </style>

        <!-- Mainly scripts -->

        <script src="<?php echo $this->config->item('accet_url') ?>js/jquery-2.1.1.js"></script>

        <script src="<?php echo $this->config->item('accet_url') ?>js/bootstrap.min.js"></script>



    <center>





        <br><br>



        <div style="width:20%;">

            <form  action="" method="post">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">

                    <input type="text" class="form-control" placeholder="Username" value="<?php echo set_value('username'); ?>" name='username' id='username' required="">

                    <?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']]) ? $errors[$username['name']] : ''; ?>

                </div>

                <div class="form-group">

                    <input type="number" class="form-control" placeholder="Mobile" value="<?php echo set_value('mobile'); ?>" name='mobile' id='mobile' required="">

                    <?php echo form_error($email['mobile']); ?><?php echo isset($errors[$email['mobile']]) ? $errors[$email['mobile']] : ''; ?>

                </div>

                <div class="form-group">

                    <input type="text" class="form-control" placeholder="Email" value="<?php echo set_value('email'); ?>" name='email' id='email' required="">

                    <?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']]) ? $errors[$email['name']] : ''; ?>

                </div>

                <div class="form-group">

                    <input type="password" class="form-control" placeholder="Password" name='password' value="<?php echo set_value('password'); ?>" required="">

                    <?php echo form_error('password', '<span class="err-msg">', '</span>') ?>

                </div>

                <div class="form-group">

                    <input type="password" class="form-control" placeholder="Confirm Password" value="<?php echo set_value('confirm_password'); ?>" name='confirm_password' id='confirm_password' required="">

                    <?php echo form_error('confirm_password', '<span class="err-msg">', '</span>') ?>

                </div>

                <button type="submit" value="Register" name="register" class="btn btn-primary block full-width m-b">Registration</button>   



                <div>if already registered please <a href="<?php echo base_url() ?>">click here</a> to login</div>



            </form>

        </div>

    </center>