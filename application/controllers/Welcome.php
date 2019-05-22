<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->helper('url');
    }

    function index() {
        if (!$this->ion_auth->logged_in())
        {
            redirect('auth/login');
        }
        $this->load->view('welcome');
    }

    function delete_notification($id = '') {

        if (!$this->tank_auth->is_logged_in()) {

            redirect('/auth/login/');
        }

        $id = $_POST['note_id'];

        $data['user_id'] = $userid = $this->tank_auth->get_user_id();

        $data['username'] = $this->tank_auth->get_username();

        $data['email'] = $this->tank_auth->get_email();

        $data['groupid'] = $this->tank_auth->get_group();



        if (isset($id) and ! empty($id)) {



            $count = $this->generic->getList('notification', 'c', '', '', 'user_id', $userid, 'id', $id);

            if (isset($count) and ! empty($count)) {

                $this->generic->crud('notification', '', 'id', $id, 'delete');

                $this->session->set_flashdata('message', ' Notification Deleted Successfully !');

                redirect('welcome');
            } else {

                $this->session->set_flashdata('message', ' Invalid Id !');

                redirect('welcome');
            }
        } else {

            $this->session->set_flashdata('message', ' Invalid Id !');

            redirect('welcome');
        }
    }

    function common_delete($id = '', $table) {

        if (!$this->tank_auth->is_logged_in()) {

            redirect('/auth/login/');
        }

        $id = $_POST['id'];

        $data['user_id'] = $userid = $this->tank_auth->get_user_id();



        if (isset($id) and ! empty($id)) {

            $count = $this->generic->getList($table, 'c', '', '', '', '', 'id', $id);

            if (isset($count) and ! empty($count)) {

                $this->generic->crud($table, '', 'id', $id, 'delete');
            }
        }
    }

}

/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */