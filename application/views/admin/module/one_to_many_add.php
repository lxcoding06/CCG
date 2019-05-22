<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> CI GENERATOR </title>
        <link href="<?php echo $this->config->item('accet_url') ?>css/plugins/select2/select2.min.css" rel="stylesheet">
        <script src="<?php echo $this->config->item('accet_url') ?>js/jquery-2.1.1.min.js"></script>
         <script src="<?php echo $this->config->item('accet_url') ?>js/plugins/slimscroll/jquery.slimscroll.min.js"></script> 
        <script src="<?php echo $this->config->item('accet_url') ?>js/plugins/pace/pace.min.js"></script>
        <link href="<?php echo $this->config->item('accet_url') ?>css/plugins/chosen/chosen.css" rel="stylesheet">
        <link href="<?php echo $this->config->item('accet_url') ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $this->config->item('accet_url') ?>font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="<?php echo $this->config->item('accet_url') ?>css/style.css" rel="stylesheet">
        <link href="<?php echo $this->config->item('accet_url') ?>css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <script src="<?php echo $this->config->item('accet_url') ?>js/bootstrap.min.js"></script>
        <style type="text/css">
        .borderless td, .borderless th, table.borderless {
        border: none !important;
        }
        </style>
    </head>
    <body>

<div id="wrapper">
    <div id="" class="gray-bg dashbard-1">

<!--  BO :heading -->

