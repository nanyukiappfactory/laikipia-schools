<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Schools extends MX_Controller
{
    public $upload_path;
    public $upload_location;

    public function __construct()
    {
        parent::__construct();
        $this->upload_path = realpath(APPPATH . '../assets/uploads');

        //get the location to upload images
        $this->upload_location = base_url() . 'assets/uploads';
        $this->load->model("laikipiaschools/schools_model");
        $this->load->library("image_lib");
        $this->load->library('googlemaps');
        $this->load->model("laikipiaschools/schools_model");
        $this->load->model("laikipiaschools/site_model");
        $this->load->model("laikipiaschools/file_model");

    }
   
    public function index($start = null)
    {
        $where = 'school_id > 0 AND deleted = 0 ';
        $table = 'school';
        $school_search = $this->session->userdata('schools_search');
        $search_title = $this->session->userdata('schools_search_title');

        if (!empty($school_search) && $school_search != null) {
            $where .= $school_search;
            // var_dump($where);die();
        }

        // This is our Google API key. You will need to change this for your own website
        $map_config['apikey'] = 'AIzaSyAMfrWKiELcjgQDzNq1n3LTVMSQAXGSs6E';
        $map_config['center'] = '37.4419, -122.1419';
        $map_config['zoom'] = 'auto';
        // Initialize our map, passing in any map_config
        $this->googlemaps->initialize($map_config);

        $order = 'school.school_name';
        $order_method = 'ASC';
        $this->form_validation->set_rules("school_name", "School Name", "required");
        $this->form_validation->set_rules("school_write_up", "school Write Up", "required");
        $this->form_validation->set_rules("school_boys_number", "Number of Boys", "required");
        $this->form_validation->set_rules("school_girls_number", "Number of Girls", "required");
        $this->form_validation->set_rules("school_location_name", "Location", "required");
        $this->form_validation->set_rules("school_zone", "School Zone", "required");
        $this->form_validation->set_rules("school_latitude", "Latitude", "required");
        $this->form_validation->set_rules("school_longitude", "Longitude", "required");

        $form_errors = "";
        if ($this->form_validation->run()) {

            $resize = array(
                "width" => 600,
                "height" => 600,
            );

            if (isset($_FILES['school_image']) && $_FILES['school_image']['size'] > 0) {
                $upload_response = $this->file_model->upload_image($this->upload_path, "school_image", $resize);
                // var_dump($upload_response);die();

                if ($upload_response['check'] == false) {
                    $this->session->set_flashdata('error', $upload_response['message']);
                    redirect('laikipiaschools/schools');
                } else {
                    if ($this->schools_model->add_school($upload_response['file_name'], $upload_response['thumb_name'])) {
                        $this->session->set_flashdata('success', 'school Added successfully!!');
                        redirect('laikipiaschools/schools');
                    } else {
                        $this->session->flashdata("error", "Unable to add  school");
                    }
                }

            } else {
                if ($this->schools_model->add_school(null, null)) {
                    $this->session->set_flashdata('success', 'school Added successfully!!');
                    redirect('laikipiaschools/schools');
                } else {
                    $this->session->flashdata("error", "Unable to add  school");
                }

            }

        } else {
            if (!empty(validation_errors())) {
                $this->session->set_flashdata("form_inputs", array(
                    'school_name' => $this->input->post('school_name'),
                    'school_boys_number' => $this->input->post('school_boys_number'),
                    'school_girls_number' => $this->input->post('school_girls_number'),
                    'school_location_name' => $this->input->post('school_location_name'),
                    'school_zone' => $this->input->post('school_zone'),
                    'school_latitude' => $this->input->post('school_latitude'),
                    'school_longitude' => $this->input->post('school_longitude'),
                    'school_write_up' => $this->input->post('school_write_up'),
                ));
                $this->session->set_flashdata("error", validation_errors());
                redirect('administration/add-school');
            } else {
                //pagination
                $segment = 5;
                $this->load->library('pagination');
                $config['base_url'] = site_url() . 'administration/schools/' . $order . '/' . $order_method;
                $config['total_rows'] = $this->site_model->count_items($table, $where);
                // $config["total_rows"] = $this->friends_model->countAll();
                $config['uri_segment'] = $segment;
                $config['per_page'] = 20;
                $config['num_links'] = 5;
                $config['full_tag_open'] = '<div class="pagging text-center"><nav aria-label="Page navigation example"><ul class="pagination">';
                $config['full_tag_close'] = '</ul></nav></div>';
                $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
                $config['num_tag_close'] = '</span></li>';
                $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
                $config['cur_tag_close'] = '<span class="sr-only">(current)</span></span></li>';
                $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
                $config['next_tagl_close'] = '<span aria-hidden="true">&raquo;</span></span></li>';
                $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
                $config['prev_tagl_close'] = '</span></li>';
                $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';
                $config['first_tagl_close'] = '</span></li>';
                $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';
                $config['last_tagl_close'] = '</span></li>';

                $this->pagination->initialize($config);
                $page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
                $v_data["links"] = $this->pagination->create_links();
                $query = $this->schools_model->get_all_schools($table, $where, $start, $config["per_page"], $page, $order, $order_method);

                //change of order method
                if ($order_method == 'DESC') {
                    $order_method = 'ASC';
                } else {
                    $order_method = 'DESC';
                }

                $data['title'] = 'Lakipia Schools';

                if (!empty($search_title) && $search_title != null) {
                    $data['title'] = 'school filtered by ' . $search_title;
                }
                $v_data['title'] = $data['title'];

                $v_data['order'] = $order;
                $v_data['order_method'] = $order_method;
                $v_data['query'] = $query;
                $v_data['other_images'] = ($this->schools_model->get_other_images());
                $v_data['categories'] = $this->site_model->get_all_categories();
                $v_data['pictures'] = $this->schools_model->get_images();
                $v_data['page'] = $page;
                $v_data['schools'] = $this->schools_model->get_all_schools($table, $where, $start, $config["per_page"], $page, $order, $order_method);
                $v_data['map'] = $this->googlemaps->create_map();
                $school_array = array();
                // echo json_encode($v_data['pictures']->result());die();
                foreach ($v_data["schools"]->result() as $school) {
                    array_push($school_array, array(
                        'id' => $school->school_id,
                        'name' => $school->school_name,
                    ));
                }

                $v_data['search_options'] = $school_array;
                $v_data['route'] = 'schools';
                $data = array(
                    "title" => $this->site_model->display_page_title(),
                    'map' => $this->googlemaps->create_map(),
                    "content" => $this->load->view("schools/all_schools", $v_data, true),
                );
                // $v_data["all_schools"] = $this->schools_model->get_all_schools();

                $this->load->view("laikipiaschools/layouts/layout", $data);
            }
        }

    }
    public function search_schools()
    {
        $school_name = $this->input->post('search_param');
        $search_title = '';

        if (!empty($school_name)) {
            $search_title .= ' school ID <strong>' . $school_name . '</strong>';
            $school_name = ' AND school.school_name = "' . $school_name . '"';
            $search = $school_name;
            $this->session->set_userdata('schools_search', $search);
            $this->session->set_userdata('schools_search_title', $search_title);
        }

        redirect("administration/schools");
    }

    public function deactivate_school($school_id, $status_id)
    {
        if ($status_id == 1) {
            $new_school_status = 0;
            $message = 'Deactivated';
        } else {
            $new_school_status = 1;
            $message = 'Activated';
        }

        $result = $this->schools_model->change_school_status($school_id, $new_school_status);
        if ($result == true) {
            $this->session->set_flashdata('success', "school ID: " . $school_id . " " . $message . " successfully!");
        } else {
            $this->session->set_flashdata('error', "school ID: " . $school_id . " failed to " . $message);
        }

        redirect('administration/schools');
    }

    public function get_zones()
    {$this->load->model("schools_model");
        $data['zones'] = $this->schools_model->get_zones();
        $this->load->view('administration/all_schools', $data);
    }
    public function get_country_dropdownlist()
    {
        $data['school'] = $this->schools_model->get_location_dropdownlist();

        $this->load->view('Administration/all_schools', $data);
    }

    public function export_schools()
    {
        $order = 'school.school_id';
        $order_method = 'DESC';
        $where = 'school_id > 0';
        $table = 'school';
        $schools_search = $this->session->userdata('school_search');
        $search_title = $this->session->userdata('school_search_title');

        if (!empty($schools_search) && $schools_search != null) {
            $where .= $schools_search;
        }
        $title = 'Schools';

        if (!empty($search_title) && $search_title != null) {
            $title = 'schools filtered by ' . $search_title;
        }

        if ($this->site_model->export_results($table, $where, $order, $order_method, $title)) {
        } else {
            $this->session->set_userdata('error_message', "Unable to export results");
        }
    }

    public function singleSchool($school_id)
    {
        $school = $this->schools_model->get_single_school($school_id);
        if ($school->num_rows() > 0) {
            //  $age, $gender, $hobby
            $row = $school->row();
            $school_image_name = $row->school_image_name;
            $school_name = $row->school_name;
            $school_write_up = $row->school_write_up;
            $school_boys_number = $row->school_boys_number;
            $school_zone = $row->$row->school_zone;
            $school_girls_number = $row->school_girls_number;
            $school_location_name = $row->school_location_name;
            $school_latitude = $row->school_latitude;
            $school_longitude = $row->school_longitude;
            $school_status = $row->school_status;
            $data = array(
                'school_image_name' => $school_image_name,
                'school_name' => $school_name,
                'school_write_up' => $school_write_up,
                'school_boys_number' => $school_boys_number,
                'school_girls_number' => $school_girls_number,
                'school_location_name' => $school_location_name,
                'school_latitude' => $school_latitude,
                'school_longitude' => $school_longitude,
                'school_status' => $school_status,
            );
            $v_data["all_schools"] = $this->schools_model->get_single_school();

            $data = array(
                "title" => $this->site_model->display_page_title(),
                "content" => $this->load->view("schools/all_schools", $v_data, true),
            );
// $v_data["all_schools"] = $this->schools_model->get_all_schools();

            $this->load->view("laikipiaschools/layouts/layout", $data);

        } else {
            $this->session->set_flashdata("error_message, could not find your school");
            redirect("laikipiaschools/schools");
        }
        //  pass the date
    }
    public function add_school()
    {
        $this->form_validation->set_rules("school_name", "School Name", "required");
        $this->form_validation->set_rules("school_write_up", "school Write Up", "required");
        $this->form_validation->set_rules("school_boys_number", "Number of Boys", "required");
        $this->form_validation->set_rules("school_girls_number", "Number of Girls", "required");
        $this->form_validation->set_rules("school_location_name", "Location", "required");
        $this->form_validation->set_rules("school_zone", "School Zone", "school_zone");
        $this->form_validation->set_rules("school_latitude", "Latitude", "required");
        $this->form_validation->set_rules("school_longitude", "Longitude", "required");
        $this->form_validation->set_rules("school_status", "Status", "required");

        //  validate
        $form_errors = "";

        if ($this->form_validation->run()) {
            if ($upload_response['check'] == false) {
                $this->session->set_flashdata('error', $upload_response['message']);
            } else {
                if ($this->schools_model->add_school($upload_response['file_name'], $upload_response['thumb_name'])) {
                    $this->session->set_flashdata('success', 'School Added successfully!!');
                    redirect('laikipiaschools/add_school');
                } else {
                    $this->session->flashdata("error_message", "Unable to add  school");
                }

            }
            $school_id = $this->schools_model->add_school();
            if ($school_id > 0) {
                $this->session->flashdata("success_message", "New school ID" . $school_id . "has been added");

                redirect("laikipiaschools/add_school");
            } else {
                $this->session->flashdata("error_message", "Unable to add  school");
            }
        }
        $v_data['title'] = "add school";
        $data = array(
            "title" => $this->site_model->display_page_title(),
            "content" => $this->load->view("schools/add_school", $v_data, true),
        );
        // var_dump($data);die();
        // $this->load->view("schools/add_school", $data);

        $this->load->view("laikipiaschools/layouts/layout", $data);
    }

    public function edit_school($school_id)
    {

        $this->form_validation->set_rules("school_name", "School Name", "required");
        $this->form_validation->set_rules("school_write_up", "school Write Up", "required");
        $this->form_validation->set_rules("school_boys_number", "Number of Boys", "required");
        $this->form_validation->set_rules("school_girls_number", "Number of Girls", "required");
        $this->form_validation->set_rules("school_zone", "School Zone", "required");
        $this->form_validation->set_rules("school_location_name", "Location", "required");
        $this->form_validation->set_rules("school_latitude", "Latitude", "required");
        $this->form_validation->set_rules("school_longitude", "Longitude", "required");
        $form_errors = "";
        if ($this->form_validation->run()) {
            $resize = array(
                "width" => 600,
                "height" => 600,
            );
            if (isset($_FILES['school_image']) && $_FILES['school_image']['size'] > 0) {
                $upload_response = $this->file_model->upload_image($this->upload_path, "school_image", $resize);
                // var_dump($upload_response);die();
                if ($upload_response['check'] == false) {
                    $this->session->set_flashdata('error', $upload_response['message']);
                } else {
                    if ($this->schools_model->update_school($school_id, $upload_response['file_name'], $upload_response['thumb_name'])) {
                        $this->session->set_flashdata('success', 'school updated successfully!!');
                    } else {
                        $this->session->flashdata("error_message", "Unable to update  school");
                    }
                }
            } else {
                if ($this->schools_model->update_school($school_id)) {
                    $this->session->set_flashdata('success', 'school updated successfully!!');
                } else {
                    $this->session->flashdata("error", "Unable to update  school");
                }

            }

        }
        $query = $this->schools_model->get_single_school($school_id);
// echo json_encode($query->result());die();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $school = $row->school_id;
            $v_data["query"] = $query;
            $v_data["school_id"] = $school;
            $v_data['categories'] = $this->site_model->get_all_categories();
            $v_data['schools'] = $this->schools_model->get_schools();
            $data = array("title" => $this->site_model->display_page_title(),
                "content" => $this->load->view("schools/edit_school", $v_data, true));

            $this->load->view("laikipiaschools/layouts/layout", $data);

        } else {
            $this->session->set_flashdata("error", "Unable to update  school");
            redirect("administration/schools");
        }

    }
    public function single_school($school_id)
    {
        $v_data['query'] = $this->schools_model->get_single_school($school_id);
        $v_data['title'] = "View";
        $query = $this->schools_model->get_single_school($school_id);

        $data['content'] = $this->load->view('schools/single_school', $v_data, true);
        $data["fetch_data"] = $this->main_model->fetch_data();
        $this->load->view("administration/layouts/layout", $data);
    }
    public function delete_school($school_id)
    {
        if ($this->schools_model->delete_school($school_id)) {
            $this->session->set_flashdata('success', 'Deleted successfully');
            redirect('administration/schools');
        } else {
            $this->session->set_flashdata('error', 'Unable to delete, Try again!!');
            redirect('administration/schools');
        }
    }

    public function delete_school_image($school_image_id)
    {
        if ($this->schools_model->delete_school_image($school_image_id)) {
            $this->session->set_flashdata('success', 'Image deleted successfully');
            redirect('administration/schools');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete imaged!!');
            redirect('administration/schools');
        }
    }

    public function bulk_actions()
    {
        $this->form_validation->set_rules('action_name', 'Action', 'required');

        //if form conatins invalid data
        if ($this->form_validation->run()) {
            $school_id_array = $_POST['school_id'];
            $action_name = $this->input->post('action_name');

            $total_schools = count($school_id_array);

            if ($total_schools > 0) {
                foreach ($school_id_array as $key => $value) {
                    if ($action_name == "delete") {
                        $this->schools_model->delete_school($value);
                    } else {

                    }
                }

                $schools = "laikipiaschools/schools";
                if ($total_schools == 1) {
                    $schools = "school";
                }
                $this->session->set_userdata('success_message', $action_name . " of " . $total_schools . " " . $schools . "  successfull");
            } else {
                $this->session->set_userdata('error_message', "No schools have been selected");
            }
        } else {
            $validation_errors = validation_errors();
            if (!empty($validation_errors)) {
                $this->session->set_userdata("error_message", $validation_errors);
            }
        }

        redirect("laikipiaschools/schools");
    }
}