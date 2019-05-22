<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class controller_name extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		@@@this->load->library('pagination');
		@@@this->load->helper('url');
		@@@this->load->library('ion_auth');
		@@@this->load->library('form_validation');
		@@@this->load->model('admin/==table==_model','==table==');
	}

	function index(@@@rel_field='', @@@rel_id='', @@@id=1)
	{
		@@@cond="";
		@@@data['searchBy']='';
		@@@data['searchValue']='';
		@@@v_fields=@@@this->==table==->v_fields;
		@@@per_page_arr = array('5', '10', '20', '50', '100');

		if (isset(@@@_GET['searchValue']) && isset(@@@_GET['searchBy'])) {
			@@@data['searchBy']=@@@_GET['searchBy'];
			@@@data['searchValue']=@@@_GET['searchValue'];
			if (!empty(@@@_GET['searchValue']) && @@@_GET['searchValue']!="" && !empty(@@@_GET['searchBy']) && @@@_GET['searchBy']!="" ) {
					@@@cond="true";
			}
		}

		@@@data['sortBy']='';
        @@@order_by = isset(@@@_GET['sortBy']) && in_array(@@@_GET['sortBy'], @@@v_fields)?@@@_GET['sortBy']:'';
        @@@order = isset(@@@_GET['order']) && @@@_GET['order']=='asc'?'asc':'desc';
        @@@searchBy = isset(@@@_GET['searchBy']) && in_array(@@@_GET['searchBy'], @@@v_fields)?@@@_GET['searchBy']:null;
        @@@searchValue = isset(@@@_GET['searchValue'])?@@@_GET['searchValue']:'';
        @@@searchValue = addslashes(@@@searchValue);

		if(isset(@@@_GET['sortBy']) && @@@_GET['sortBy']!=''){
			@@@data['sortBy']=@@@_GET['sortBy'];
			if(isset(@@@_GET['order']) && @@@_GET['order']!=''){
				@@@_GET['order']=@@@_GET['order']=='asc'?'desc':'asc';
			} else{
				@@@_GET['order']='desc';
			}
		}

		@@@get_q = @@@_GET;
		foreach (@@@v_fields as @@@key => @@@value) {
			@@@get_q['sortBy'] = @@@value;
			@@@query_result = http_build_query(@@@get_q);
			@@@data['fields_links'][@@@value] =current_url().'?'.@@@query_result;
		}
		@@@data['csvlink'] = base_url().'admin/==table==/export/csv';
		@@@data['pdflink'] = base_url().'admin/==table==/export/pdf';
		@@@data['per_page'] = isset(@@@_GET['per_page']) && in_array(@@@_GET['per_page'], @@@per_page_arr)?@@@_GET['per_page']:"5";

		// PAGINATION
		@@@rel_arr = array('rel_field'=>@@@rel_field, 'rel_id'=>@@@rel_id);
		@@@config = array();
		@@@config['suffix']='?'.@@@_SERVER['QUERY_STRING'];
        @@@config["base_url"] = base_url() . "admin/==table==/index/@@@rel_field/@@@rel_id";
        @@@total_row = @@@this->==table==->getCount('==table==', @@@searchBy, @@@searchValue, @@@rel_arr);
        @@@config["first_url"] = base_url()."admin/==table==/index/$rel_field/$rel_id".'?'.@@@_SERVER['QUERY_STRING'];
        @@@config["total_rows"] = @@@total_row;
        @@@config["per_page"] = @@@per_page = @@@data['per_page'];
        @@@config["uri_segment"] = @@@this->uri->total_segments();
        @@@config['use_page_numbers'] = TRUE;
        @@@config['num_links'] = 3; //@@@total_row
        @@@config['cur_tag_open'] = '&nbsp;<a class="current">';
        @@@config['cur_tag_close'] = '</a>';
        @@@config['full_tag_open'] = "<ul class='pagination'>";
		@@@config['full_tag_close'] ="</ul>";
		@@@config['num_tag_open'] = '<li>';
		@@@config['num_tag_close'] = '</li>';
		@@@config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		@@@config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		@@@config['next_tag_open'] = "<li>";
		@@@config['next_tagl_close'] = "</li>";
		@@@config['prev_tag_open'] = "<li>";
		@@@config['prev_tagl_close'] = "</li>";
		@@@config['first_link'] = 'First';
		@@@config['first_tag_open'] = "<li>";
		@@@config['first_tagl_close'] = "</li>";
		@@@config['last_link'] = 'Last';
		@@@config['last_tag_open'] = "<li>";
		@@@config['last_tagl_close'] = "</li>";
        @@@this->pagination->initialize(@@@config);

        if(@@@this->uri->segment(2)){
        	@@@cur_page = @@@id;
        	@@@pagi = array("cur_page"=>(@@@cur_page-1)*@@@per_page, "per_page"=>@@@per_page, 'order'=>@@@order, 'order_by'=>@@@order_by, 'rel_field'=>@@@rel_field, 'rel_id'=>@@@rel_id);
        }
        else{	
    		@@@pagi = array("cur_page"=>0, "per_page"=>@@@per_page, 'rel_field'=>@@@rel_field, 'rel_id'=>@@@rel_id);
    	}

        @@@data["results"] = @@@result = @@@this->==table==->getList("==table==",@@@pagi);
        @@@str_links = @@@this->pagination->create_links();

        @@@data["links"] = @@@str_links;
        // ./ PAGINATION /.

		if (!@@@this->ion_auth->logged_in()) {
			redirect('/auth/login/');
        }
		else {
			@@@data['==table==']  = @@@this->==table==->getList('==table==');
			@@@data["rel_field"] = @@@rel_field;
			@@@data["rel_id"] = @@@rel_id;
		    @@@this->load->view('admin/==table==/manage',@@@data);
		}
	}

	public function add(@@@rel_field='', @@@rel_id='')
	{
		if (!@@@this->ion_auth->logged_in()) {
			redirect('/auth/login/');
		}

	    @@@data = NULL;

==validation==			

        @@@data['errors'] = array();
        if(@@@this->form_validation->run() == FALSE)
        {
			***foreign_table***
		==list_tbl==
		@@@data['rel_field'] = @@@rel_field;
		@@@data['rel_id'] = @@@rel_id;
			@@@this->load->view('admin/==table==/add', @@@data);
        }
        else
        {
==fields==
			@@@rel_field = @@@_POST['rel_field'];
			@@@rel_id = @@@_POST['rel_id'];
			@@@saveData[@@@rel_field] = @@@rel_id;
			@@@insert_id = @@@this->==table==->insert('==table==',@@@saveData);
			==call_multi_add==
			@@@this->session->set_flashdata('message', 'controller_name Added Successfully!');
			@@@rel_field = @@@_POST['rel_field'];
			@@@rel_id = @@@_POST['rel_id'];
			redirect("admin/==table==/index/@@@rel_field/@@@rel_id");
	   }
	}

	function view(@@@id)
	{

	  if (!@@@this->ion_auth->logged_in()) {
	  redirect('/auth/login/');
	  }

	  if(isset(@@@id) and !empty(@@@id))
	  {
	   @@@data = NULL;

==validation==

       @@@data['errors'] = array();
       if(@@@this->form_validation->run() == FALSE) 
       {
      	***foreign_table***
      	==multi_selected_id==
      	==return_multi_selected_data==
        @@@data['==table==']=@@@this->==table==->getRow('==table==',@@@id);
        @@@this->load->view('admin/==table==/view', @@@data);
       }
       else
       {
		redirect('admin/==table==/view');
       }
    }
    else
    {
       @@@this->session->set_flashdata('message', ' Invalid Id !'); 
       redirect('admin/==table==/view');
	}
  }

	function edit(@@@id, @@@rel_field='', @@@rel_id='')
    {
	 	if (!@@@this->ion_auth->logged_in()) {
			redirect('/auth/login/');
		}

		if(isset(@@@id) and !empty(@@@id))
		{
			@@@data = NULL;

			    ==validation==

            @@@data['errors'] = array();
            if(@@@this->form_validation->run() == FALSE) 
            {
            	==list_tbl==
            	==return_multi_selected_id==
              	@@@data['==table==']=@@@this->==table==->getRow('==table==',@@@id);
              	***foreign_table***
              	@@@data['rel_field'] = @@@rel_field;
				@@@data['rel_id'] = @@@rel_id;
				@@@this->load->view('admin/==table==/edit', @@@data);
            }
            else
            {
			   	==fields==					
				@@@this->==table==->updateData('==table==',@@@saveData,@@@id);
				==call_multi_edit==
				@@@this->session->set_flashdata('message', 'controller_name Updated Successfully!');
				redirect("admin/==table==/index/@@@rel_field/@@@rel_id");
            }
		}
		else
		{
			@@@this->session->set_flashdata('message', ' Invalid Id !');	
		    redirect('admin/==table==');
		}
	 }

	 function delete(@@@id='')
     {
		if (!@@@this->ion_auth->logged_in()) {
			redirect('/auth/login/');
		}

 		if(isset(@@@id) and !empty(@@@id))
		{
			@@@count=@@@this->==table==->getCount('==table==','==table==.==primary_key==',@@@id);
			if(isset(@@@count) and !empty(@@@count))
			{
				@@@this->==table==->delete('==table==','==primary_key==',@@@id);
				@@@this->session->set_flashdata('message', ' controller_name Deleted Successfully !');
	            echo "success";
           		exit;
			}
			else
			{
				@@@this->session->set_flashdata('message', ' Invalid Id !');	
			}
		}
		else
		{
			@@@this->session->set_flashdata('message', ' Invalid Id !');
		}
	}

	function deleteAll(@@@id='')
    {

		if (!@@@this->ion_auth->logged_in()) {
			redirect('/auth/login/');
		}

		@@@all_ids = @@@_POST["allIds"];

 		if(isset(@@@all_ids) and !empty(@@@all_ids))
		{
			
			//@@@count=@@@this->==table==->getCount('==table==','==table==.id',@@@id);
			for(@@@a=0; @@@a<count(@@@all_ids); @@@a++)
	  		{
	  			if(@@@all_ids[@@@a]!="")
	  			{
					@@@this->==table==->delete('==table==','==primary_key==',@@@all_ids[@@@a]);
					@@@this->session->set_flashdata('message', ' controller_name(s) Deleted Successfully !');
				}
	  		}	

            echo "success";
       		exit;
		}
		else
		{
			@@@this->session->set_flashdata('message', ' Invalid Id !');
		}
	}

	function export(@@@filetype='csv'){

		if (!@@@this->ion_auth->logged_in()) {
			redirect('/auth/login/');
		}
		
		@@@searchBy='';
		@@@searchValue='';
		@@@v_fields=array(++sort_fields_arr2++);

		@@@data['sortBy']='';

        @@@order_by = isset(@@@_GET['sortBy']) && in_array(@@@_GET['sortBy'], @@@v_fields)?@@@_GET['sortBy']:'';
        @@@order = isset(@@@_GET['order']) && @@@_GET['order']=='asc'?'asc':'desc';

        @@@searchBy = isset(@@@_GET['searchBy']) && in_array(@@@_GET['searchBy'], @@@v_fields)?@@@_GET['searchBy']:null;
        @@@searchValue = isset(@@@_GET['searchValue'])?@@@_GET['searchValue']:'';
        @@@searchValue = addslashes(@@@searchValue);
        @@@pagi = array('order'=>@@@order, 'order_by'=>@@@order_by);

		@@@result = @@@this->==table==->getList("==table==");

		if(@@@filetype=='csv'){
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename===table==.csv');
			header('Pragma: no-cache');
			@@@csv='Sr. No,'.implode(',', @@@v_fields)."\n";
			foreach (@@@result as @@@key => @@@value) {
				@@@line=(@@@key+1).',';
				foreach (@@@v_fields as @@@field) {
					@@@line.='"'.addslashes(@@@value->@@@field).'"'.',';
				}
				@@@csv.=ltrim(@@@line,',')."\n";
			}
			echo @@@csv; exit;
		} elseif (@@@filetype=='pdf'){
			error_reporting(0);
			ob_start();
			@@@this->load->library('m_pdf');
			@@@table='
			<html>
			<head><title></title>
			<style>
			table{
				border:1px solid;
			}
			tr:nth-child(even)
			{
			    background-color: rgba(158, 158, 158, 0.82);
			}
			</style>
			</head>
			<body>
			<h1 align="center">controller_name</h1>
			<table><tr>';
			@@@table.='<th>Sr. No</th>';
			foreach (@@@v_fields as @@@value) {
				@@@table.='<th>'.@@@value.'</th>';
			}
			@@@table.='</tr>';
			foreach (@@@result as @@@key => @@@value) {
				@@@table.='<tr><td>'.(@@@key+1).'</td>';
				foreach (@@@v_fields as @@@field) {
					@@@table.='<td>'.@@@value->@@@field.'</td>';
				}
				@@@table.='</tr>';
			}
			@@@table.='</table></body></html>';
			ob_clean();
			@@@pdf = @@@this->m_pdf->load();
			@@@pdf->WriteHTML(@@@table);
			@@@pdf->Output('==table==.pdf', "D");
			exit;
		} else{
			echo 'Invalid option'; exit;
		}
	}


	function status(@@@field,@@@id)
	{
		if (!@@@this->ion_auth->logged_in()) {
			redirect('/auth/login/');
		}

		if (in_array(@@@field, array(==status_field_string==)))
		{
			if(isset(@@@id) && !empty(@@@id))
			{
				if (!is_null(@@@==table===@@@this->==table==->getRow('==table==',@@@id)))	
				{					
					@@@status = =*===table===@=@@@field;				
					if(@@@status == 1){
						@@@status = 0;
					}else{
						@@@status = 1;
					}
						@@@statusData[@@@field] = @@@status;
						@@@this->==table==->updateData('==table==',@@@statusData,@@@id);
						@@@this->session->set_flashdata('message', ucfirst(@@@field).' Updated Successfully');
						if(isset(@@@_GET['redirect']) && @@@_GET['redirect']!=''){
							redirect(@@@_GET['redirect']);
						} else{
							redirect('admin/==table==');
						}
				}else{
						@@@this->session->set_flashdata('error', 'Invalid Record Id!');
						redirect('admin/==table==');
				}
			}
			else
			{
				@@@this->session->set_flashdata('error', 'Invalid Record Id!');
				redirect('admin/==table==');
			}
		}
	}


}

