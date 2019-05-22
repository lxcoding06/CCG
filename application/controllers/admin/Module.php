<?php

if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Module extends CI_Controller {



    function __construct() {

        parent::__construct();

        ob_start();



        if (version_compare(CI_VERSION, '2.1.0', '<')) {

            $this->load->library('security');

        }

        $this->load->helper('url');

        $this->load->library('ion_auth');

        $this->load->library('form_validation');

    }



    function index() {



        if (!$this->ion_auth->logged_in())

        {

            redirect('/auth/login/');

        } else {

            $this->load->view('admin/module/manage');

        }

    }







    public function add() {



        if (!$this->ion_auth->logged_in())

        {

            redirect('auth/login');

        }

        $data = NULL;

        

        $this->form_validation->set_rules('module_name', 'module Name', 'trim|xss_clean');

        $data['errors'] = array();

        if ($this->form_validation->run() == FALSE) {

            $data['tables'] = $this->db->list_tables();

            $this->load->view('admin/module/add', $data);

        } else {

            $table = $_POST['table_name'];

            $table_new = $_POST['table_name'];

            $tab_fields = $this->db->field_data($table);

            $primary_key = 'id';

            foreach ($tab_fields as $field)

            {

                if($field->primary_key===1){

                    $primary_key = $field->name;

                }

            }

            $cntlr = ucfirst($table);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// Menu Generator Start /////////////////////////

//////////////////////////////////////////////////////////////////////////

            $file = $this->config->item('base_path') . "application/views/header.php";

            $data = file_get_contents($file);

            $temp_strng = 'BO : ' . ucfirst($table);

            if (strpos($data, $temp_strng) !== false) {



            } else {

                $newMenu = '



				<!-- BO : Module -->

                <li <?php if($contr == \'module\'){?>class="active "<?php } ?>  >

                    <a href="javascript:;"><i class="fa fa-users"></i><span class="title">Module</span>

                        <?php if($contr == \'module\'){?><span class="selected"></span><?php } ?>

                      <span class="arrow <?php if($contr == \'module\'){?>open<?php } ?>"></span>

                    </a>

                    <ul class="nav nav-second-level">

                      <li <?php if($contrnew == \'module/add\'){?>class="active "<?php } ?>>

                        <a href="<?php echo base_url()?>admin/module/add"><i class="fa fa-angle-double-right">

                            </i>Add Module</a>

                      </li>

                      <li <?php if($contrnew == \'module/\'){?>class="active"<?php } ?>>

                        <a href="<?php echo base_url()?>admin/module/index"><i class="fa fa-gear"></i>Manage Module</a>

                      </li>                       

                    </ul>

                </li>

                <!--  EO : Module -->



               <!--  @@@@@#####@@@@@ -->



                ';

                $newMenu = str_replace("module", $table, $newMenu);

                $newMenu = str_replace("Module", ucfirst($table), $newMenu);

                $final_data = str_replace("<!--  @@@@@#####@@@@@ -->", $newMenu, $data);

                file_put_contents($file, $final_data);

            }

//////////////////////////////////////////////////////////////////////////

/////////////////////////// Menu Generator End ///////////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// Controller Generator Start ///////////////////

//////////////////////////////////////////////////////////////////////////

            $controller_path = $this->config->item('base_path') . "application/controllers/admin/";

            $file = $controller_path . ucfirst($table) . '.php';

            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);

            $current = "";

            $myfile = fopen($controller_path . "module_files/controller.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/controller.php"));

            fclose($myfile);

            // Write the contents back to the file

            file_put_contents($file, $current);

            $data = file_get_contents($file);

            // do tag replacements or whatever you want

            $data = str_replace("=*=", "$", $data);

            $data = str_replace("=@=", "->", $data);



///////////////////////////////////////////////////////////

//////////////////// Generate Validation Start ////////////

///////////////////////////////////////////////////////////

            $alias = "";

            $select_join = "";

            $validations = "";

            $fields = "";

            $foreign_tables = '';

            $sort_fields_arr = array();

            $status_field = 'status';

            $valds = "";

            $status_field_string="";

            $status_arr =array();

            foreach ($_POST['ischeck'] as $key => $value) {

                $vals_str = "";



                if (isset($_POST['required_' . $value])) {

                    $vals_str = implode('|', $_POST['required_' . $value]);

                } else{

                    $vals_str = 'trim';

                }



                if ($_POST[$value][0] != 'select') {

                    $sort_fields_arr[] = "'" . $value . "'";

                    $sort_fields_arr2[] = "'" . $value . "'";

                }



$all_one_to_many_relations = json_decode($_POST['one_to_many']);



foreach ($all_one_to_many_relations as $key => $value_rel) {

    $one_to_many_table = $value_rel->rel_table;

    $one_to_many_field = $value_rel->rel_field;

    if (isset($one_to_many_table) && !empty($one_to_many_table)) {



        // $alias .= " , count(".$one_to_many_table.".".$one_to_many_field.") as ".$one_to_many_table."_total ";

        // $select_join .= ' $this->db->join("' .$one_to_many_table. '", "' . $table . '.' . $primary_key . ' = ' . $one_to_many_table . '.' .$one_to_many_field. '", "left"); 

              //            $this->db->group_by("' . $table . '.' . $primary_key . '");

                //        ';

    }

}



                if ($_POST[$value][0] == 'select') {

                    $foreign_tables .= '$data["' . $_POST[$value]["selected_table"] . '"]=$this->' . $table . '->getListTable("' . $_POST[$value]["selected_table"] . '");';

                    $alias .= " , " . $_POST[$value]["selected_table"] . "." . $_POST[$value]["value"] . " as $value ";

                    $select_join .= ' $this->db->join("' . $_POST[$value]["selected_table"] . '", "' . $table . '.' . $value . ' = ' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["key"] . '", "left"); ';



                    $sort_fields_arr[] = "'" . $_POST[$value]["selected_table"] . "." . $_POST[$value]["value"] . "'";

                    $sort_fields_arr2[] = "'" . $value . "'";

                }

                $cap_field = ucfirst($value);

                if ($_POST[$value][0] == "status") {

                    $validations .= "@@@this->form_validation->set_rules('$value', '$cap_field Name', 'trim|xss_clean');";

                    $fields .= "\t\t\t@@@saveData['$value'] = set_value('$value');\n";

                    $status_field = $value;

                    $status_arr[] = "'$value'";

                } elseif ($_POST[$value][0] == "image") {

                    $validations .= '$this->form_validation->set_rules("' . $value . '", "' . ucfirst($value) . '", "trim|xss_clean");

         $this->' . $table . '->uploadData($photo_data, "' . $value . '", "photo_path","","gif|jpg|png|jpeg");

	    if(isset($photo_data["photo_err"]) and !empty($photo_data["photo_err"]))

	    {

	     $data["errors"]=$photo_data["photo_err"];

	     $this->form_validation->set_rules("' . $value . '","' . ucfirst($value) . '","' . $vals_str . '");

	    }';



                    $fields .= 'if(isset($photo_data["' . $value . '"]) && !empty($photo_data["' . $value . '"]))

		{

	      $saveData["' . $value . '"] = $photo_data["' . $value . '"];

        }';

                } elseif ($_POST[$value][0] == "checkbox") {



                    $validations .= "\t\t@@@this->form_validation->set_rules('$value', '$cap_field Name', 'xss_clean');\n";

                    $fields .= "\t\t@@@saveData['$value'] = addslashes(implode(', ', \$_POST['$value']));\n";

                } elseif ($_POST[$value][0] == "radio") {

                    $validations .= "\t\t@@@this->form_validation->set_rules('$value', '$cap_field Name', '$vals_str');\n";

                    $fields .= "\t\t\t@@@saveData['$value'] = set_value('$value');\n";

                } else {

                    // For Input & Textarea Field



                    $validations .= "\t\t@@@this->form_validation->set_rules('$value', '$cap_field Name', '$vals_str');\n";



                    $fields .= "\t\t\t@@@saveData['$value'] = set_value('$value');\n";

                }

            }



    $return_multi_selected_id = "";

    if (isset($_POST["multiselect"])) {

        for ($i=0; $i < count($_POST["multiselect"]["table"]); $i++) {

            if ($_POST["multiselect"]["table"][$i]) {

                $rtable = $_POST["multiselect"]["r_table"][$i];

                $field1 = $_POST["multiselect"]["r_main"][$i];

                $field2 = $_POST["multiselect"]["r_multi"][$i];



                $call_multi_add .= "\n\t\t\t@@@this->==table==->multiSelectInsert(\"$rtable\", \"$field2\", @@@insert_id, \"$field1\", @@@_POST['".$_POST["multiselect"]["table"][$i]."']);\n";



                $call_multi_edit .= "\n\t\t\t@@@this->==table==->multiSelectInsert(\"$rtable\", \"$field2\", @@@id, \"$field1\", @@@_POST['".$_POST["multiselect"]["table"][$i]."']);\n";



                $list_tbl .= "\t@@@data['".$_POST["multiselect"]["table"][$i]."']=@@@this->==table==->getList('".$_POST["multiselect"]["table"][$i]."');\n";



                $return_multi_selected_id.= "\n\t\t\t@@@data['selected_".$_POST["multiselect"]["table"][$i]."'] = @@@this->==table==->getSelectedIds(\"$rtable\", @@@id, \"$field1\", \"$field2\");\n";



                $multi_selected_id.= "\n\t\t\t@@@selected_".$_POST["multiselect"]["table"][$i]."_id = @@@this->==table==->getSelectedIds(\"$rtable\", @@@id, \"$field1\", \"$field2\");\n";



                $return_multi_selected_data.= "\n\t\t\t@@@data['selected_".$_POST["multiselect"]["table"][$i]."_data'] = array();

            if (isset(@@@selected_".$_POST["multiselect"]["table"][$i]."_id) && !empty(@@@selected_".$_POST["multiselect"]["table"][$i]."_id)) {

        \n\t\t\t\t@@@data['selected_".$_POST["multiselect"]["table"][$i]."_data'] = @@@this->==table==->getSelectedData('".$_POST["multiselect"]["table"][$i]."', '".$_POST["multiselect"]["value"][$i]."', @@@selected_".$_POST["multiselect"]["table"][$i]."_id);\n\t\t}\n";

            }

        }

    }

///////////////////////////////////////////////////////////

//////////////////// Generate Validation End //////////////

///////////////////////////////////////////////////////////



            $sort_fields_arr = implode(', ', $sort_fields_arr);

            $sort_fields_arr2 = implode(', ', $sort_fields_arr2);

            $data = str_replace('***foreign_table***', $foreign_tables, $data);

            $data = str_replace("==validation==", $validations, $data);

            $data = str_replace("==call_multi_add==", $call_multi_add, $data);

            $data = str_replace("==call_multi_edit==", $call_multi_edit, $data);

            $data = str_replace("==return_multi_selected_id==", $return_multi_selected_id, $data);

            $data = str_replace("==multi_selected_id==", $multi_selected_id, $data);

            $data = str_replace("==return_multi_selected_data==", $return_multi_selected_data, $data);

            $data = str_replace("==fields==", $fields, $data);

            $data = str_replace("{{status_field}}", $status_field, $data);

            $data = str_replace("++sort_fields_arr++", $sort_fields_arr, $data);

            $data = str_replace("++sort_fields_arr2++", $sort_fields_arr2, $data);



            $data = str_replace("==list_tbl==", $list_tbl, $data);

            $data = str_replace("==table==", $table, $data);

            $data = str_replace("controller_name", ucfirst($table), $data);

            $data = str_replace("==primary_key==", $primary_key, $data);

            $data = str_replace("@@@", "$", $data);



            $status_field_string = implode(", ", $status_arr);

            $data = str_replace("==status_field_string==", $status_field_string, $data);

            file_put_contents($file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// Controller Generator End /////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// Model Generator Start ////////////////////////

//////////////////////////////////////////////////////////////////////////



            $model_path = $this->config->item('base_path') . "application/models/admin/";

            $file = $model_path . ucfirst($table) . '_model.php';

            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);



            $current = "";

            $exist_model_path = $this->config->item('base_path') . "application/controllers/admin/";

            $myfile = fopen($exist_model_path . "module_files/model.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($exist_model_path . "module_files/model.php"));

            fclose($myfile);



// Write the contents back to the file

            file_put_contents($file, $current);



            $data = file_get_contents($file);

            $current = str_replace("@@@", "$", $data);

            $current = str_replace("++sort_fields_arr++", $sort_fields_arr, $current);

            $current = str_replace("==table==", ucfirst($table), $current);

            $current = str_replace("==select_alias==", $alias, $current);

            $current = str_replace("==primary_key==", $primary_key, $current);

            $current = str_replace("==select_join==", $select_join, $current);

            file_put_contents($file, $current);

//////////////////////////////////////////////////////////////////////////

/////////////////////////// Model Generator End //////////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Add Generator Start /////////////////////

//////////////////////////////////////////////////////////////////////////



            $ori_path = $this->config->item('base_path') . "application/views/admin/";

            $view_path = $this->config->item('base_path') . "application/views/admin/$table/";

            $add_file = $view_path . 'add.php';

            $edit_file = $view_path . 'edit.php';

            $manage_file = $view_path . 'manage.php';

            if (file_exists($add_file)) {

                $handle = fopen($add_file, 'w') or die('Cannot open file:  ' . $add_file);

            } else {

                mkdir($ori_path . $table, 0700);

                $handle = fopen($add_file, 'w') or die('Cannot open file:  ' . $add_file);

            }



            $current = "";

            $myfile = fopen($controller_path . "module_files/add.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/add.php"));

            fclose($myfile);



            file_put_contents($add_file, $current);

            $data = file_get_contents($add_file);



            $data = str_replace("@@@", "$", $data);

            $data = str_replace("cntlr", $cntlr, $data);



            $formfields = "";

            foreach ($_POST['ischeck'] as $key => $value) {

                if (isset($value) && !empty($value)) {



////////////////////////////////////////////////////

/////////// GENERATE INPUT FIELD FOR ADD ///////////

////////////////////////////////////////////////////

                    if ($_POST[$value][0] == "input") {

                        echo "input field for $value";

                        $formfields .= '





	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

	  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	  <div class="col-sm-4">

	    <input type="text" class="form-control" id="' . $value . '" name="' . $value . '" 

	    

	    value="<?php echo set_value("' . $value . '"); ?>"

	    >

	  </div>

	  <div class="col-sm-5" >

	 

	    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

	  </div>

	</div> 

	<!-- ' . ucfirst($value) . ' End -->





	';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE INPUT FIELD FOR ADD /. /////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE DATE FIELD FOR ADD /. //////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "date") {

                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

	  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	  <div class="col-sm-4">

	    <input type="text" class="form-control span2 datepicker" id="' . $value . '" name="' . $value . '" value="<?php echo set_value("' . $value . '","' . date('Y-m-d') . '"); ?>"	    >

	  </div>

	  <div class="col-sm-5" >

	 

	    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

	  </div>

	</div> 

	<!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE DATE FIELD FOR ADD /. /////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE TIME FIELD FOR ADD /. /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "time") {



                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

	  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	  <div class="col-sm-4 clockpicker" data-autoclose="true">

	    <input type="text" class="form-control" value="09:30" name="' . $value . '">

	  </div>

	  <div class="col-sm-5" >

	    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

	  </div>

	</div>

	<!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE TIME FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE DATETIME FIELD FOR ADD /. //

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "datetime") {



                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

	  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	  <div class="col-sm-4">

	    <input type="text" class="form-control datetimepicker" id="' . $value . '"  name="' . $value . '"/>

	  </div>

	  <div class="col-sm-5" >

	    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

	  </div>

	</div>

	<!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE DATETIME FIELD FOR ADD /. //

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////// GENERATE TEXTAREA FIELD FOR ADD //////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "textarea") {

                        $formfields .= '



				<!-- ' . ucfirst($value) . ' Start -->

			<div class="form-group">

			  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

			  <div class="col-sm-4">

			    <textarea class="form-control" id="' . $value . '" name="' . $value . '"><?php echo set_value("' . $value . '"); ?></textarea>

			  </div>

			  <div class="col-sm-5" >

			 

			    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

			  </div>

			</div> 

			<!-- ' . ucfirst($value) . ' End -->



			';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE TEXTAREA FIELD FOR ADD /. ///////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

////////// GENERATE SELECT FIELD FOR ADD ///////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "select") {



                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

        <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

          <div class="col-md-4">

              <select class="form-control select2" name="' . $value . '" id="' . $value . '">

              <option value="">Select ' . ucfirst($value) . '</option>

      <?php 

      if(isset($' . $_POST[$value]['selected_table'] . ') && !empty($' . $_POST[$value]['selected_table'] . ')):

      foreach($' . $_POST[$value]['selected_table'] . ' as $key => $value): ?>

          <option value="<?php echo $value->' . $_POST[$value]['key'] . '; ?>">

            <?php echo $value->' . $_POST[$value]['value'] . '; ?>

          </option>

      <?php endforeach; ?>

      <?php endif; ?>

      </select>

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

///////// ./ GENERATE SELECT FIELD FOR ADD /. //////

////////////////////////////////////////////////////





////////////////////////////////////////////////////

////////// GENERATE STATUS FIELD FOR ADD ///////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "status") {



                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

        <label class="control-label col-md-3">' . ucfirst($value) . '</label>

         <div class=" col-md-4 switch">

                    <div class="onoffswitch">

     <input type="checkbox" class="onoffswitch-checkbox" checked data-on-label="Yes" data-off-label="No"  name="' . $value . '" value="1" id="' . $value . '" <?php echo set_checkbox("' . $value . '","1")?>/>

    <?php echo form_error("' . $value . '","<span class=err-msg>,</span>")?>

                        <label class="onoffswitch-label" for="' . $value . '">

                            <span class="onoffswitch-switch"></span>

                            <span class="onoffswitch-inner"></span>

                            

                        </label>

                    </div>

                </div>

      </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

///////// ./ GENERATE STATUS FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE RADIO FIELD FOR ADD ///////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "radio") {

                        $formfields .= '



 <!-- ' . ucfirst($value) . ' Start -->

 <div class="form-group">

          <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

          <div class="col-sm-4">';

                        $rad_arr = $_POST[$value]['radios'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

            <span style="margin-right:20px;"><input type="radio" style="width:20px; height:20px;" name="' . $value . '" value="' . $rad_arr[$aaa] . '"> ' . $rad_arr[$aaa] . ' </span>';

                        }

                        $formfields .= '

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

/////// ./ GENERATE RADIO FIELD FOR ADD /. /////////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE CHECKBOX FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "checkbox") {

                        $formfields .= '



 <!-- ' . ucfirst($value) . ' Start -->

 <div class="form-group">

          <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

          <div class="col-sm-4">';

                        $rad_arr = $_POST[$value]['checks'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

            <span style="margin-right:20px;"><input type="checkbox" style="width:20px; height:20px;" name="' . $value . '[]" value="' . $rad_arr[$aaa] . '"> ' . $rad_arr[$aaa] . ' </span>';

                        }

                        $formfields .= '

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

/////////// GENERATE CHECKBOX FIELD FOR ADD ////////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////////// GENERATE IMAGE FIELD FOR ADD /////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "image") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="address" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-6">

      <input type="file" name="' . $value . '" />

      <input type="hidden" name="old_' . $value . '" value="<?php if (isset($' . $value . ') && $' . $value . '!=""){echo $' . $value . '; } ?>" />

        <?php if(isset($' . $value . '_err) && !empty($' . $value . '_err)) 

        { foreach($' . $value . '_err as $key => $error)

        { echo "<div class=\"error-msg\">' . $error . '</div>"; } }?>

      </div>

        <div class="col-sm-3" >

      </div>

    </div>

    <!-- ' . ucfirst($value) . ' End -->



    ';



                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE IMAGE FIELD FOR ADD /. /////

////////////////////////////////////////////////////

                }



            }





if (isset($_POST["multiselect"])) {

    for ($i=0; $i < count($_POST["multiselect"]["table"]); $i++) {

    

////////////////////////////////////////////////////

////////// GENERATE MULTI SELECT FIELD FOR ADD ///////////

////////////////////////////////////////////////////

    if ($_POST["multiselect"]["table"][$i]) {

    $value = $_POST["multiselect"]["table"][$i];

    $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

        <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

          <div class="col-md-4">

              <select class="form-control select2" name="' . $value . '[]" id="' . $value . '" multiple="multiple">

              <option value="">Select ' . ucfirst($value) . '</option>

      <?php 

      if(isset($' . $value . ') && !empty($' . $value . ')):

      foreach($' . $value . ' as $key => $value): ?>

          <option value="<?php echo $value->' . $_POST["multiselect"]['key'][$i] . '; ?>">

            <?php echo $value->' . $_POST["multiselect"]['value'][$i] . '; ?>

          </option>

      <?php endforeach; ?>

      <?php endif; ?>

      </select>

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

}

////////////////////////////////////////////////////

///////// ./ GENERATE MULTI SELECT FIELD FOR ADD /. //////

////////////////////////////////////////////////////



    }

}



            $data = str_replace("==formfields==", $formfields, $data);

            $data = str_replace("singlequote", "'", $data);

            file_put_contents($add_file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Add Generator End ///////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Edit Generator Start ////////////////////

//////////////////////////////////////////////////////////////////////////

            $ori_path = $this->config->item('base_path') . "application/views/admin/";

            $view_path = $this->config->item('base_path') . "application/views/admin/$table/";

            $edit_file = $view_path . 'edit.php';

            if (file_exists($edit_file)) {

                $handle = fopen($edit_file, 'w') or die('Cannot open file:  ' . $edit_file);

            } else {

                mkdir($ori_path . $table, 0700);

                $handle = fopen($edit_file, 'w') or die('Cannot open file:  ' . $edit_file);

            }



            $current = "";

            $myfile = fopen($controller_path . "module_files/edit.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/add.php"));

            fclose($myfile);



            file_put_contents($edit_file, $current);

            $data = file_get_contents($edit_file);



            $data = str_replace("@@@", "$", $data);

            $data = str_replace("cntlr", $cntlr, $data);



            $formfields = "";

            foreach ($_POST['ischeck'] as $key => $value) {

                if (isset($value) && !empty($value)) {



////////////////////////////////////////////////////

/////////// GENERATE Edit Input FIELD FOR ADD //////

////////////////////////////////////////////////////

                    if ($_POST[$value][0] == "input") {



                        $formfields .= '



<!-- ' . ucfirst($value) . ' Start -->

<div class="form-group">

  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

  <div class="col-sm-4">

    <input type="text" class="form-control" id="' . $value . '" name="' . $value . '" 

    

    value="<?php echo set_value("' . $value . '",html_entity_decode($' . $table . '->' . $value . ')); ?>"

    >

  </div>

  <div class="col-sm-5" >

 

    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

  </div>

</div> 

<!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

/////// ./ GENERATE Edit Input FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE Edit Textarea FIELD FOR ADD ///

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "textarea") {

                        $formfields .= '

<!-- ' . ucfirst($value) . ' Start -->



<div class="form-group">

  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

  <div class="col-sm-4">

    <textarea class="form-control" id="' . $value . '" name="' . $value . '"><?php echo set_value("' . $value . '",html_entity_decode($' . $table . '->' . $value . ')); ?></textarea>

  </div>

  <div class="col-sm-5" >

 

    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

  </div>

</div> 



<!-- ' . ucfirst($value) . ' End -->

';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Textarea FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE Edit Date FIELD FOR ADD ///////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "date") {

                        echo "input field for $value";

                        $formfields .= '



<!-- ' . ucfirst($value) . ' Start -->

<div class="form-group">

  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

  <div class="col-sm-4">

    <input type="text" class="form-control span2 datepicker" id="' . $value . '" name="' . $value . '" 

    

    value="<?php echo set_value("' . $value . '",$' . $table . '->' . $value . '); ?>"

    >

  </div>

  <div class="col-sm-5" >

 

    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

  </div>

</div> 

<!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

//////// ./ GENERATE Edit Date FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE TIME FIELD FOR ADD /. /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "time") {



                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

	  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	  <div class="col-sm-4 clockpicker" data-autoclose="true">

	 <input type="text" class="form-control" value="<?php echo set_value("' . $value . '",$' . $table . '->' . $value . '); ?>" name="' . $value . '" id="' . $value . '">

	  </div>

	  <div class="col-sm-5" >

	    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

	  </div>

	</div> 

	<!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE TIME FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE DATETIME FIELD FOR ADD /. //

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "datetime") {



                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

	  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	  <div class="col-sm-4">

	    <input type="text" class="form-control datetimepicker" name="' . $value . '" id="' . $value . '" value="<?php echo set_value("' . $value . '",$' . $table . '->' . $value . '); ?>"/> 

	  </div>

	  <div class="col-sm-5" >

	    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

	  </div>

	</div> 

	<!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE DATETIME FIELD FOR ADD /. //

////////////////////////////////////////////////////

////////////////////////////////////////////////////

/////////// GENERATE Edit Select FIELD FOR ADD /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "select") {



                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	<div class="form-group">

        <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

          <div class="col-md-4">

              <select class="form-control select2" name="' . $value . '" id="' . $value . '">

              <option value="">Select ' . ucfirst($value) . '</option>

      <?php 

      if(isset($' . $_POST[$value]['selected_table'] . ') && !empty($' . $_POST[$value]['selected_table'] . ')):

      foreach($' . $_POST[$value]['selected_table'] . ' as $key => $value): ?>

          <option value="<?php echo $value->' . $_POST[$value]['key'] . '; ?>" <?php echo $value->' . $_POST[$value]['key'] . '==$' . $table . '->' . $value . '?\'selected="selected"\':""; ?>>

            <?php echo $value->' . $_POST[$value]['value'] . '; ?>

          </option>

      <?php endforeach; ?>

      <?php endif; ?>

      </select>

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Select FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE Edit Status FIELD FOR ADD /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "status") {



                        $formfields .= '



	<!-- ' . ucfirst($value) . ' Start -->

	 <div class="form-group">

        <label class="control-label col-md-3">' . $value . '

             

        </label>                    

         <div class=" col-md-4 switch">

                    <div class="onoffswitch">

     <input type="checkbox" class="onoffswitch-checkbox"  data-on-label="Yes" data-off-label="No"  name="' . $value . '" value="1" id="' . $value . '" <?php if(set_value("' . $value . '",$' . $table . '->' . $value . ' == 1)){echo "checked=checked";}?>/>

      <?php echo form_error("' . $value . '","<span class=err-msg>","</span>")?>

                        <label class="onoffswitch-label" for="' . $value . '">

                            <span class="onoffswitch-switch"></span>

                            <span class="onoffswitch-inner"></span>

                        </label>

                    </div>

                </div>

      </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Status FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE Edit Radio FIELD FOR ADD //////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "radio") {



                        $formfields .= '



	 <!-- ' . ucfirst($value) . ' Start -->

	 <div class="form-group">

	          <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

	          <div class="col-sm-4">';

                        $rad_arr = $_POST[$value]['radios'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

	            <span style="margin-right:20px;"><input type="radio" style="width:20px; height:20px;" <?php echo $' . $table . '->' . $value . '=="' . $rad_arr[$aaa] . '"?\'checked="checked"\':""; ?> name="' . $value . '" value="' . $rad_arr[$aaa] . '"> ' . $rad_arr[$aaa] . ' </span>';

                        }

                        $formfields .= '

	        </div>

	    </div>

	      <!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Status FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////// GENERATE Edit Checkbox FIELD FOR ADD /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "checkbox") {



                        $formfields .= '



		<!-- ' . ucfirst($value) . ' Start -->

		<div class="form-group">

		<label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

		<div class="col-sm-4">

		<?php $arr=explode(", ", $' . $table . '->' . $value . ') ?>

		';

                        $rad_arr = $_POST[$value]['checks'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

			<span style="margin-right:20px;"><input type="checkbox" style="width:20px; height:20px;" <?php echo in_array("' . $rad_arr[$aaa] . '", $arr)?\'checked="checked"\':""; ?> name="' . $value . '[]" value="' . $rad_arr[$aaa] . '"> ' . $rad_arr[$aaa] . ' </span>';

                        }



                        $formfields .= '

	</div>

	</div>

	<!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Checkbox FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////// GENERATE Edit Image FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "image") {



    $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="address" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-6">

      <input type="file" name="' . $value . '" />

      <input type="hidden" name="old_' . $value . '" 

      value="<?php if (isset($' . $value . ') && $' . $value . '!=""){echo $' . $value . '; }?>" />  

        <?php if(isset($' . $value . '_err) && !empty($' . $value . '_err)) 

        {foreach($' . $value . '_err as $key => $error)

        {echo "<div class=\"error-msg\">' . $error . '</div>"; } }?>

        <?php if (isset($' . $table . '->' . $value . ') && $' . $table . '->' . $value . '!=""){?>

            <br>

  <img src="<?php echo $this->config->item("photo_url");?><?php echo $' . $table . '->' . $value . '; ?>" alt="pic" width="50" height="50" />

    <?php } ?>

      </div>

        <div class="col-sm-3" >

      </div>

    </div>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

///// ./ GENERATE Edit Image FIELD FOR ADD /. //////

////////////////////////////////////////////////////

                }

            }





if (isset($_POST["multiselect"])) {

    for ($i=0; $i < count($_POST["multiselect"]["table"]); $i++) {



////////////////////////////////////////////////////

////////// GENERATE MULTI SELECT FIELD FOR EDIT /////

////////////////////////////////////////////////////

    if ($_POST["multiselect"]["table"][$i]) {

    $value = $_POST["multiselect"]["table"][$i];

    $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

        <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

          <div class="col-md-4">

              <select class="form-control select2" name="' . $value . '[]" id="' . $value . '" multiple="multiple">

              <option value="">Select ' . ucfirst($value) . '</option>

      <?php 

      if(isset($' . $value . ') && !empty($' . $value . ')):

      foreach($' . $value . ' as $key => $value): ?>

          <option <?php if(in_array($value->' . $_POST["multiselect"]['key'][$i] . ', $selected_'.$_POST["multiselect"]["table"][$i].')){ echo "selected"; } ?> value="<?php echo $value->' . $_POST["multiselect"]['key'][$i] . '; ?>">

            <?php echo $value->' . $_POST["multiselect"]['value'][$i] . '; ?>

          </option>

      <?php endforeach; ?>

      <?php endif; ?>

      </select>

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

}

////////////////////////////////////////////////////

////// ./ GENERATE MULTI SELECT FIELD FOR EDIT /. ///

////////////////////////////////////////////////////



    }

}



            $data = str_replace("==formfields==", $formfields, $data);

            $data = str_replace("singlequote", "'", $data);

            file_put_contents($edit_file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Edit Generator End //////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Generator Start /////////////////////////

//////////////////////////////////////////////////////////////////////////



            $ori_path = $this->config->item('base_path') . "application/views/admin/";

            $view_path = $this->config->item('base_path') . "application/views/admin/$table/";

            $edit_file = $view_path . 'view.php';

            if (file_exists($edit_file)) {

                $handle = fopen($edit_file, 'w') or die('Cannot open file:  ' . $edit_file);

            } else {

                mkdir($ori_path . $table, 0700);

                $handle = fopen($edit_file, 'w') or die('Cannot open file:  ' . $edit_file);

            }



            $current = "";

            $myfile = fopen($controller_path . "module_files/view.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/add.php"));

            fclose($myfile);



            file_put_contents($edit_file, $current);

            $data = file_get_contents($edit_file);



            $data = str_replace("@@@", "$", $data);

            $data = str_replace("cntlr", $cntlr, $data);



            $formfields = "

<table class='table table-bordered' style='width:70%;' align='center'>";

            foreach ($_POST['ischeck'] as $key => $value) {

                if (isset($value) && !empty($value)) {



////////////////////////////////////////////////////

///////// GENERATE Edit Input FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    if ($_POST[$value][0] == "input") {



                        $formfields .= '

	<tr>

	 <td>

	   <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	 </td>

	 <td> 

	   <?php echo set_value("' . $value . '",html_entity_decode($' . $table . '->' . $value . ')); ?>

	 </td>

	</tr>

	';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Input FIELD FOR ADD /. /////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////// GENERATE Edit Date FIELD FOR ADD /////////

////////////////////////////////////////////////////

elseif ($_POST[$value][0] == "date" || $_POST[$value][0] == "time" || $_POST[$value][0] == "datetime") {

    $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

	<tr>

	 <td>

	  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	 </td>

	 <td> 

	   <?php echo set_value("' . $value . '", html_entity_decode($' . $table . '->' . $value . ')); ?>

	 </td>

	</tr>

    <!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Date FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////// GENERATE Edit Textarea FIELD FOR ADD ///////

////////////////////////////////////////////////////

elseif ($_POST[$value][0] == "textarea") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

	<tr>

	 <td>

	  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	 </td>

	 <td> 

	   <?php echo set_value("' . $value . '",  html_entity_decode($' . $table . '->' . $value . ')); ?>

	 </td>

	</tr>

    <!-- ' . ucfirst($value) . ' End -->



	';



                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Textarea FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

//////// GENERATE Edit Select FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "select") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

	<tr>

	 <td>

	  <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

	 </td>

	 <td> 

	   <?php 

	      if(isset($' . $_POST[$value]['selected_table'] . ') && !empty($' . $_POST[$value]['selected_table'] . ')):



	      foreach($' . $_POST[$value]['selected_table'] . ' as $key => $value): 

	       if($value->' . $_POST[$value]['key'] . '==$' . $table . '->' . $value . ')

	             echo $value->' . $_POST[$value]['value'] . ';



	       endforeach; 

	       endif; ?>

	 </td>

	</tr>

    <!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Select FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

//////// GENERATE Edit Status FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "status") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

	<tr>

	 <td>

	  <label class="control-label col-md-3">' . $value . '</label>

	 </td>

	 <td> 

	   <?php if(set_value("' . $value . '",$' . $table . '->' . $value . ' == 1)){echo "Active";}else{ echo "Inactive";}?>

	 </td>

	</tr>

    <!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Status FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

//////// GENERATE Edit Radio FIELD FOR ADD /////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "radio") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

	<tr>

	 <td>

	  <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

	 </td>

	 <td> 

	   ';

                        $rad_arr = $_POST[$value]['radios'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

	   <?php echo $' . $table . '->' . $value . '=="' . $rad_arr[$aaa] . '"?\'' . $rad_arr[$aaa] . '\':""; ?>';

                        }

                        $formfields .= '

	 </td>

	</tr>

    <!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Radio FIELD FOR ADD /. ///////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

////// GENERATE Edit Checkbox FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "checkbox") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

	<tr>

	 <td>

	  <label class="col-sm-3 control-label">' . ucfirst($value) . '</label>

	 </td>

	 <td> 

	   <?php $arr=explode(", ", $' . $table . '->' . $value . ') ?>

	          ';

                        $rad_arr = $_POST[$value]['checks'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

	            <span style="margin-left:5px;"><?php echo in_array("' . $rad_arr[$aaa] . '", $arr)?\'' . $rad_arr[$aaa] . ', \':""; ?></span>';

                        }

                        $formfields .= '

	 </td>

	</tr>

    <!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Checkbox FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

//////// GENERATE Edit Image FIELD FOR ADD /////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "image") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

	<tr>

	 <td>

	  <label for="address" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

	 </td>

	 <td>

	 <?php if (isset($' . $table . '->' . $value . ') && $' . $table . '->' . $value . '!=""){?>

	            <br>

	    <img src="<?php echo $this->config->item("photo_url");?><?php echo $' . $table . '->' . $value . '; ?>" alt="pic" width="50" height="50" />

	    <?php } ?>

	 </td>

	</tr>

    <!-- ' . ucfirst($value) . ' End -->



	';

                    }

////////////////////////////////////////////////////

///// ./ GENERATE Edit Image FIELD FOR ADD /. //////

////////////////////////////////////////////////////

                }

            }



    if (isset($_POST["multiselect"])) {

        for ($i=0; $i < count($_POST["multiselect"]["table"]); $i++) {

            if ($_POST["multiselect"]["table"][$i]) {

                $rtable = $_POST["multiselect"]["r_table"][$i];

                $field1 = $_POST["multiselect"]["r_main"][$i];

                $field2 = $_POST["multiselect"]["r_multi"][$i];

                $call_multi_add .= "\n\t@@@this->==table==->multiSelectInsert(\"$rtable\", \"$field2\", @@@insert_id, \"$field1\", @@@_POST['".$_POST["multiselect"]["table"][$i]."']);\n";

                $call_multi_edit .= "\n\t@@@this->==table==->multiSelectInsert(\"$rtable\", \"$field2\", @@@id, \"$field1\", @@@_POST['".$_POST["multiselect"]["table"][$i]."']);\n";

                $list_tbl .= "\n\t@@@data['".$_POST["multiselect"]["table"][$i]."']=@@@this->==table==->getList('".$_POST["multiselect"]["table"][$i]."');\n";

                $return_multi_selected_id.= "\n\t@@@data['selected_".$_POST["multiselect"]["table"][$i]."'] = @@@this->==table==->getSelectedIds(\"$rtable\", @@@id, \"$field1\", \"$field2\");\n";



                $formfields .= '



                <!-- ' . ucfirst($_POST["multiselect"]["table"][$i]) . ' Start -->

                <tr>

                 <td>

                  <label for="address" class="col-sm-3 control-label"> ' . ucfirst($rtable) . ' </label>

                 </td>

                 <td>

                 <?php echo implode(", ", $selected_'.$_POST["multiselect"]["table"][$i].'_data); ?>

                 </td>

                </tr>

                <!-- ' . ucfirst($_POST["multiselect"]["table"][$i]) . ' End -->



                ';



            }

        }

    }



            $formfields .= '<tr><td colspan="2"><a type="reset" class="btn btn-info pull-right" onclick="history.back()">Back</a></td></tr></table>';

            $data = str_replace("==formfields==", $formfields, $data);

            $data = str_replace("singlequote", "'", $data);

// $data = str_replace("==backscript==", "", $data);



            file_put_contents($edit_file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Generator End ///////////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Manage Generator Start //////////////////

//////////////////////////////////////////////////////////////////////////



            $ori_path = $this->config->item('base_path') . "application/views/admin/";

            $view_path = $this->config->item('base_path') . "application/views/admin/$table/";

            $manage_file = $view_path . 'manage.php';

            if (file_exists($manage_file)) {

                $handle = fopen($manage_file, 'w') or die('Cannot open file:  ' . $manage_file);

            } else {

                mkdir($ori_path . $table, 0700);

                $handle = fopen($manage_file, 'w') or die('Cannot open file:  ' . $manage_file);

            }



            $current = "";

            $myfile = fopen($controller_path . "module_files/manage.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/manage.php"));

            fclose($myfile);



            file_put_contents($manage_file, $current);

            $data = file_get_contents($manage_file);



            $data = str_replace("@@@", "$", $data);

            $data = str_replace("cntlr", $cntlr, $data);



            $option_fields = "";

            $tableheadrows = '<?php $sortSym=isset($_GET["order"]) && $_GET["order"]=="asc" ? "up" : "down"; ?>';

            $tabledatarows = "<th><input name='input' id='del' onclick=\"callme('show')\"  type='checkbox' class='del' value='<?php echo @@@value->".$primary_key."; ?>'/></th>

                              

            <th><?php if(!empty(@@@value->".$primary_key.")){ echo @@@count; @@@count++; }?></th>";

            foreach ($_POST['ischeck'] as $key => $value) {

                if (isset($value) && !empty($value)) {



                    if ($_POST[$value][0] == 'select') {



                        $tableheadrows .= '

				<?php

				 $symbol = isset($_GET["sortBy"]) && $_GET["sortBy"]=="' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["value"] . '"?"<i class=\'fa fa-sort-$sortSym\' aria-hidden=\'true\'></i>": "<i class=\'fa fa-sort\' aria-hidden=\'true\'></i>"; ?>



				<th> <a href="<?php echo $fields_links["' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["value"] . '"]; ?>" class="link_css"> ' . ucfirst($value) . ' <?php echo $symbol ?></a></th>

   						';



                        $option_fields .= '<option value="' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["value"] . '" <?php echo $searchBy=="' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["value"] . '"?\'selected="selected"\':""; ?>>' . ucfirst($value) . '</option>';

                    } else {

                        $tableheadrows .= '

				<?php $symbol = isset($_GET["sortBy"]) && $_GET["sortBy"]=="' . $value . '"?"<i class=\'fa fa-sort-$sortSym\' aria-hidden=\'true\'></i>": "<i class=\'fa fa-sort\' aria-hidden=\'true\'></i>"; ?>

				<th> <a href="<?php echo $fields_links["' . $value . '"]; ?>" class="link_css"> ' . ucfirst($value) . ' <?php echo $symbol ?></a></th>

						';



                        $option_fields .= '<option value="' . $value . '" <?php echo $searchBy=="' . $value . '"?\'selected="selected"\':""; ?>>' . ucfirst($value) . '</option>';

                    }





                    if ($_POST[$value][0] == 'status') {

                        $tabledatarows .= '<th><a href="<?php echo base_url()?>admin/' . $table . '/status/' . $value . '/<?php echo @@@value->'.$primary_key.'."?redirect=".current_url()."?".urlencode($_SERVER["QUERY_STRING"]); ?>">

                        <?php if(!empty(@@@value->' . $value . ') and @@@value->' . $value . '==1 )

                        { echo "Active"; }else{ echo "Inactive";}?>

                       </a></th>

                ';

                    } elseif ($_POST[$value][0] == 'image') {

                        $tabledatarows .= '<th><?php if(!empty(@@@value->' . $value . ')){ ?> 

                        <img src="<?php echo $this->config->item(\'photo_url\');?><?php echo @@@value->' . $value . '; ?>" alt="pic" width="50" height="50" />

                         <?php }?></th>';

                    } else {

                        $tabledatarows .= '<th><?php if(!empty(@@@value->' . $value . ')){ echo @@@value->' . $value . '; }?></th>

                ';

                    }

                }



                //echo $_POST[$value][0]."<br>";

            }



            $all_one_to_many_relations = json_decode($_POST['one_to_many']);

            foreach ($all_one_to_many_relations as $key => $value) {

                $one_to_many_table = $value->rel_table;

                $one_to_many_field = $value->rel_field;



                 if (isset($one_to_many_table) && !empty($one_to_many_table)) {

                    $tableheadrows .= '<th class="action-width">'.$one_to_many_table.'</th>';

                    $tabledatarows .= '<th class="action-width">

                       <a href="<?php echo base_url()?>admin/' .$one_to_many_table.'/index/'.$one_to_many_field.'/<?php echo @@@value->'.$primary_key.'; ?>/1" title="View">

                        <span class="btn btn-info " >

                            ' . $one_to_many_table . '

                        </span>

                       </a>

                       </th>';

                }

            }



            $tabledatarows .= '<th class="action-width">

		   <a href="<?php echo base_url()?>admin/' . $table_new . '/view/<?php echo @@@value->'.$primary_key.'; ?>" title="View">

            <span class="btn btn-info " ><i class="fa fa-eye"></i></span>

           </a>

           <a href="<?php echo base_url()?>admin/' . $table_new . '/edit/<?php echo @@@value->'.$primary_key.'; ?>" title="Edit">

            <span class="btn btn-info " ><i class="fa fa-edit"></i></span>

           </a>

           <a  title="Delete" data-toggle="modal" data-target="#commonDelete" onclick="set_common_delete(\'<?php echo @@@value->'.$primary_key.'; ?>\',\'' . $table_new . '\');">

           <span class="btn btn-info " ><i class="fa fa-trash-o "></i></span>

           </a>

            </th>';



           



            $row_id = " id=\"hide<?php echo @@@value->".$primary_key."; ?>\" ";

            $data = str_replace("==tableheadrows==", $tableheadrows, $data);

            $data = str_replace("==tabledatarows==", $tabledatarows, $data);

            $data = str_replace("==searchoptions==", $option_fields, $data);

            $data = str_replace("==table==", $table_new, $data);

            $data = str_replace("++id++", $row_id, $data);

            $data = str_replace("@@@", "$", $data);

            $data = str_replace("singlequote", "'", $data);



            file_put_contents($manage_file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Manage Generator End ////////////////////

//////////////////////////////////////////////////////////////////////////





            $this->session->set_flashdata('message', 'Module created Successfully!');

            redirect('admin/module/add');

        }

    }











public function one_to_many_add() {



        if (!$this->ion_auth->logged_in())

        {

            redirect('/auth/login/');

        }

        $data = NULL;



        $this->form_validation->set_rules('module_name', 'module Name', 'trim|xss_clean');

        $data['errors'] = array();

        if ($this->form_validation->run() == FALSE) {

            $data['tables'] = $this->db->list_tables();

            $this->load->view('admin/module/one_to_many_add', $data);

        } else {

            $table = $_POST['table_name'];

            $table_new = $_POST['table_name'];

            $tab_fields = $this->db->field_data($table);

            $primary_key = 'id';

            foreach ($tab_fields as $field)

            {

                if($field->primary_key===1){

                    $primary_key = $field->name;

                }

            }

            $cntlr = ucfirst($table);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// Menu Generator Start /////////////////////////

//////////////////////////////////////////////////////////////////////////

            $file = $this->config->item('base_path') . "application/views/header.php";

            $data = file_get_contents($file);

            $temp_strng = 'BO : ' . ucfirst($table);

            if (strpos($data, $temp_strng) !== false) {

                

            } else {

                $newMenu = '



                <!-- BO : Module -->

                <li <?php if($contr == \'module\'){?>class="active "<?php } ?>  >

                    <a href="javascript:;"><i class="fa fa-users"></i><span class="title">Module</span>

                        <?php if($contr == \'module\'){?><span class="selected"></span><?php } ?>

                      <span class="arrow <?php if($contr == \'module\'){?>open<?php } ?>"></span>

                    </a>

                    <ul class="nav nav-second-level">

                      <li <?php if($contrnew == \'module/add\'){?>class="active "<?php } ?>>

                        <a href="<?php echo base_url()?>admin/module/add"><i class="fa fa-angle-double-right">

                            </i>Add Module</a>

                      </li>

                      <li <?php if($contrnew == \'module/\'){?>class="active"<?php } ?>>

                        <a href="<?php echo base_url()?>admin/module/index"><i class="fa fa-gear"></i>Manage Module</a>

                      </li>                       

                    </ul>

                </li>

                <!--  EO : Module -->



               <!--  @@@@@#####@@@@@ -->



                ';

                // $newMenu = str_replace("module", $table, $newMenu);

                // $newMenu = str_replace("Module", ucfirst($table), $newMenu);

                // $final_data = str_replace("<!--  @@@@@#####@@@@@ -->", $newMenu, $data);

                // file_put_contents($file, $final_data);

            }

//////////////////////////////////////////////////////////////////////////

/////////////////////////// Menu Generator End ///////////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// Controller Generator Start ///////////////////

//////////////////////////////////////////////////////////////////////////

            $controller_path = $this->config->item('base_path') . "application/controllers/admin/";

            $file = $controller_path . ucfirst($table) . '.php';

            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);

            $current = "";

            $myfile = fopen($controller_path . "module_files/controller_one_to_many.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/controller_one_to_many.php"));

            fclose($myfile);

            // Write the contents back to the file

            file_put_contents($file, $current);

            $data = file_get_contents($file);

            // do tag replacements or whatever you want

            $data = str_replace("=*=", "$", $data);

            $data = str_replace("=@=", "->", $data);



///////////////////////////////////////////////////////////

//////////////////// Generate Validation Start ////////////

///////////////////////////////////////////////////////////

            $alias = "";

            $select_join = "";

            $validations = "";

            $fields = "";

            $foreign_tables = '';

            $sort_fields_arr = array();

            $status_field = 'status';

            $valds = "";

            $status_field_string="";

            $status_arr =array();

            foreach ($_POST['ischeck'] as $key => $value) {

                $vals_str = "";



                if (isset($_POST['required_' . $value])) {

                    $vals_str = implode('|', $_POST['required_' . $value]);

                } else{

                    $vals_str = 'trim';

                }



                if ($_POST[$value][0] != 'select') {

                    $sort_fields_arr[] = "'" . $value . "'";

                    $sort_fields_arr2[] = "'" . $value . "'";

                }



                



                if ($_POST[$value][0] == 'select') {

                    $foreign_tables .= '$data["' . $_POST[$value]["selected_table"] . '"]=$this->' . $table . '->getListTable("' . $_POST[$value]["selected_table"] . '");';

                    $alias .= " , " . $_POST[$value]["selected_table"] . "." . $_POST[$value]["value"] . " as $value ";

                    $select_join .= ' $this->db->join("' . $_POST[$value]["selected_table"] . '", "' . $table . '.' . $value . ' = ' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["key"] . '", "left"); ';



                    $sort_fields_arr[] = "'" . $_POST[$value]["selected_table"] . "." . $_POST[$value]["value"] . "'";

                    $sort_fields_arr2[] = "'" . $value . "'";

                }

                $cap_field = ucfirst($value);

                if ($_POST[$value][0] == "status") {

                    $validations .= "@@@this->form_validation->set_rules('$value', '$cap_field Name', 'trim|xss_clean');";

                    $fields .= "\t\t\t@@@saveData['$value'] = set_value('$value');\n";

                    $status_field = $value;

                    $status_arr[] = "'$value'";

                } elseif ($_POST[$value][0] == "image") {

                    $validations .= '$this->form_validation->set_rules("' . $value . '", "' . ucfirst($value) . '", "trim|xss_clean");

         $this->' . $table . '->uploadData($photo_data, "' . $value . '", "photo_path","","gif|jpg|png|jpeg");

        if(isset($photo_data["photo_err"]) and !empty($photo_data["photo_err"]))

        {

         $data["errors"]=$photo_data["photo_err"];

         $this->form_validation->set_rules("' . $value . '","' . ucfirst($value) . '","' . $vals_str . '");

        }';



                    $fields .= 'if(isset($photo_data["' . $value . '"]) && !empty($photo_data["' . $value . '"]))

        {

          $saveData["' . $value . '"] = $photo_data["' . $value . '"];

        }';

                } elseif ($_POST[$value][0] == "checkbox") {



                    $validations .= "\t\t@@@this->form_validation->set_rules('$value', '$cap_field Name', 'xss_clean');\n";

                    $fields .= "\t\t@@@saveData['$value'] = addslashes(implode(', ', \$_POST['$value']));\n";

                } elseif ($_POST[$value][0] == "radio") {

                    $validations .= "\t\t@@@this->form_validation->set_rules('$value', '$cap_field Name', '$vals_str');\n";

                    $fields .= "\t\t\t@@@saveData['$value'] = set_value('$value');\n";

                } else {

                    // For Input & Textarea Field

                    $validations .= "\t\t@@@this->form_validation->set_rules('$value', '$cap_field Name', '$vals_str');\n";



                    $fields .= "\t\t\t@@@saveData['$value'] = set_value('$value');\n";

                }

            }



    $return_multi_selected_id = "";

    if (isset($_POST["multiselect"])) {

        for ($i=0; $i < count($_POST["multiselect"]["table"]); $i++) {

            if ($_POST["multiselect"]["table"][$i]) {

                $rtable = $_POST["multiselect"]["r_table"][$i];

                $field1 = $_POST["multiselect"]["r_main"][$i];

                $field2 = $_POST["multiselect"]["r_multi"][$i];



                $call_multi_add .= "\n\t\t\t@@@this->==table==->multiSelectInsert(\"$rtable\", \"$field2\", @@@insert_id, \"$field1\", @@@_POST['".$_POST["multiselect"]["table"][$i]."']);\n";



                $call_multi_edit .= "\n\t\t\t@@@this->==table==->multiSelectInsert(\"$rtable\", \"$field2\", @@@id, \"$field1\", @@@_POST['".$_POST["multiselect"]["table"][$i]."']);\n";



                $list_tbl .= "\t@@@data['".$_POST["multiselect"]["table"][$i]."']=@@@this->==table==->getList('".$_POST["multiselect"]["table"][$i]."');\n";



                $return_multi_selected_id.= "\n\t\t\t@@@data['selected_".$_POST["multiselect"]["table"][$i]."'] = @@@this->==table==->getSelectedIds(\"$rtable\", @@@id, \"$field1\", \"$field2\");\n";



                $multi_selected_id.= "\n\t\t\t@@@selected_".$_POST["multiselect"]["table"][$i]."_id = @@@this->==table==->getSelectedIds(\"$rtable\", @@@id, \"$field1\", \"$field2\");\n";



                $return_multi_selected_data.= "\n\t\t\t@@@data['selected_".$_POST["multiselect"]["table"][$i]."_data'] = array();

            if (isset(@@@selected_".$_POST["multiselect"]["table"][$i]."_id) && !empty(@@@selected_".$_POST["multiselect"]["table"][$i]."_id)) {

        \n\t\t\t\t@@@data['selected_".$_POST["multiselect"]["table"][$i]."_data'] = @@@this->==table==->getSelectedData('".$_POST["multiselect"]["table"][$i]."', '".$_POST["multiselect"]["value"][$i]."', @@@selected_".$_POST["multiselect"]["table"][$i]."_id);\n\t\t}\n";

            }

        }

    }

///////////////////////////////////////////////////////////

//////////////////// Generate Validation End //////////////

///////////////////////////////////////////////////////////





        



            $sort_fields_arr = implode(', ', $sort_fields_arr);

            $sort_fields_arr2 = implode(', ', $sort_fields_arr2);

            $data = str_replace('***foreign_table***', $foreign_tables, $data);

            $data = str_replace("==validation==", $validations, $data);

            $data = str_replace("==call_multi_add==", $call_multi_add, $data);

            $data = str_replace("==call_multi_edit==", $call_multi_edit, $data);

            $data = str_replace("==return_multi_selected_id==", $return_multi_selected_id, $data);

            $data = str_replace("==multi_selected_id==", $multi_selected_id, $data);

            $data = str_replace("==return_multi_selected_data==", $return_multi_selected_data, $data);

            $data = str_replace("==fields==", $fields, $data);

            $data = str_replace("{{status_field}}", $status_field, $data);

            $data = str_replace("++sort_fields_arr++", $sort_fields_arr, $data);

            $data = str_replace("++sort_fields_arr2++", $sort_fields_arr2, $data);



            $data = str_replace("==list_tbl==", $list_tbl, $data);

            $data = str_replace("==table==", $table, $data);

            $data = str_replace("controller_name", ucfirst($table), $data);

            $data = str_replace("==primary_key==", $primary_key, $data);

            $data = str_replace("@@@", "$", $data);



            $status_field_string = implode(", ", $status_arr);

            $data = str_replace("==status_field_string==", $status_field_string, $data);

            file_put_contents($file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// Controller Generator End /////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// Model Generator Start ////////////////////////

//////////////////////////////////////////////////////////////////////////



            $model_path = $this->config->item('base_path') . "application/models/admin/";

            $file = $model_path . ucfirst($table) . '_model.php';

            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);



            $current = "";

            $exist_model_path = $this->config->item('base_path') . "application/controllers/admin/";

            $myfile = fopen($exist_model_path . "module_files/model_one_to_many.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($exist_model_path . "module_files/model_one_to_many.php"));

            fclose($myfile);



// Write the contents back to the file

            file_put_contents($file, $current);



            $data = file_get_contents($file);

            $current = str_replace("@@@", "$", $data);

            $current = str_replace("++sort_fields_arr++", $sort_fields_arr, $current);

            $current = str_replace("==table==", ucfirst($table), $current);

            $current = str_replace("==select_alias==", $alias, $current);

            $current = str_replace("==primary_key==", $primary_key, $current);

            $current = str_replace("==select_join==", $select_join, $current);

            file_put_contents($file, $current);

//////////////////////////////////////////////////////////////////////////

/////////////////////////// Model Generator End //////////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Add Generator Start /////////////////////

//////////////////////////////////////////////////////////////////////////



            $ori_path = $this->config->item('base_path') . "application/views/admin/";

            $view_path = $this->config->item('base_path') . "application/views/admin/$table/";

            $add_file = $view_path . 'add.php';

            $edit_file = $view_path . 'edit.php';

            $manage_file = $view_path . 'manage.php';

            if (file_exists($add_file)) {

                $handle = fopen($add_file, 'w') or die('Cannot open file:  ' . $add_file);

            } else {

                mkdir($ori_path . $table, 0700);

                $handle = fopen($add_file, 'w') or die('Cannot open file:  ' . $add_file);

            }



            $current = "";

            $myfile = fopen($controller_path . "module_files/add_one_to_many.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/add_one_to_many.php"));

            fclose($myfile);



            file_put_contents($add_file, $current);

            $data = file_get_contents($add_file);



            $data = str_replace("@@@", "$", $data);

            $data = str_replace("cntlr", $cntlr, $data);



            $formfields = "";

            foreach ($_POST['ischeck'] as $key => $value) {

                if (isset($value) && !empty($value)) {



////////////////////////////////////////////////////

/////////// GENERATE INPUT FIELD FOR ADD ///////////

////////////////////////////////////////////////////

                    if ($_POST[$value][0] == "input") {

                        echo "input field for $value";

                        $formfields .= '





    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-4">

        <input type="text" class="form-control" id="' . $value . '" name="' . $value . '" 

        

        value="<?php echo set_value("' . $value . '"); ?>"

        >

      </div>

      <div class="col-sm-5" >

     

        <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

      </div>

    </div> 

    <!-- ' . ucfirst($value) . ' End -->





    ';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE INPUT FIELD FOR ADD /. /////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE DATE FIELD FOR ADD /. //////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "date") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-4">

        <input type="text" class="form-control span2 datepicker" id="' . $value . '" name="' . $value . '" value="<?php echo set_value("' . $value . '","' . date('Y-m-d') . '"); ?>"       >

      </div>

      <div class="col-sm-5" >

     

        <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

      </div>

    </div> 

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE DATE FIELD FOR ADD /. /////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE TIME FIELD FOR ADD /. /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "time") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-4 clockpicker" data-autoclose="true">

        <input type="text" class="form-control" value="09:30" name="' . $value . '">

      </div>

      <div class="col-sm-5" >

        <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

      </div>

    </div>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE TIME FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE DATETIME FIELD FOR ADD /. //

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "datetime") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-4">

        <input type="text" class="form-control datetimepicker" id="' . $value . '"  name="' . $value . '"/>

      </div>

      <div class="col-sm-5" >

        <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

      </div>

    </div>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE DATETIME FIELD FOR ADD /. //

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////// GENERATE TEXTAREA FIELD FOR ADD //////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "textarea") {

                        $formfields .= '



                <!-- ' . ucfirst($value) . ' Start -->

            <div class="form-group">

              <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

              <div class="col-sm-4">

                <textarea class="form-control" id="' . $value . '" name="' . $value . '"><?php echo set_value("' . $value . '"); ?></textarea>

              </div>

              <div class="col-sm-5" >

             

                <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

              </div>

            </div> 

            <!-- ' . ucfirst($value) . ' End -->



            ';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE TEXTAREA FIELD FOR ADD /. ///////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

////////// GENERATE SELECT FIELD FOR ADD ///////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "select") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

        <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

          <div class="col-md-4">

              <select class="form-control select2" name="' . $value . '" id="' . $value . '">

              <option value="">Select ' . ucfirst($value) . '</option>

      <?php 

      if(isset($' . $_POST[$value]['selected_table'] . ') && !empty($' . $_POST[$value]['selected_table'] . ')):

      foreach($' . $_POST[$value]['selected_table'] . ' as $key => $value): ?>

          <option value="<?php echo $value->' . $_POST[$value]['key'] . '; ?>">

            <?php echo $value->' . $_POST[$value]['value'] . '; ?>

          </option>

      <?php endforeach; ?>

      <?php endif; ?>

      </select>

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

///////// ./ GENERATE SELECT FIELD FOR ADD /. //////

////////////////////////////////////////////////////





////////////////////////////////////////////////////

////////// GENERATE STATUS FIELD FOR ADD ///////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "status") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

        <label class="control-label col-md-3">' . ucfirst($value) . '</label>

         <div class=" col-md-4 switch">

                    <div class="onoffswitch">

     <input type="checkbox" class="onoffswitch-checkbox" checked data-on-label="Yes" data-off-label="No"  name="' . $value . '" value="1" id="' . $value . '" <?php echo set_checkbox("' . $value . '","1")?>/>

    <?php echo form_error("' . $value . '","<span class=err-msg>,</span>")?>

                        <label class="onoffswitch-label" for="' . $value . '">

                            <span class="onoffswitch-switch"></span>

                            <span class="onoffswitch-inner"></span>

                            

                        </label>

                    </div>

                </div>

      </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

///////// ./ GENERATE STATUS FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE RADIO FIELD FOR ADD ///////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "radio") {

                        $formfields .= '



 <!-- ' . ucfirst($value) . ' Start -->

 <div class="form-group">

          <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

          <div class="col-sm-4">';

                        $rad_arr = $_POST[$value]['radios'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

            <span style="margin-right:20px;"><input type="radio" style="width:20px; height:20px;" name="' . $value . '" value="' . $rad_arr[$aaa] . '"> ' . $rad_arr[$aaa] . ' </span>';

                        }

                        $formfields .= '

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

/////// ./ GENERATE RADIO FIELD FOR ADD /. /////////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE CHECKBOX FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "checkbox") {

                        $formfields .= '



 <!-- ' . ucfirst($value) . ' Start -->

 <div class="form-group">

          <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

          <div class="col-sm-4">';

                        $rad_arr = $_POST[$value]['checks'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

            <span style="margin-right:20px;"><input type="checkbox" style="width:20px; height:20px;" name="' . $value . '[]" value="' . $rad_arr[$aaa] . '"> ' . $rad_arr[$aaa] . ' </span>';

                        }

                        $formfields .= '

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

/////////// GENERATE CHECKBOX FIELD FOR ADD ////////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////////// GENERATE IMAGE FIELD FOR ADD /////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "image") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="address" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-6">

      <input type="file" name="' . $value . '" />

      <input type="hidden" name="old_' . $value . '" value="<?php if (isset($' . $value . ') && $' . $value . '!=""){echo $' . $value . '; } ?>" />

        <?php if(isset($' . $value . '_err) && !empty($' . $value . '_err)) 

        { foreach($' . $value . '_err as $key => $error)

        { echo "<div class=\"error-msg\">' . $error . '</div>"; } }?>

      </div>

        <div class="col-sm-3" >

      </div>

    </div>

    <!-- ' . ucfirst($value) . ' End -->



    ';



                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE IMAGE FIELD FOR ADD /. /////

////////////////////////////////////////////////////

                }



            }





if (isset($_POST["multiselect"])) {

    for ($i=0; $i < count($_POST["multiselect"]["table"]); $i++) {

    

////////////////////////////////////////////////////

////////// GENERATE MULTI SELECT FIELD FOR ADD ///////////

////////////////////////////////////////////////////

    if ($_POST["multiselect"]["table"][$i]) {

    $value = $_POST["multiselect"]["table"][$i];

    $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

        <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

          <div class="col-md-4">

              <select class="form-control select2" name="' . $value . '[]" id="' . $value . '" multiple="multiple">

              <option value="">Select ' . ucfirst($value) . '</option>

      <?php 

      if(isset($' . $value . ') && !empty($' . $value . ')):

      foreach($' . $value . ' as $key => $value): ?>

          <option value="<?php echo $value->' . $_POST["multiselect"]['key'][$i] . '; ?>">

            <?php echo $value->' . $_POST["multiselect"]['value'][$i] . '; ?>

          </option>

      <?php endforeach; ?>

      <?php endif; ?>

      </select>

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

}

////////////////////////////////////////////////////

///////// ./ GENERATE MULTI SELECT FIELD FOR ADD /. //////

////////////////////////////////////////////////////



    }

}



            $data = str_replace("==formfields==", $formfields, $data);

            $data = str_replace("singlequote", "'", $data);

            file_put_contents($add_file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Add Generator End ///////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Edit Generator Start ////////////////////

//////////////////////////////////////////////////////////////////////////

            $ori_path = $this->config->item('base_path') . "application/views/admin/";

            $view_path = $this->config->item('base_path') . "application/views/admin/$table/";

            $edit_file = $view_path . 'edit.php';

            if (file_exists($edit_file)) {

                $handle = fopen($edit_file, 'w') or die('Cannot open file:  ' . $edit_file);

            } else {

                mkdir($ori_path . $table, 0700);

                $handle = fopen($edit_file, 'w') or die('Cannot open file:  ' . $edit_file);

            }



            $current = "";

            $myfile = fopen($controller_path . "module_files/edit.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/add.php"));

            fclose($myfile);



            file_put_contents($edit_file, $current);

            $data = file_get_contents($edit_file);



            $data = str_replace("@@@", "$", $data);

            $data = str_replace("cntlr", $cntlr, $data);



            $formfields = "";

            foreach ($_POST['ischeck'] as $key => $value) {

                if (isset($value) && !empty($value)) {



////////////////////////////////////////////////////

/////////// GENERATE Edit Input FIELD FOR ADD //////

////////////////////////////////////////////////////

                    if ($_POST[$value][0] == "input") {



                        $formfields .= '



<!-- ' . ucfirst($value) . ' Start -->

<div class="form-group">

  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

  <div class="col-sm-4">

    <input type="text" class="form-control" id="' . $value . '" name="' . $value . '" 

    

    value="<?php echo set_value("' . $value . '",html_entity_decode($' . $table . '->' . $value . ')); ?>"

    >

  </div>

  <div class="col-sm-5" >

 

    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

  </div>

</div> 

<!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

/////// ./ GENERATE Edit Input FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE Edit Textarea FIELD FOR ADD ///

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "textarea") {

                        $formfields .= '

<!-- ' . ucfirst($value) . ' Start -->



<div class="form-group">

  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

  <div class="col-sm-4">

    <textarea class="form-control" id="' . $value . '" name="' . $value . '"><?php echo set_value("' . $value . '",html_entity_decode($' . $table . '->' . $value . ')); ?></textarea>

  </div>

  <div class="col-sm-5" >

 

    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

  </div>

</div> 



<!-- ' . ucfirst($value) . ' End -->

';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Textarea FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE Edit Date FIELD FOR ADD ///////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "date") {

                        

                        $formfields .= '



<!-- ' . ucfirst($value) . ' Start -->

<div class="form-group">

  <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

  <div class="col-sm-4">

    <input type="text" class="form-control span2 datepicker" id="' . $value . '" name="' . $value . '" 

    

    value="<?php echo set_value("' . $value . '",$' . $table . '->' . $value . '); ?>"

    >

  </div>

  <div class="col-sm-5" >

 

    <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

  </div>

</div> 

<!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

//////// ./ GENERATE Edit Date FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE TIME FIELD FOR ADD /. /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "time") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-4 clockpicker" data-autoclose="true">

     <input type="text" class="form-control" value="<?php echo set_value("' . $value . '",$' . $table . '->' . $value . '); ?>" name="' . $value . '" id="' . $value . '">

      </div>

      <div class="col-sm-5" >

        <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

      </div>

    </div> 

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE TIME FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// ./ GENERATE DATETIME FIELD FOR ADD /. //

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "datetime") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-4">

        <input type="text" class="form-control datetimepicker" name="' . $value . '" id="' . $value . '" value="<?php echo set_value("' . $value . '",$' . $table . '->' . $value . '); ?>"/> 

      </div>

      <div class="col-sm-5" >

        <?php echo form_error("' . $value . '","<span class=singlequotelabel label-dangersinglequote>","</span>")?>

      </div>

    </div> 

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

/////////// ./ GENERATE DATETIME FIELD FOR ADD /. //

////////////////////////////////////////////////////

////////////////////////////////////////////////////

/////////// GENERATE Edit Select FIELD FOR ADD /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "select") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

        <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

          <div class="col-md-4">

              <select class="form-control select2" name="' . $value . '" id="' . $value . '">

              <option value="">Select ' . ucfirst($value) . '</option>

      <?php 

      if(isset($' . $_POST[$value]['selected_table'] . ') && !empty($' . $_POST[$value]['selected_table'] . ')):

      foreach($' . $_POST[$value]['selected_table'] . ' as $key => $value): ?>

          <option value="<?php echo $value->' . $_POST[$value]['key'] . '; ?>" <?php echo $value->' . $_POST[$value]['key'] . '==$' . $table . '->' . $value . '?\'selected="selected"\':""; ?>>

            <?php echo $value->' . $_POST[$value]['value'] . '; ?>

          </option>

      <?php endforeach; ?>

      <?php endif; ?>

      </select>

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Select FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE Edit Status FIELD FOR ADD /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "status") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

     <div class="form-group">

        <label class="control-label col-md-3">' . $value . '

             

        </label>                    

         <div class=" col-md-4 switch">

                    <div class="onoffswitch">

     <input type="checkbox" class="onoffswitch-checkbox"  data-on-label="Yes" data-off-label="No"  name="' . $value . '" value="1" id="' . $value . '" <?php if(set_value("' . $value . '",$' . $table . '->' . $value . ' == 1)){echo "checked=checked";}?>/>

      <?php echo form_error("' . $value . '","<span class=err-msg>","</span>")?>

                        <label class="onoffswitch-label" for="' . $value . '">

                            <span class="onoffswitch-switch"></span>

                            <span class="onoffswitch-inner"></span>

                        </label>

                    </div>

                </div>

      </div>

      <!-- ' . ucfirst($value) . ' End -->



';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Status FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////////// GENERATE Edit Radio FIELD FOR ADD //////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "radio") {



                        $formfields .= '



     <!-- ' . ucfirst($value) . ' Start -->

     <div class="form-group">

              <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

              <div class="col-sm-4">';

                        $rad_arr = $_POST[$value]['radios'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

                <span style="margin-right:20px;"><input type="radio" style="width:20px; height:20px;" <?php echo $' . $table . '->' . $value . '=="' . $rad_arr[$aaa] . '"?\'checked="checked"\':""; ?> name="' . $value . '" value="' . $rad_arr[$aaa] . '"> ' . $rad_arr[$aaa] . ' </span>';

                        }

                        $formfields .= '

            </div>

        </div>

          <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Status FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////// GENERATE Edit Checkbox FIELD FOR ADD /////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "checkbox") {



                        $formfields .= '



        <!-- ' . ucfirst($value) . ' Start -->

        <div class="form-group">

        <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

        <div class="col-sm-4">

        <?php $arr=explode(", ", $' . $table . '->' . $value . ') ?>

        ';

                        $rad_arr = $_POST[$value]['checks'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

            <span style="margin-right:20px;"><input type="checkbox" style="width:20px; height:20px;" <?php echo in_array("' . $rad_arr[$aaa] . '", $arr)?\'checked="checked"\':""; ?> name="' . $value . '[]" value="' . $rad_arr[$aaa] . '"> ' . $rad_arr[$aaa] . ' </span>';

                        }



                        $formfields .= '

    </div>

    </div>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Checkbox FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////// GENERATE Edit Image FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "image") {



    $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

      <label for="address" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

      <div class="col-sm-6">

      <input type="file" name="' . $value . '" />

      <input type="hidden" name="old_' . $value . '" 

      value="<?php if (isset($' . $value . ') && $' . $value . '!=""){echo $' . $value . '; }?>" />  

        <?php if(isset($' . $value . '_err) && !empty($' . $value . '_err)) 

        {foreach($' . $value . '_err as $key => $error)

        {echo "<div class=\"error-msg\">' . $error . '</div>"; } }?>

        <?php if (isset($' . $table . '->' . $value . ') && $' . $table . '->' . $value . '!=""){?>

            <br>

  <img src="<?php echo $this->config->item("photo_url");?><?php echo $' . $table . '->' . $value . '; ?>" alt="pic" width="50" height="50" />

    <?php } ?>

      </div>

        <div class="col-sm-3" >

      </div>

    </div>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

///// ./ GENERATE Edit Image FIELD FOR ADD /. //////

////////////////////////////////////////////////////

                }

            }





if (isset($_POST["multiselect"])) {

    for ($i=0; $i < count($_POST["multiselect"]["table"]); $i++) {



////////////////////////////////////////////////////

////////// GENERATE MULTI SELECT FIELD FOR EDIT /////

////////////////////////////////////////////////////

    if ($_POST["multiselect"]["table"][$i]) {

    $value = $_POST["multiselect"]["table"][$i];

    $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <div class="form-group">

        <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

          <div class="col-md-4">

              <select class="form-control select2" name="' . $value . '[]" id="' . $value . '" multiple="multiple">

              <option value="">Select ' . ucfirst($value) . '</option>

      <?php 

      if(isset($' . $value . ') && !empty($' . $value . ')):

      foreach($' . $value . ' as $key => $value): ?>

          <option <?php if(in_array($value->' . $_POST["multiselect"]['key'][$i] . ', $selected_'.$_POST["multiselect"]["table"][$i].')){ echo "selected"; } ?> value="<?php echo $value->' . $_POST["multiselect"]['key'][$i] . '; ?>">

            <?php echo $value->' . $_POST["multiselect"]['value'][$i] . '; ?>

          </option>

      <?php endforeach; ?>

      <?php endif; ?>

      </select>

        </div>

    </div>

      <!-- ' . ucfirst($value) . ' End -->



';

}

////////////////////////////////////////////////////

////// ./ GENERATE MULTI SELECT FIELD FOR EDIT /. ///

////////////////////////////////////////////////////



    }

}



            $data = str_replace("==formfields==", $formfields, $data);

            $data = str_replace("singlequote", "'", $data);

            file_put_contents($edit_file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Edit Generator End //////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Generator Start /////////////////////////

//////////////////////////////////////////////////////////////////////////



            $ori_path = $this->config->item('base_path') . "application/views/admin/";

            $view_path = $this->config->item('base_path') . "application/views/admin/$table/";

            $edit_file = $view_path . 'view.php';

            if (file_exists($edit_file)) {

                $handle = fopen($edit_file, 'w') or die('Cannot open file:  ' . $edit_file);

            } else {

                mkdir($ori_path . $table, 0700);

                $handle = fopen($edit_file, 'w') or die('Cannot open file:  ' . $edit_file);

            }



            $current = "";

            $myfile = fopen($controller_path . "module_files/view.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/add.php"));

            fclose($myfile);



            file_put_contents($edit_file, $current);

            $data = file_get_contents($edit_file);



            $data = str_replace("@@@", "$", $data);

            $data = str_replace("cntlr", $cntlr, $data);



            $formfields = "

<table class='table table-bordered' style='width:70%;' align='center'>";

            foreach ($_POST['ischeck'] as $key => $value) {

                if (isset($value) && !empty($value)) {



////////////////////////////////////////////////////

///////// GENERATE Edit Input FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    if ($_POST[$value][0] == "input") {



                        $formfields .= '

    <tr>

     <td>

       <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

     </td>

     <td> 

       <?php echo set_value("' . $value . '",html_entity_decode($' . $table . '->' . $value . ')); ?>

     </td>

    </tr>

    ';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Input FIELD FOR ADD /. /////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

///////// GENERATE Edit Date FIELD FOR ADD /////////

////////////////////////////////////////////////////

elseif ($_POST[$value][0] == "date" || $_POST[$value][0] == "time" || $_POST[$value][0] == "datetime") {

    $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <tr>

     <td>

      <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

     </td>

     <td> 

       <?php echo set_value("' . $value . '", html_entity_decode($' . $table . '->' . $value . ')); ?>

     </td>

    </tr>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

////// ./ GENERATE Edit Date FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

/////// GENERATE Edit Textarea FIELD FOR ADD ///////

////////////////////////////////////////////////////

elseif ($_POST[$value][0] == "textarea") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <tr>

     <td>

      <label for="' . $value . '" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

     </td>

     <td> 

       <?php echo set_value("' . $value . '",  html_entity_decode($' . $table . '->' . $value . ')); ?>

     </td>

    </tr>

    <!-- ' . ucfirst($value) . ' End -->



    ';



                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Textarea FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

//////// GENERATE Edit Select FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "select") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <tr>

     <td>

      <label class="control-label col-md-3"> ' . ucfirst($value) . ' </label>

     </td>

     <td> 

       <?php 

          if(isset($' . $_POST[$value]['selected_table'] . ') && !empty($' . $_POST[$value]['selected_table'] . ')):



          foreach($' . $_POST[$value]['selected_table'] . ' as $key => $value): 

           if($value->' . $_POST[$value]['key'] . '==$' . $table . '->' . $value . ')

                 echo $value->' . $_POST[$value]['value'] . ';



           endforeach; 

           endif; ?>

     </td>

    </tr>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Select FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

//////// GENERATE Edit Status FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "status") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <tr>

     <td>

      <label class="control-label col-md-3">' . $value . '</label>

     </td>

     <td> 

       <?php if(set_value("' . $value . '",$' . $table . '->' . $value . ' == 1)){echo "Active";}else{ echo "Inactive";}?>

     </td>

    </tr>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Status FIELD FOR ADD /. //////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

//////// GENERATE Edit Radio FIELD FOR ADD /////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "radio") {



                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <tr>

     <td>

      <label class="col-sm-3 control-label">Select ' . ucfirst($value) . '</label>

     </td>

     <td> 

       ';

                        $rad_arr = $_POST[$value]['radios'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

       <?php echo $' . $table . '->' . $value . '=="' . $rad_arr[$aaa] . '"?\'' . $rad_arr[$aaa] . '\':""; ?>';

                        }

                        $formfields .= '

     </td>

    </tr>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Radio FIELD FOR ADD /. ///////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

////// GENERATE Edit Checkbox FIELD FOR ADD ////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "checkbox") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <tr>

     <td>

      <label class="col-sm-3 control-label">' . ucfirst($value) . '</label>

     </td>

     <td> 

       <?php $arr=explode(", ", $' . $table . '->' . $value . ') ?>

              ';

                        $rad_arr = $_POST[$value]['checks'];

                        for ($aaa = 0; $aaa < count($rad_arr); $aaa++) {

                            $formfields .= '

                <span style="margin-left:5px;"><?php echo in_array("' . $rad_arr[$aaa] . '", $arr)?\'' . $rad_arr[$aaa] . ', \':""; ?></span>';

                        }

                        $formfields .= '

     </td>

    </tr>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

//// ./ GENERATE Edit Checkbox FIELD FOR ADD /. ////

////////////////////////////////////////////////////



////////////////////////////////////////////////////

//////// GENERATE Edit Image FIELD FOR ADD /////////

////////////////////////////////////////////////////

                    elseif ($_POST[$value][0] == "image") {

                        $formfields .= '



    <!-- ' . ucfirst($value) . ' Start -->

    <tr>

     <td>

      <label for="address" class="col-sm-3 control-label"> ' . ucfirst($value) . ' </label>

     </td>

     <td>

     <?php if (isset($' . $table . '->' . $value . ') && $' . $table . '->' . $value . '!=""){?>

                <br>

        <img src="<?php echo $this->config->item("photo_url");?><?php echo $' . $table . '->' . $value . '; ?>" alt="pic" width="50" height="50" />

        <?php } ?>

     </td>

    </tr>

    <!-- ' . ucfirst($value) . ' End -->



    ';

                    }

////////////////////////////////////////////////////

///// ./ GENERATE Edit Image FIELD FOR ADD /. //////

////////////////////////////////////////////////////

                }

            }



    if (isset($_POST["multiselect"])) {

        for ($i=0; $i < count($_POST["multiselect"]["table"]); $i++) {

            if ($_POST["multiselect"]["table"][$i]) {

                $rtable = $_POST["multiselect"]["r_table"][$i];

                $field1 = $_POST["multiselect"]["r_main"][$i];

                $field2 = $_POST["multiselect"]["r_multi"][$i];

                $call_multi_add .= "\n\t@@@this->==table==->multiSelectInsert(\"$rtable\", \"$field2\", @@@insert_id, \"$field1\", @@@_POST['".$_POST["multiselect"]["table"][$i]."']);\n";

                $call_multi_edit .= "\n\t@@@this->==table==->multiSelectInsert(\"$rtable\", \"$field2\", @@@id, \"$field1\", @@@_POST['".$_POST["multiselect"]["table"][$i]."']);\n";

                $list_tbl .= "\n\t@@@data['".$_POST["multiselect"]["table"][$i]."']=@@@this->==table==->getList('".$_POST["multiselect"]["table"][$i]."');\n";

                $return_multi_selected_id.= "\n\t@@@data['selected_".$_POST["multiselect"]["table"][$i]."'] = @@@this->==table==->getSelectedIds(\"$rtable\", @@@id, \"$field1\", \"$field2\");\n";



                $formfields .= '



                <!-- ' . ucfirst($_POST["multiselect"]["table"][$i]) . ' Start -->

                <tr>

                 <td>

                  <label for="address" class="col-sm-3 control-label"> ' . ucfirst($rtable) . ' </label>

                 </td>

                 <td>

                 <?php echo implode(", ", $selected_'.$_POST["multiselect"]["table"][$i].'_data); ?>

                 </td>

                </tr>

                <!-- ' . ucfirst($_POST["multiselect"]["table"][$i]) . ' End -->



                ';



            }

        }

    }



            $formfields .= '<tr><td colspan="2"><a type="reset" class="btn btn-info pull-right" onclick="history.back()">Back</a></td></tr></table>';

            $data = str_replace("==formfields==", $formfields, $data);

            $data = str_replace("singlequote", "'", $data);

// $data = str_replace("==backscript==", "", $data);



            file_put_contents($edit_file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Generator End ///////////////////////////

//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Manage Generator Start //////////////////

//////////////////////////////////////////////////////////////////////////



            $ori_path = $this->config->item('base_path') . "application/views/admin/";

            $view_path = $this->config->item('base_path') . "application/views/admin/$table/";

            $manage_file = $view_path . 'manage.php';

            if (file_exists($manage_file)) {

                $handle = fopen($manage_file, 'w') or die('Cannot open file:  ' . $manage_file);

            } else {

                mkdir($ori_path . $table, 0700);

                $handle = fopen($manage_file, 'w') or die('Cannot open file:  ' . $manage_file);

            }



            $current = "";

            $myfile = fopen($controller_path . "module_files/manage_one_to_many.php", "r") or die("Unable to open file!");

            $current = fread($myfile, filesize($controller_path . "module_files/manage_one_to_many.php"));

            fclose($myfile);



            file_put_contents($manage_file, $current);

            $data = file_get_contents($manage_file);



            $data = str_replace("@@@", "$", $data);

            $data = str_replace("cntlr", $cntlr, $data);



            $option_fields = "";

            $tableheadrows = '<?php $sortSym=isset($_GET["order"]) && $_GET["order"]=="asc" ? "up" : "down"; ?>';

            $tabledatarows = "<th><input name='input' id='del' onclick=\"callme('show')\"  type='checkbox' class='del' value='<?php echo @@@value->".$primary_key."; ?>'/></th>

                              

                              <th><?php if(!empty(@@@value->".$primary_key.")){ echo @@@count; @@@count++; }?></th>";

            foreach ($_POST['ischeck'] as $key => $value) {

                if (isset($value) && !empty($value)) {



                    if ($_POST[$value][0] == 'select') {



                        $tableheadrows .= '

                <?php

                 $symbol = isset($_GET["sortBy"]) && $_GET["sortBy"]=="' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["value"] . '"?"<i class=\'fa fa-sort-$sortSym\' aria-hidden=\'true\'></i>": "<i class=\'fa fa-sort\' aria-hidden=\'true\'></i>"; ?>



                <th> <a href="<?php echo $fields_links["' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["value"] . '"]; ?>" class="link_css"> ' . ucfirst($value) . ' <?php echo $symbol ?></a></th>

                        ';



                        $option_fields .= '<option value="' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["value"] . '" <?php echo $searchBy=="' . $_POST[$value]["selected_table"] . '.' . $_POST[$value]["value"] . '"?\'selected="selected"\':""; ?>>' . ucfirst($value) . '</option>';

                    } else {

                        $tableheadrows .= '

                <?php $symbol = isset($_GET["sortBy"]) && $_GET["sortBy"]=="' . $value . '"?"<i class=\'fa fa-sort-$sortSym\' aria-hidden=\'true\'></i>": "<i class=\'fa fa-sort\' aria-hidden=\'true\'></i>"; ?>

                <th> <a href="<?php echo $fields_links["' . $value . '"]; ?>" class="link_css"> ' . ucfirst($value) . ' <?php echo $symbol ?></a></th>

                        ';



                        $option_fields .= '<option value="' . $value . '" <?php echo $searchBy=="' . $value . '"?\'selected="selected"\':""; ?>>' . ucfirst($value) . '</option>';

                    }





                    if ($_POST[$value][0] == 'status') {

                        $tabledatarows .= '<th><a href="<?php echo base_url()?>admin/' . $table . '/status/' . $value . '/<?php echo @@@value->'.$primary_key.'."?redirect=".current_url()."?".urlencode($_SERVER["QUERY_STRING"]); ?>">

                        <?php if(!empty(@@@value->' . $value . ') and @@@value->' . $value . '==1 )

                        { echo "Active"; }else{ echo "Inactive";}?>

                       </a></th>

                ';

                    } elseif ($_POST[$value][0] == 'image') {

                        $tabledatarows .= '<th><?php if(!empty(@@@value->' . $value . ')){ ?> 

                        <img src="<?php echo $this->config->item(\'photo_url\');?><?php echo @@@value->' . $value . '; ?>" alt="pic" width="50" height="50" />

                         <?php }?></th>';

                    } else {

                        $tabledatarows .= '<th><?php if(!empty(@@@value->' . $value . ')){ echo @@@value->' . $value . '; }?></th>

                ';

                    }

                }



                //echo $_POST[$value][0]."<br>";

            }

            $tabledatarows .= '<th class="action-width">

           <a href="<?php echo base_url()?>admin/' . $table_new . '/view/<?php echo @@@value->'.$primary_key.'; ?>/<?php echo @@@rel_field; ?>/<?php echo @@@rel_id; ?>" title="View">

            <span class="btn btn-info " ><i class="fa fa-eye"></i></span>

           </a>

           <a href="<?php echo base_url()?>admin/' . $table_new . '/edit/<?php echo @@@value->'.$primary_key.'; ?>/<?php echo @@@rel_field; ?>/<?php echo @@@rel_id; ?>" title="Edit">

            <span class="btn btn-info " ><i class="fa fa-edit"></i></span>

           </a>

           <a  title="Delete" data-toggle="modal" data-target="#commonDelete" onclick="set_common_delete(\'<?php echo @@@value->'.$primary_key.'; ?>\',\'' . $table_new . '\');">

           <span class="btn btn-info " ><i class="fa fa-trash-o "></i></span>

           </a>

            </th>';







            $row_id = " id=\"hide<?php echo @@@value->".$primary_key."; ?>\" ";

            $data = str_replace("==tableheadrows==", $tableheadrows, $data);

            $data = str_replace("==tabledatarows==", $tabledatarows, $data);

            $data = str_replace("==searchoptions==", $option_fields, $data);

            $data = str_replace("==table==", $table_new, $data);

            $data = str_replace("++id++", $row_id, $data);

            $data = str_replace("@@@", "$", $data);

            $data = str_replace("singlequote", "'", $data);



            file_put_contents($manage_file, $data);



//////////////////////////////////////////////////////////////////////////

/////////////////////////// View Manage Generator End ////////////////////

//////////////////////////////////////////////////////////////////////////





            $this->session->set_flashdata('message', 'Module created Successfully!');

            redirect('admin/module/one_to_many_add');

        }

    }













    function edit($id) {

        if (!$this->tank_auth->is_logged_in()) {

            redirect('/auth/login/');

        }



        if (isset($id) and ! empty($id)) {

            $data = NULL;

            $data['user_id'] = $userid = $this->tank_auth->get_user_id();

            $data['username'] = $this->tank_auth->get_username();

            $data['email'] = $this->tank_auth->get_email();

            $data['groupid'] = $this->tank_auth->get_group();



            $this->form_validation->set_rules('module_name', 'module Name', 'trim|xss_clean|required');

            $this->form_validation->set_rules('module_value', 'module Value', 'trim|xss_clean|required');

            $this->form_validation->set_rules('status', 'Is Status', 'trim|xss_clean');



            $data['errors'] = array();

            if ($this->form_validation->run() == FALSE) {

                $data['module'] = $this->generic->getList('module', 'r', '', '', 'id', $id, 'userid', $userid);

                $this->load->view('admin/module/edit', $data);

            } else {

                $saveData['module_name'] = set_value('module_name');

                $saveData['module_value'] = set_value('module_value');

                $saveData['userid'] = $userid;

                $saveData['status'] = set_value('status');

                $saveData['ip'] = $_SERVER['REMOTE_ADDR'];

                $saveData['modified'] = date("Y-m-d H:i:s");



                $this->generic->crud('module', $saveData, 'id', $id, 'update');

                $this->session->set_flashdata('message', 'Tax Updated Successfully!');

                redirect('admin/module');

            }

        } else {

            $this->session->set_flashdata('message', ' Invalid Id !');

            redirect('admin/module');

        }

    }



    function delete($id = '') {

        if (!$this->tank_auth->is_logged_in()) {

            redirect('/auth/login/');

        }



        $data['user_id'] = $userid = $this->tank_auth->get_user_id();

        $data['username'] = $this->tank_auth->get_username();

        $data['email'] = $this->tank_auth->get_email();

        $data['groupid'] = $this->tank_auth->get_group();



        if (isset($id) and ! empty($id)) {

            $count = $this->generic->getList('module', 'c', '', '', 'id', $id);

            if (isset($count) and ! empty($count)) {

                $this->generic->crud('module', '', 'id', $id, 'delete');

                $this->session->set_flashdata('message', ' Tax Deleted Successfully !');

                redirect('admin/module');

            } else {

                $this->session->set_flashdata('message', ' Invalid Id !');

                redirect('admin/module');

            }

        } else {

            $this->session->set_flashdata('message', ' Invalid Id !');

            redirect('admin/module');

        }

    }



    function status($id) {



        if (!$this->tank_auth->is_logged_in()) {

            redirect('/auth/login/');

        }



        if (isset($id) && !empty($id)) {

            if (!is_null($module = $this->generic->getList('module', 'r', '', '', 'id', $id))) {

                $status = $module->status;



                if ($status == 1) {

                    $status = 0;

                } else {

                    $status = 1;

                }

                $statusData['status'] = $status;

                $this->generic->crud('module', $statusData, 'id', $id, 'update');

                $this->session->set_flashdata('message', 'Status Changed Successfully');



                redirect('admin/module');

            } else {

                $this->session->set_flashdata('error', 'Invalid Record Id!');

                redirect('admin/module');

            }

        } else {

            $this->session->set_flashdata('error', 'Invalid Record Id!');

            redirect('admin/module');

        }

    }



    function get_fields() {

        $tbl_name = $_POST["tbl_name"];

        $fields = $this->db->list_fields($tbl_name);

        echo "<table class='table table-striped table-bordered table-hover client dataTable no-footer' cellpadding='20px' cellspacing='10px'>

  <tr>

  <td><input type='checkbox' onclick='checkAllCheckbox();' id='checkAll'></td>

  <th>Field</th>

  <th>Validations</th>

  <th>Input</th>

  <th>Textarea</th>

  <th>Dropdown</th>

  <th>Status</th>

  <th>Image</th>

  <th>Radio</th>

  <th>Checkbox</th>

  <th>Date</th>

  <th>Time</th>

  <th>Date Time</th>

  </tr>";

        $i = 0;

        foreach ($fields as $key => $value) {

            ?>

            <tr>



                <!-- CHECKBOX -->

                <td><input class="checked" type="checkbox" name="ischeck[]" value="<?php echo $value; ?>"></td>



                <!-- TITLE -->

                <td><?php echo $value; ?></td>



                <!-- REQUIRED -->

                <td>

                    <select multiple chosen class="form-control chosen-select" name="required_<?php echo $value; ?>[]" id="required_<?php echo $value; ?>[]" style="width: 150px;">

                        <option value="required">required</option>

                        <option value="min_length[6]">min_length[8]</option>

                        <option value="max_length[12]">max_length[8]</option>

                        <option value="exact_length[8]">exact_length[8]</option>

                        <option value="greater_than[8]">greater_than[8]</option>

                        <option value="less_than[8]">less_than[8]</option>

                        <option value="alpha">alpha</option>

                        <option value="alpha_numeric">alpha_numeric</option>

                        <option value="alpha_dash">alpha_dash</option>

                        <option value="numeric">numeric</option>

                        <option value="integer">integer</option>

                        <option value="decimal">decimal</option>

                        <option value="is_natural">is_natural</option>

                        <option value="is_natural_no_zero">is_natural_no_zero</option>

                        <option value="valid_email">valid_email</option>

                        <option value="valid_emails">valid_emails</option>

                        <option value="valid_ip">valid_ip</option>

                        <option value="valid_base64">valid_base64</option>

                        <option value="xss_clean">xss_clean</option>

                        <option value="prep_for_form">prep_for_form</option>

                        <option value="prep_url">prep_url</option>

                        <option value="strip_image_tags">strip_image_tags</option>

                        <option value="encode_php_tags">encode_php_tags</option>

                        <option value="trim">trim</option>

                        <option value="htmlspecialchars">htmlspecialchars</option>

                        <option value="urldecode">urldecode</option>

                    </select>

                    <!-- <input type="checkbox" name="required_<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');close_all();"> -->

                </td>



                <!-- INPUT -->

                <td><input type="radio" class="default_input" value="input" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');close_all();"></td>



                <!-- TEXTAREA -->

                <td><input type="radio" value="textarea" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');close_all();"></td>



                <!-- DROPDOWN -->

                <td>

                    <input type="radio" name="<?php echo $value; ?>[]" value="select" onclick="show_tables('table_id_<?php echo $i; ?>');setTitle('<?php echo $value; ?>');">



                    <!-- select table for dropdown -->

                    <span class="top-display" id="table_id_<?php echo $i; ?>" style="display: none">

                        Table: <select class="form-control" id="select_table_<?php echo $i; ?>" name="<?php echo $value . "[selected_table]"; ?>" onchange="show_key_value('select_table_<?php echo $i; ?>', 'table_key_id_<?php echo $i; ?>', 'table_value_id_<?php echo $i; ?>', '<?php echo $value; ?>', '<?php echo $i; ?>');">

                            <option value="">-Select Table-</option>

                            <?php

                            $tables = $this->db->list_tables();

                            for ($j = 0; $j < count($tables); $j++) {

                                if(!in_array($tables[$j], array())){

                                ?>

                                <option value="<?php echo set_value('table_name', $tables[$j]); ?>"><?php echo $tables[$j] ?></option>

                            <?php } } ?>

                        </select>

                        <!-- select table for key -->

                        <span id="table_key_id_<?php echo $i; ?>" style="display: none">

                        </span>

                        <!-- select table for keyvalues -->

                        <span id="table_value_id_<?php echo $i; ?>" style="display: none">

                        </span>

                    </span>



                </td>



                

                <!-- STATUS -->

                <td><input type="radio" value="status" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



                <!-- IMAGE -->

                <td><input type="radio" value="image" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



                <!-- RADIO -->

                <td>

                    <input type="radio" value="radio" name="<?php echo $value; ?>[]" onclick="select_radio('radio_span_<?php echo $i; ?>', '<?php echo $i; ?>');

                            setTitle('<?php echo $value; ?>');">

                    <table class="top-display" id="radio_span_<?php echo $i; ?>" style="display: none;">

                        <tr><td colspan="2"><a href="javascript:void(0)" onclick="setTitleRadio('<?php echo $value; ?>');

                                add_more_radio('radio_span_<?php echo $i; ?>');"><b>Add</b></a></td></tr>

                        <tr>

                            <td><input type="text" value="Radio" name="<?php echo $value; ?>[radios][]"></td>

                            <td></td>

                        </tr>

                    </table>

                </td>



                <!-- CHECKBOX -->

                <td><input type="radio" value="checkbox" name="<?php echo $value; ?>[]" onclick="select_check('check_span_<?php echo $i; ?>', '<?php echo $i; ?>');

                           ">

                    <table class="top-display" id="check_span_<?php echo $i; ?>" style="display: none;">

                        <tr><td colspan="2"><a href="javascript:void(0)" onclick="setTitleCheck('<?php echo $value; ?>');add_more_check('check_span_<?php echo $i; ?>');"><b>Add</b></a></td></tr>

                        <tr>

                            <td><input value="Checkbox" type="text" name="<?php echo $value; ?>[checks][]"></td>

                            <td></td>

                        </tr>

                    </table>

                </td>



                <!-- DATE -->

                <td><input type="radio" value="date" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



                <!-- TIME -->

                <td><input type="radio" value="time" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



                <!-- DATETIME -->

                <td><input type="radio" value="datetime" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



            </tr>

            <?php

            $i++;

        }

        ?>

        </table>



        <br>



        <!-- Multi Select -->

        <button type="button" name="<?php echo $value; ?>[]" value="multi_select" onclick="add_multi_table();" class="btn btn-info">

        Many To Many</button>



        <!-- One To Many -->

        <button type="button" name="<?php echo $value; ?>[]" value="single_select" onclick="get_table_dropdown();" class="btn btn-info">

        One To Many</button>



        <br>

        <div id="result_multi_table"></div>

        <br>

        <div id="result_one_many_table"></div>

        <br>



        <input type="hidden" name="mode" value="generate">



        <input type="hidden" id="selected_check_field">



        <input type="hidden" id="selected_radio">

        <input type="hidden" id="radio_id">

        <input type="hidden" id="selected_field_radio">



        <input type="hidden" id="selected_check">

        <input type="hidden" id="check_id">

        <input type="hidden" id="selected_field_check">



        <input type="hidden" id="one_to_many" name="one_to_many">



        <input type="hidden" id="accet_url" value="<?php echo $this->config->item('accet_url'); ?>">

        <?php

    }







    function get_one_many_fields() {

        $tbl_name = $_POST["tbl_name"];

        $fields = $this->db->list_fields($tbl_name);

        echo "<table class='table table-striped table-bordered table-hover client dataTable no-footer' cellpadding='20px' cellspacing='10px'>

  <tr>

  <td><input type='checkbox' onclick='checkAllCheckbox();' id='checkAll'></td>

  <th>Field</th>

  <th>Validations</th>

  <th>Input</th>

  <th>Textarea</th>

  <th>Dropdown</th>

  <th>Status</th>

  <th>Image</th>

  <th>Radio</th>

  <th>Checkbox</th>

  <th>Date</th>

  <th>Time</th>

  <th>Date Time</th>

  </tr>";

        $i = 0;

        foreach ($fields as $key => $value) {

            ?>

            <tr>



                <!-- CHECKBOX -->

                <td><input class="checked" type="checkbox" name="ischeck[]" value="<?php echo $value; ?>"></td>



                <!-- TITLE -->

                <td><?php echo $value; ?></td>



                <!-- REQUIRED -->

                <td>

                    <select multiple chosen class="form-control chosen-select" name="required_<?php echo $value; ?>[]" id="required_<?php echo $value; ?>[]" style="width: 150px;">

                        <option value="required">required</option>

                        <option value="min_length[6]">min_length[8]</option>

                        <option value="max_length[12]">max_length[8]</option>

                        <option value="exact_length[8]">exact_length[8]</option>

                        <option value="greater_than[8]">greater_than[8]</option>

                        <option value="less_than[8]">less_than[8]</option>

                        <option value="alpha">alpha</option>

                        <option value="alpha_numeric">alpha_numeric</option>

                        <option value="alpha_dash">alpha_dash</option>

                        <option value="numeric">numeric</option>

                        <option value="integer">integer</option>

                        <option value="decimal">decimal</option>

                        <option value="is_natural">is_natural</option>

                        <option value="is_natural_no_zero">is_natural_no_zero</option>

                        <option value="valid_email">valid_email</option>

                        <option value="valid_emails">valid_emails</option>

                        <option value="valid_ip">valid_ip</option>

                        <option value="valid_base64">valid_base64</option>

                        <option value="xss_clean">xss_clean</option>

                        <option value="prep_for_form">prep_for_form</option>

                        <option value="prep_url">prep_url</option>

                        <option value="strip_image_tags">strip_image_tags</option>

                        <option value="encode_php_tags">encode_php_tags</option>

                        <option value="trim">trim</option>

                        <option value="htmlspecialchars">htmlspecialchars</option>

                        <option value="urldecode">urldecode</option>

                    </select>

                    <!-- <input type="checkbox" name="required_<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');close_all();"> -->

                </td>



                <!-- INPUT -->

                <td><input type="radio" class="default_input" value="input" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');close_all();"></td>



                <!-- TEXTAREA -->

                <td><input type="radio" value="textarea" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');close_all();"></td>



                <!-- DROPDOWN -->

                <td>

                    <input type="radio" name="<?php echo $value; ?>[]" value="select" onclick="show_tables('table_id_<?php echo $i; ?>');setTitle('<?php echo $value; ?>');">



                    <!-- select table for dropdown -->

                    <span class="top-display" id="table_id_<?php echo $i; ?>" style="display: none">

                        Table: <select class="form-control" id="select_table_<?php echo $i; ?>" name="<?php echo $value . "[selected_table]"; ?>" onchange="show_key_value('select_table_<?php echo $i; ?>', 'table_key_id_<?php echo $i; ?>', 'table_value_id_<?php echo $i; ?>', '<?php echo $value; ?>', '<?php echo $i; ?>');">

                            <option value="">-Select Table-</option>

                            <?php

                            $tables = $this->db->list_tables();

                            for ($j = 0; $j < count($tables); $j++) {

                                if(!in_array($tables[$j], array())){

                                ?>

                                <option value="<?php echo set_value('table_name', $tables[$j]); ?>"><?php echo $tables[$j] ?></option>

                            <?php } } ?>

                        </select>

                        <!-- select table for key -->

                        <span id="table_key_id_<?php echo $i; ?>" style="display: none">

                        </span>

                        <!-- select table for keyvalues -->

                        <span id="table_value_id_<?php echo $i; ?>" style="display: none">

                        </span>

                    </span>



                </td>



                

                <!-- STATUS -->

                <td><input type="radio" value="status" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



                <!-- IMAGE -->

                <td><input type="radio" value="image" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



                <!-- RADIO -->

                <td>

                    <input type="radio" value="radio" name="<?php echo $value; ?>[]" onclick="select_radio('radio_span_<?php echo $i; ?>', '<?php echo $i; ?>');

                            setTitle('<?php echo $value; ?>');">

                    <table class="top-display" id="radio_span_<?php echo $i; ?>" style="display: none;">

                        <tr><td colspan="2"><a href="javascript:void(0)" onclick="setTitleRadio('<?php echo $value; ?>');

                                add_more_radio('radio_span_<?php echo $i; ?>');"><b>Add</b></a></td></tr>

                        <tr>

                            <td><input type="text" value="Radio" name="<?php echo $value; ?>[radios][]"></td>

                            <td></td>

                        </tr>

                    </table>

                </td>



                <!-- CHECKBOX -->

                <td><input type="radio" value="checkbox" name="<?php echo $value; ?>[]" onclick="select_check('check_span_<?php echo $i; ?>', '<?php echo $i; ?>');

                           ">

                    <table class="top-display" id="check_span_<?php echo $i; ?>" style="display: none;">

                        <tr><td colspan="2"><a href="javascript:void(0)" onclick="setTitleCheck('<?php echo $value; ?>');add_more_check('check_span_<?php echo $i; ?>');"><b>Add</b></a></td></tr>

                        <tr>

                            <td><input value="Checkbox" type="text" name="<?php echo $value; ?>[checks][]"></td>

                            <td></td>

                        </tr>

                    </table>

                </td>



                <!-- DATE -->

                <td><input type="radio" value="date" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



                <!-- TIME -->

                <td><input type="radio" value="time" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



                <!-- DATETIME -->

                <td><input type="radio" value="datetime" name="<?php echo $value; ?>[]" onclick="setTitle('<?php echo $value; ?>');"></td>



            </tr>

            <?php

            $i++;

        }

        ?>

        </table>



        <br>





        <input type="hidden" name="mode" value="generate">



        <input type="hidden" id="selected_check_field">



        <input type="hidden" id="selected_radio">

        <input type="hidden" id="radio_id">

        <input type="hidden" id="selected_field_radio">



        <input type="hidden" id="selected_check">

        <input type="hidden" id="check_id">

        <input type="hidden" id="selected_field_check">



        <input type="hidden" id="accet_url" value="<?php echo $this->config->item('accet_url'); ?>">

        <?php

    }











    function get_multi_table_html()

    { ?>



    <table class="table multi_table_box" style="border-collapse: collapse;">

                    <tr>

                    <td colspan="4"><center><h1>Many To Many<img src="<?php echo base_url(); ?>accets/img/button-cross_basic_red.png" onclick="delete_multi_table(this);" style="float: right;"></h1></center></td>



                    </tr>

                   <tr>

                     <td>

                    <!-- select table for dropdown -->

                    <span class="top-display">

                      Multi Select Table: <a href="javascript:void(0);" data-toggle="popover" data-content="A second table that relate to current selected table. Eg: products and category."><i class="fa fa-info-circle" aria-hidden="true"></i></a>

                      <select class="form-control multi_table" name="multiselect[table][]" onchange="populate_key_val(this, 'multi_key', 'multi_value', '');">

                        <option value="">-Select Table-</option>

                            <?php

                            $tables = $this->db->list_tables();

                            for ($j = 0; $j < count($tables); $j++) {

                                if(!in_array($tables[$j], array())){

                                ?>

                                <option value="<?php echo set_value('table_name', $tables[$j]); ?>"><?php echo $tables[$j] ?></option>

                            <?php } } ?>

                        </select>

                    </span>

                    </td>

                    <td>

                        <!-- select table for key -->

                        <span class="multi_key"></span>

                    </td>

                    <td>

                        <!-- select table for values -->

                        <span class="multi_value"></span>

                    </td>



                  </tr>

                  <tr>

                    <td>

                    <!-- select table for multi Relatoin -->

                    <span class="top-display multi_relation_table">

                       Relational Table: 

                       <select class="form-control multi_relation_table" name="multiselect[r_table][]" onchange="popuplate_multi_get_key_value(this,'multi_relation_key','multi_relation_value','relation');">

                        <option value="">-Select Table-</option>

                            <?php

                            $tables = $this->db->list_tables();

                            for ($j = 0; $j < count($tables); $j++) {

                                if(!in_array($tables[$j], array())){

                                ?>

                                <option value="<?php echo set_value('table_name', $tables[$j]); ?>"><?php echo $tables[$j] ?></option>

                            <?php } } ?>

                        </select>

                    </td>

                    <td>

                        <!-- select table for key -->

                        <span class="multi_relation_key"></span>

                    </td>

                    <td>

                        <!-- select table for keyvalues -->

                        <span class="multi_relation_value"></span>

                </td>

            </tr>

        </table>

    <?php

    exit;

    }



    function popuplate_multi_get_key_value() {

        $dropdown_tbl = $_POST["table"];

        $parent_table = $_POST["parent_table"];

        $multi_table = $_POST["multi_table"];

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        ?>

        Key : <a href="javascript:void(0);" data-toggle="popover" data-content="<?php echo $parent_table; ?> id"><i class="fa fa-info-circle" aria-hidden="true"></i></a>

        <select class="form-control multi_select_key" name="multiselect[r_main][]">

            <option value="">-Select Key-</option>

            <?php foreach ($fields as $key => $value) { ?>

                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                <?php

                $i++;

            }

            ?>

        </select>

        <?php

        echo '==##==';

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        // echo $_POST["table"];

        ?>

        Value : <a href="javascript:void(0);" data-toggle="popover" data-content="<?php echo $multi_table; ?> id"><i class="fa fa-info-circle" aria-hidden="true"></i></a>

        <select class="form-control multi_select_value" name="multiselect[r_multi][]">

            <option value="">-Select Value-</option>

            <?php foreach ($fields as $key => $value) { ?>

                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                <?php

                $i++;

            }

            ?>

        </select>

        <?php

        exit;

    }



    function multi_get_key_value() {

        $dropdown_tbl = $_POST["table"];

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        ?>

        Key:<select class="form-control multi_select_key" name="multiselect[key][]">

            <option value="">-Select Key-</option>

            <?php foreach ($fields as $key => $value) { ?>

                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                <?php

                $i++;

            }

            ?>

        </select>

        <?php

        echo '==##==';

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        ?>

        Value:<select class="form-control multi_select_value" name="multiselect[value][]">

            <option value="">-Select Value-</option>

            <?php foreach ($fields as $key => $value) { ?>

                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                <?php

                $i++;

            }

            ?>

        </select>

        <?php

        exit;

    }



    function r_get_key_value() {

        $dropdown_tbl = $_POST["table"];

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        ?>

        Key:<select class="form-control multi_select_key" name="multiselect[r_main][]">

            <option value="">-Select Key-</option>

            <?php foreach ($fields as $key => $value) { ?>

                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                <?php

                $i++;

            }

            ?>

        </select>

        <?php

        echo '==##==';

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        ?>

        Value:<select class="form-control multi_select_value" name="multiselect[r_multi][]">

            <option value="">-Select Value-</option>

            <?php foreach ($fields as $key => $value) { ?>

                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                <?php

                $i++;

            }

            ?>

        </select>

        <?php

        exit;

    }



    function get_key_value() {

        $dropdown_tbl = $_POST["dropdown_tbl"];

        $field = $_POST["field"];

        $id = $_POST["id"];

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        ?>

        Key:<select class="form-control" id="key_<?php echo $id; ?>" name="<?php echo $field . "[key]"; ?>">

            <option value="">-Select Key-</option>

            <?php foreach ($fields as $key => $value) { ?>

                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                <?php

                $i++;

            }

            ?>

        </select>

        <?php

        echo '==##==';

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        ?>

        Value:<select class="form-control" id="value_<?php echo $id; ?>" name="<?php echo $field . "[value]"; ?>">

            <option value="">-Select Value-</option>

            <?php foreach ($fields as $key => $value) { ?>

                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                <?php

                $i++;

            }

            ?>

        </select>

        <?php

        exit;

    }





    function get_table_dropdown()

    {

        ?>

         <br>



        <div class="form-group">

            <span rowspan="2" valign="center"><img src="<?php echo base_url(); ?>/accets/img/button-cross_basic_red.png" onclick="delete_one_to_many(this);" style="float: right;"></span>

            <center><h1>One To Many</h1></center>

        <hr>

            <iframe  id="myIframe" src="<?php echo base_url(); ?>admin/module/one_to_many_add" style="width: 100%; height: 400px;"></iframe>

        </div>

        <?php 

        exit;

    }





    function get_key_dropdown() {

        $dropdown_tbl = $_POST["dropdown_tbl"];

        $parent_table = $_POST["parent_table"];

        $fields = $this->db->list_fields($dropdown_tbl);

        $i = 0;

        ?>

            <label for="Module_name" class="col-sm-3 control-label"> Related Field </label>

            <div class="col-sm-4">



                Select Field :  <a href="javascript:void(0);" data-toggle="popover" data-content="<?php echo $parent_table; ?> id"><i class="fa fa-info-circle" aria-hidden="true"></i></a>

                <select onchange="setRelatedField();" class="form-control" id="related_field" name="related_field">

                    <option value="">-Select Table-</option>

                    <?php foreach ($fields as $key => $value) { ?>

                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>

                        <?php

                        $i++;

                    }

                    ?>

                </select>

            </div>

        <?php

        exit;

    }

}



/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */

