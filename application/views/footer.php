<div class="footer">
    <div class="pull-right">
    </div>
    <div>
        <strong>Copyright</strong> Genisys Web Solution
    </div>
</div>
</div>
</div>
<!-- Mainly scripts -->
<script src="<?php echo $this->config->item('accet_url') ?>js/bootstrap.min.js"></script>
<script src="<?php echo $this->config->item('accet_url') ?>js/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo $this->config->item('accet_url') ?>/js/plugins/cropper/cropper.min.js"></script>
<script src="<?php echo $this->config->item('accet_url') ?>js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $this->config->item('accet_url') ?>js/plugins/jeditable/jquery.jeditable.js"></script>
<script src="<?php echo $this->config->item('accet_url') ?>js/plugins/dataTables/datatables.min.js"></script>
<!-- Custom and plugin javascript -->
<script type="text/javascript">
    $(function () {
        var navMain = $("#nav-main");
        navMain.on("click", "a", null, function () {
            navMain.collapse('hide');
        });
    });
</script>
<script src="<?php echo $this->config->item('accet_url') ?>js/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript">
    $(".chosen-select").chosen();

</script>
<script src="<?php echo $this->config->item('accet_url') ?>js/plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
    $(".select2").select2();

</script>
<script type="text/javascript">
    $(function () {
        window.prettyPrint && prettyPrint();
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    });
</script>
<script src="<?php echo $this->config->item('accet_url') ?>js/plugins/clockpicker/clockpicker.js"></script>
<script type="text/javascript">
    $('.clockpicker').clockpicker();
</script>
<script src="<?php echo $this->config->item('accet_url') ?>css/plugins/moment-develop/min/moment-with-locales.js"></script>
<script src="<?php echo $this->config->item('accet_url') ?>js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript">
    $(function () {
        $(".datetimepicker").datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
    });
</script>
</body>
</html>