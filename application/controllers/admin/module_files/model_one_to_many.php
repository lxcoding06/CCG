<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ==table==_model extends CI_Model
{
    public @@@v_fields=array(++sort_fields_arr++);

	function __construct()
	{
		parent::__construct();
	}

	function getList(@@@table, @@@pagination=array()) {

        //  PAGINATION START
        if((isset(@@@pagination['cur_page'])) and !empty(@@@pagination['per_page']))
        {
          @@@this->db->limit(@@@pagination['per_page'],@@@pagination['cur_page']);
        }
        //  PAGINATION END

        // sort
          @@@order_by = isset(@@@_GET['sortBy']) && in_array(@@@_GET['sortBy'], @@@this->v_fields)?@@@_GET['sortBy']:'';
          @@@order = isset(@@@_GET['order']) && @@@_GET['order']=='asc'?'asc':'desc';
          if(@@@order_by!=''){
            @@@this->db->order_by(@@@order_by, @@@order);
          }

        // end sort

        // SEARCH START
        if (!empty(@@@_GET['searchValue']) && @@@_GET['searchValue']!="" && !empty(@@@_GET['searchBy']) && @@@_GET['searchBy']!="" && in_array(@@@_GET['searchBy'],@@@this->v_fields) ) {
            @@@this->db->like(@@@_GET['searchBy'],@@@_GET['searchValue']);
        }
        // SEARCH END

        // Id Condition
        if((isset(@@@pagination['rel_field'])) and !empty(@@@pagination['rel_field']))
        {
          @@@this->db->where(@@@pagination['rel_field'],@@@pagination['rel_id']);
        }
        // Id Condition

        @@@this->db->select("@@@table.* ==select_alias==");
        @@@this->db->from(@@@table);
        ==select_join==
        @@@this->db->order_by("==primary_key==","desc");
        @@@query = @@@this->db->get();
        return @@@result = @@@query->result();
    }

    function getListTable(@@@table) {
        @@@this->db->select("*");
        @@@this->db->from(@@@table);
        @@@query = @@@this->db->get();
        return @@@result = @@@query->result();
    }

    function getRow(@@@table, @@@id) {
        @@@this->db->select('*');
        @@@query = @@@this->db->get_where(@@@table, array('==primary_key==' => @@@id));
        @@@data = @@@query->result();
        return @@@data[0];
    }

    function getSelectedData(@@@table, @@@field, @@@idArr) {
        @@@this->db->select('*');
        @@@this->db->from(@@@table);
        @@@this->db->where_in('id', @@@idArr);
        @@@query = @@@this->db->get();
        @@@data = @@@query->result();
        foreach (@@@data as @@@key => @@@value) {
            @@@arr[] = @@@value->@@@field;
        }
        return @@@arr;
    }

    function getCount(@@@table, @@@key='', @@@value='', @@@rel_arr=array()) {
        if(isset(@@@key) && isset(@@@value) && !empty(@@@key) && !empty(@@@value))
        {
            @@@this->db->where(@@@key,@@@value);
        }
        @@@this->db->from(@@@table);
        ==select_join==    
        // Id Condition
        if((isset(@@@rel_arr['rel_field'])) and !empty(@@@rel_arr['rel_field']))
        {
          @@@this->db->where(@@@rel_arr['rel_field'],@@@rel_arr['rel_id']);
        }
        return @@@this->db->count_all_results();
    }

    function insert(@@@table, @@@data){
        @@@this->db->insert(@@@table, @@@data);
        return @@@this->db->insert_id();
    }

    function multiSelectInsert(@@@r_table, @@@field1, @@@value1, @@@field2, @@@value2=array())
    {
      @@@this->db->query("delete from @@@r_table where @@@field1='@@@value1'");
      if (@@@r_table!="" && @@@field1!="" && @@@value1!="" && @@@field2!="" && count(@@@value2)>0) {
        for (@@@i=0; @@@i < count(@@@value2); @@@i++) {
          @@@data[] = array(
            @@@field1 => @@@value1,
            @@@field2 => @@@value2[@@@i],
          );
        }
        @@@this->db->insert_batch(@@@r_table, @@@data);        
      }
    }

    function getSelectedIds(@@@table, @@@id, @@@select_field, @@@where_field)
    {
        @@@arr=array();
        @@@this->db->select("@@@select_field");
        @@@this->db->from(@@@table);
        @@@this->db->where("@@@where_field",@@@id);
        @@@query = @@@this->db->get();
        @@@data = @@@query->result();
        foreach (@@@data as @@@key => @@@value) {
            @@@arr[] = @@@value->@@@select_field;
        }
        return @@@arr;
    }

    function updateData(@@@table, @@@data, @@@id)
    {
        @@@this->db->where("==primary_key==",@@@id);
        @@@this->db->update(@@@table,@@@data);
    }

    function delete(@@@table, @@@key='', @@@value='')
    {
        if(isset(@@@key) && isset(@@@value) && !empty(@@@key) && !empty(@@@value))
        {
            @@@this->db->where(@@@key,@@@value);
        }
        @@@this->db->delete(@@@table);
    }

    

public function uploadData(&@@@data, @@@file_name, @@@file_path, @@@postfix='', @@@allowedTypes)
{
   @@@config = NULL;
   @@@config['upload_path'] = @@@this->config->item(@@@file_path);  
   @@@config['allowed_types'] = @@@allowedTypes;
   if (isset(@@@_FILES[@@@file_name]['name']) && !empty(@@@_FILES[@@@file_name]['name']))
   {
    @@@this->load->library('upload', @@@config);
    @@@this->upload->initialize(@@@config);
    @@@exts = explode(".",@@@_FILES[@@@file_name]['name']);
    @@@_FILES[@@@file_name]['name'] = @@@exts[0].@@@postfix.".".end(@@@exts);
    if ( ! @@@this->upload->do_upload(@@@file_name))
    {
     @@@data[@@@file_name.'_err'] = array('error' => @@@this->upload->display_errors());
    }
    else
    {
     @@@uploadImg = @@@this->upload->data();
     if(@@@uploadImg['file_name'] != '')
    {
     if (isset(@@@_POST['old_'.@@@file_name]) && !empty(@@@_POST['old_'.@@@file_name]))
     {
      @unlink(@@@this->config->item(@@@file_path).@@@_POST['old_'.@@@file_name]);
     }
     @@@data[@@@file_name] = @@@uploadImg['file_name'];
    }
   } 
  }
  elseif (isset(@@@_POST['old_'.@@@file_name]) && !empty(@@@_POST['old_'.@@@file_name]))
  {
   @@@data[@@@file_name] = @@@_POST['old_'.@@@file_name];
  }   
}

}

