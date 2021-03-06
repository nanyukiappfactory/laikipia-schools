<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Site extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("sites_model");
        $this->load->library('googlemaps');

    }
    public function index()
    {
        $where = 'school_id > 0 AND deleted = 0 ';
        $table = 'school';
        $map_config['apikey'] = 'AIzaSyAMfrWKiELcjgQDzNq1n3LTVMSQAXGSs6E';
        $map_config['center'] = '37.4419, -122.1419';
        $map_config['zoom'] = 'auto';
        // Initialize our map, passing in any map_config
        $this->googlemaps->initialize($map_config);

        $order = 'school.school_name';
        $order_method = 'ASC';
        //pagination
        $segment = 5;
        $this->load->library('pagination');
        $config['base_url'] = site_url() . 'client/schools/' . $order . '/' . $order_method;
        $config['total_rows'] = $this->sites_model->count_items($table, $where);
        // $config["total_rows"] = $this->friends_model->countAll();
        $config['uri_segment'] = $segment;
        $config['per_page'] = 10;
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
        $v_data['abouts'] = $this->sites_model->get_about_posts();
        //echo json_encode($v_data['abouts']->result());die();
        $v_data['get_donors'] = $this->sites_model->get_donations();
        $v_data['get_dignity_packs'] = $this->sites_model->get_donations();
        $donations = $this->sites_model->get_donation_totals();
        $v_data['news_items'] = $this->sites_model->get_new_items();
        $v_data['pictures'] = $this->sites_model->get_gallery_pictures();
        $v_data['schools'] = $this->sites_model->get_schools();
        $v_data['partners'] = $this->sites_model->get_partners();
        $v_data['slider'] = $this->sites_model->get_slider_posts();
        $v_data['allschools'] = $this->sites_model->get_all_schools();
        $v_data['map'] = $this->googlemaps->create_map();
        $project_donation_total = $project_target_total = $percentage_donated_total = 0;
        if ($donations->num_rows() > 0) {
            foreach ($donations->result() as $row) {
                $project_donation_total = $row->total_donated_amount;
                $project_target_total = $row->total_target_amount;
                $percentage_donated_total = round(($project_donation_total / $project_target_total) * 100);
            }
        }
        $v_data['project_donation_total'] = $project_donation_total;
        $v_data['project_target_total'] = $project_target_total;
        $v_data['percentage_donated_total'] = $percentage_donated_total;
        $data['content'] = $this->load->view('site/home/home', $v_data, true);

        $data['title'] = $this->sites_model->display_page_title();
        // $data['title'] = $this->sites_model->decode_web_name($web_name);
         $this->load->view("site/layouts/layout", $data);

    }
    public function all_schools()
    {

        $v_data['abouts'] = $this->sites_model->get_about_posts();
        $v_data['get_donors'] = $this->sites_model->get_donations();
        $v_data['get_dignity_packs'] = $this->sites_model->get_donations();
        $donations = $this->sites_model->get_donation_totals();
        $v_data['news_items'] = $this->sites_model->get_new_items();
        $v_data['pictures'] = $this->sites_model->get_gallery_pictures();
        $v_data['schools'] = $this->sites_model->get_schools();
        $v_data['partners'] = $this->sites_model->get_partners();
        $v_data['allschools'] = $this->sites_model->get_all_schools();
        //echo json_encode($v_data['allschools']->result());die();

        $project_donation_total = $project_target_total = $percentage_donated_total = 0;
        if ($donations->num_rows() > 0) {
            foreach ($donations->result() as $row) {
                $project_donation_total = $row->total_donated_amount;
                $project_target_total = $row->total_target_amount;
                $percentage_donated_total = round(($project_donation_total / $project_target_total) * 100);
            }
        }
        $v_data['project_donation_total'] = $project_donation_total;
        $v_data['project_target_total'] = $project_target_total;
        $v_data['percentage_donated_total'] = $percentage_donated_total;
        $data['content'] = $this->load->view('site/school/all_schools', $v_data, true);
        $data['title'] = $this->sites_model->display_page_title();
        $this->load->view("site/layouts/layout", $data);

    }

    public function single_school($school_name)
    {
        $school_name = preg_replace('/-/', ' ', $school_name);
        $v_data['school_name'] = $school_name;
        $v_data['get_donors'] = $this->sites_model->get_donations();
        $v_data['get_dignity_packs'] = $this->sites_model->get_donations();
        $donations = $this->sites_model->get_donation_totals();
        // $v_data['news_items'] = $this->sites_model->get_new_items();
        $v_data['pictures'] = $this->sites_model->get_gallery_pictures();
        //echo json_encode($v_data['pictures']->result());die();
        $v_data['allschools'] = $this->sites_model->get_all_schools();
        $v_data['schoolpictures'] = $this->sites_model->get_school_pictures();
        $v_data['singleschool'] = $this->sites_model->get_single_school($school_name);
        $project_donation_total = $project_target_total = $percentage_donated_total = 0;
        if ($donations->num_rows() > 0) {
            foreach ($donations->result() as $row) {

                $project_donation_total = $row->total_donated_amount;
                $project_target_total = $row->total_target_amount;
                $percentage_donated_total = round(($project_donation_total / $project_target_total) * 100);

            }
        }
        $v_data['project_donation_total'] = $project_donation_total;
        $v_data['project_target_total'] = $project_target_total;
        $v_data['percentage_donated_total'] = $percentage_donated_total;
        $data['content'] = $this->load->view('site/school/school_single', $v_data, true);
        $data['title'] = $this->sites_model->display_page_title();
        //  $data['title'] = $this->sites_model->decode_web_name($school_name);
        $this->load->view("site/layouts/layout", $data);

	}
	
    public function about()
    {
		$v_data['about_query'] = $this->sites_model->get_about_posts();
		
        $data['content'] = $this->load->view('site/about', $v_data, true);
        $data['title'] = $this->sites_model->display_page_title();
        $this->load->view("site/layouts/layout", $data);

    }
	
    public function contact()
    {
		$v_data['about_query'] = NULL;

        $data['content'] = $this->load->view('site/contact', $v_data, true);
        $data['title'] = $this->sites_model->display_page_title();
        $this->load->view("site/layouts/layout", $data);

    }
	
    public function blog_page()
    {
		$v_data['blog_query'] = $this->sites_model->get_blog_posts();
		
        $data['content'] = $this->load->view('site/blog/all_posts', $v_data, true);
        $data['title'] = $this->sites_model->display_page_title();
        $this->load->view("site/layouts/layout", $data);

    }
	
    public function blog_single()
    {
		$v_data['blog_query'] = NULL;
		
        $data['content'] = $this->load->view('site/blog/single_post', $v_data, true);
        $data['title'] = $this->sites_model->display_page_title();
        $this->load->view("site/layouts/layout", $data);

    }
}