<!--  EO :heading -->
<div class="row">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox">
            <!-- BO : content  -->
            <div class="col-sm-12 white-bg" style="margin-top: -20px;">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">  </h3>
                    </div><!-- /.box-header -->
                    <br><br>
                    <!-- form start -->
                    <form action="" id="" class="form-horizontal " method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="box-body">
                            <?php if ($this->session->flashdata('message')): ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-close="alert"></button>
                                    <?php echo $this->session->flashdata('message'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="Module_name" class="col-sm-3 control-label"> Tables </label>
                                <div class="col-sm-4">
                                    <select onchange="getField();" class="form-control" id="table_name_new" name="table_name">
                                        <option value="">-Select Table-</option>
                                        <?php
                                        for ($i = 0; $i < count($tables); $i++) {
                                            if(!in_array($tables[$i], array('ci_sessions', 'users'))){
                                            ?>
                                            <option value="<?php echo set_value('table_name', $tables[$i]); ?>"><?php echo $tables[$i] ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="col-sm-5" >
                                    <?php echo form_error('table_name', '<span class="label label-danger">', '</span>') ?>
                                </div>
                            </div> 
                            <div class="form-group" id="result_field">
                                
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12" id="tbl_result">
                                    <?php echo form_error('table_name', '<span class="label label-danger">', '</span>') ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3">
                                </div>
                                <div class="col-sm-6">
                                    <button type="reset" class="btn btn-default">Reset</button>
                                    <button type="submit" disabled="disabled" id="generate-btn" class="btn btn-info" style="display: none;">Submit</button>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->
                <br><br><br><br><br><br><br><br><br><br><br><br><br>
            </div>
            <!-- EO : content  -->
        </div>
    </div>

    <script type="text/javascript">
        function calling()
        {
            var tbl_name = $('#table_name_new').val();
            // parent.$("#one_to_many_table").val(tbl_name);

            var field_name = $('#related_field').val();
            // parent.$("#one_to_many_field").val(field_name);
            parent.one_to_many.push({rel_table:tbl_name, rel_field:field_name})
        }

        function checkAllCheckbox()
        {
            $(".checked").each(function () {
                if ($("#checkAll").prop("checked") == true)
                {
                    if ($(this).prop("checked") == false)
                    {
                        $(this).click();
                    }
                    $("#generate-btn").removeAttr("disabled");
                } else
                {
                    $(this).click();
                    $("#generate-btn").attr("disabled", "disabled");
                }
            });
        }

        function setTitleRadio(field)
        {
            $("#selected_field_radio").val(field);
        }

        function setTitleCheck(field)
        {
            $("#selected_field_check").val(field);
        }

        function setTitle(field, from)
        {
            $("#selected_field").val(field);
        }

        function close_all()
        {
            var selected_radio = $("#selected_radio").val();
            $("#" + selected_radio).hide();
        }

        function add_more_radio(id)
        {
            var rad_id = $("#radio_id").val();
            var selected_radio = $("#selected_radio").val();
            var accet_url = $("#accet_url").val();
            var selected_field = $("#selected_field_radio").val();
            var x = document.getElementById(selected_radio).rows.length;
            x = x + 1;
            $("#" + id).append('<tr id="radio_row_' + x + '"><td><input value="Radio" type="text" name="' + selected_field + '[radios][]"></td><td><img src="' + accet_url + '/img/button-cross_basic_red.png" width="25px" onclick="del_ratio_row(\'radio_row_' + x + '\');"></td></tr>');
        }

        function del_ratio_row(id)
        {
            $("#" + id).remove();
        }

        function select_radio(id, num)
        {
            $("#"+id).closest("tr").find(".top-display").css("display", "none");
            $("#" + id).show();
            $("#selected_radio").val(id);
            $("#radio_id").val(num);
        }
        // checkbox js start

        function close_all_select()
        {
            var selected_check = $("#selected_check").val();
            $("#" + selected_check).hide();
        }

        function add_more_check(id)
        {
            var chk_id = $("#check_id").val();
            var selected_check = $("#selected_check").val();
            var accet_url = $("#accet_url").val();
            var selected_field = $("#selected_field_check").val();
            var x = document.getElementById(selected_check).rows.length;
            x = x + 1;
            $("#" + id).append('<tr id="check_row_' + x + '"><td><input value="Checkbox" type="text" name="' + selected_field + '[checks][]"></td><td><img src="' + accet_url + '/img/button-cross_basic_red.png" width="25px" onclick="del_check_row(\'check_row_' + x + '\');"></td></tr>');
        }

        function del_check_row(id)
        {
            $("#" + id).remove();
        }

        function select_check(id, num)
        {
            $("#"+id).closest("tr").find(".top-display").css("display", "none");
            $("#" + id).show();
            $("#selected_check").val(id);
            $("#check_id").val(num);
        }
        // checkbox js end

        function show_tables(id)
        {
            $("#"+id).closest("tr").find(".top-display").css("display", "none");
            table_name_new = $("#table_name_new").val();
            // $("#"+id+" select option:contains('"+table_name_new+"')").attr("disabled","disabled");

            $("#" + id).show();
            $("#"+id+" select:first").change();
        }

        function show_key_value(dropdown_id, key_id, value_id, field, id)
        {
            var dropdown_tbl = $("#" + dropdown_id).val();
            $.ajax({
                url: '<?php echo base_url() . "admin/module/get_key_value"; ?>',
                type: "post",
                data: "dropdown_tbl=" + dropdown_tbl + '&field=' + field + '&id=' + id+"&<?php echo $this->security->get_csrf_token_name(); ?>="+'<?php echo $this->security->get_csrf_hash(); ?>',
                beforeSend: function () {
                },
                success: function (result) {
                    var arr = result.split("==##==");
                    $('#' + key_id).css('display', 'block');
                    $('#' + value_id).css('display', 'block');

                    $("#" + key_id).html(arr[0]);
                    $("#" + value_id).html(arr[1]);
                },
                error: function (output)
                {
                }
            });
        }


        function get_table_dropdown()
        {
            $.ajax({
                url: '<?php echo base_url() . "admin/module/get_table_dropdown"; ?>',
                type: "post",
                beforeSend: function () {
                },
                success: function (result) {
                    $("#result_one_many_table").append(result);
                },
                error: function (output)
                {
                }
            });
        }

        function add_multi_table(multi_table_id, relation_table_id)
        {
            $.ajax({
                url: '<?php echo base_url() . "admin/module/get_multi_table_html"; ?>',
                type: "post",
                beforeSend: function () {
                },
                success: function (result) {
                    $("#result_multi_table").append(result);
                },
                error: function (output)
                {
                }
            });
        }

        function multi_select_show_key_value(dropdown_id, key_id, value_id, field, id)
        {
            var dropdown_tbl = $("#" + dropdown_id).val();
            $.ajax({
                url: '<?php echo base_url() . "admin/module/multi_select_get_key_value"; ?>',
                type: "post",
                data: "dropdown_tbl=" + dropdown_tbl + '&field=' + field + '&id=' + id+"&<?php echo $this->security->get_csrf_token_name(); ?>="+'<?php echo $this->security->get_csrf_hash(); ?>',
                beforeSend: function () {
                },
                success: function (result) {
                    var arr = result.split("==##==");
                    $('#' + key_id).css('display', 'block');
                    $('#' + value_id).css('display', 'block');

                    $("#" + key_id).html(arr[0]);
                    $("#" + value_id).html(arr[1]);
                },
                error: function (output)
                {
                }
            });
        }

        function show_relation_key_value(dropdown_id, key_id, value_id, field, id)
        {
            var table = $("#table_name_new").val();
            var dropdown_tbl = $("#" + dropdown_id).val();
            $.ajax({
                url: '<?php echo base_url() . "admin/module/multi_relation_get_key_value"; ?>',
                type: "post",
                data: "table="+table+"&dropdown_tbl=" + dropdown_tbl + '&field=' + field + '&id=' + id+"&<?php echo $this->security->get_csrf_token_name(); ?>="+'<?php echo $this->security->get_csrf_hash(); ?>',
                beforeSend: function () {
                },
                success: function (result) {
                    var arr = result.split("==##==");
                    $('#' + key_id).css('display', 'block');
                    $('#' + value_id).css('display', 'block');

                    $("#" + key_id).html(arr[0]);
                    $("#" + value_id).html(arr[1]);
                },
                error: function (output)
                {
                }
            });
        }

        function getField()
        {

            var tbl_name = $('#table_name_new').val();
            parent.$("#one_to_many_table").val(tbl_name);
            var parent_table = parent.$("#table_name_new").val();

            // Get Related Field
            $.ajax({
                url: '<?php echo base_url() . "admin/module/get_key_dropdown"; ?>',
                type: "post",
                data: "parent_table="+parent_table+"&dropdown_tbl=" + tbl_name+"&<?php echo $this->security->get_csrf_token_name(); ?>="+'<?php echo $this->security->get_csrf_hash(); ?>',
                beforeSend: function () {
                },
                success: function (result) {
                    $("#result_field").html(result);
                    $('[data-toggle="popover"]').popover();
                },
                error: function (output)
                {
                }
            });


            // Get All Fields
            $.ajax({
                url: '<?php echo base_url() . "admin/module/get_one_many_fields"; ?>',
                type: "post",
                data: "tbl_name=" + tbl_name+"&<?php echo $this->security->get_csrf_token_name(); ?>="+'<?php echo $this->security->get_csrf_hash(); ?>',
                beforeSend: function () {
                },
                success: function (result) {
                    $("#tbl_result").html(result);
                    try{
                        $(".chosen-select").chosen();
                    } catch(e){
                        
                    }

                    // Input checked after select
                    $("input.checked").on('click', function () {
                        if($("input.checked:checked").length>0){
                            $("#generate-btn").removeAttr("disabled");
                        } else{
                            $("#generate-btn").attr("disabled", "disabled");
                        }
                        if ($(this).prop("checked")) {
                            var temp = $(this).parent().parent();
                            temp.css("background-color", "#FFFFCC");
                            var temp1 = temp.find('.default_input');
                            temp1.click();
                        } else
                        {
                            var temp = $(this).parent().parent();
                            temp.css("background-color", "#fff");
                            var temp1 = temp.find('input[type=radio],input[type=checkbox]');
                            temp1.removeAttr("checked");
                        }
                    });
                    // ./ Input checked after select /.

                    // On radio check select checkbox
                    $("input[type=radio]").click(function () {
                        var check = $(this).parent().parent().find("input[type=checkbox]");
                        check.prop("checked", true);
                        var temp = $(this).parent().parent();
                        temp.css("background-color", "#FFFFCC");

                        if($("input.checked:checked").length>0){
                            $("#generate-btn").removeAttr("disabled");
                        } else{
                            $("#generate-btn").attr("disabled", "disabled");
                        }
                    });
                    // ./ On radio check select checkbox /.
                },
                error: function (output)
                {
                }
            });
        }

        function populate_key_val(element, key, value, type=''){
          var table_name = $(element).val();
          if (type=="") {
            url = '<?php echo base_url() . "admin/module/multi_get_key_value"; ?>';
          }
          else
          {
            url = '<?php echo base_url() . "admin/module/r_get_key_value"; ?>'; 
          }
          $.ajax({
                url: url,
                type: "post",
                data: "table="+table_name,
                beforeSend: function () {
                },
                success: function (result) {
                    var arr = result.split("==##==");
                    $(element).closest("table").find("."+key).html(arr[0]);
                    $(element).closest("table").find("."+value).html(arr[1]);
                },
                error: function (output)
                {
                }
            });
        }

        function delete_multi_table(element)
        {
            $(element).closest("table").remove();
        }

        function setRelatedField()
        {
            var field_name = $('#related_field').val();
            parent.$("#one_to_many_field").val(field_name);
        }
    </script>
</div>
  </body>
</html>