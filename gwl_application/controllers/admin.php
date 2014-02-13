<?php

class Admin extends CI_Controller {
	
	// admin home
	public function home()
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Admin");

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('admin/admin.php', $data);
		$this->load->view('templates/footer', $data);
	}

	// blog post list
	public function blogPostList()
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Edit Blog Post");

		// get blog posts
		$this->load->model('Blog');
		$data['posts'] = $this->Blog->getPosts(10); // get 10 most recent posts

		$this->load->view('templates/header', $data);
		$this->load->view('admin/blogPostList', $data);
		$this->load->view('templates/footer', $data);
	}

	// new blog post
	public function newBlogPost()
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		// form validation
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('url', 'URL', 'trim|required|xss_clean');
		$this->form_validation->set_rules('post', 'Post', 'trim|required|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("New Blog Post");
		$data['formSuccess'] = $this->form_validation->run();
		$data['formType'] = "new";

		if ($this->form_validation->run() == TRUE)
		{
			$this->load->model('Blog');
			$this->Blog->add($this->input->post('title'), $this->input->post('url'), $this->input->post('post'), $data['sessionUserID']);

			header("location: " . base_url() . "admin/blog/edit");
		}

		// empty post object required (same view used for editing post)
		$post = new stdClass();
		$post->PostID = 0;
		$post->Title = "";
		$post->URL = "";
		$post->Post = "";
		$data['post'] = $post;

		$this->load->view('templates/header', $data);
		$this->load->view('admin/blogPostEditor', $data);
		$this->load->view('templates/footer', $data);
	}

	// edit blog post
	public function editBlogPost($PostID)
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		// form validation
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('url', 'URL', 'trim|required|xss_clean');
		$this->form_validation->set_rules('post', 'Post', 'trim|required|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

		if ($this->form_validation->run() == TRUE)
		{
			$this->load->model('Blog');
			$this->Blog->update($PostID, $this->input->post('title'), $this->input->post('url'), $this->input->post('post'));
		}
		
		// get blog posts
		$this->load->model('Blog');
		$post = $this->Blog->getPostByID($PostID); 

		if($post == null)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Edit " . $post->Title);
		$data['formSuccess'] = $this->form_validation->run();
		$data['formType'] = "edit/" . $PostID;
		$data['post'] = $post;
		
		$this->load->view('templates/header', $data);
		$this->load->view('admin/blogPostEditor', $data);
		$this->load->view('templates/footer', $data);
	}

	public function deleteBlogPost()
	{
		// form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('postID', 'postID', 'trim|xss_clean');

		// restricted method
		if($this->session->userdata('Admin') != 1)
		{
			$result['error'] = true; 
            $result['errorMessage'] = "You don't have permission to do that duder."; 
            echo json_encode($result);
            return;
		} else {
			// delete blog post
			$this->load->model('Blog');
			$this->Blog->delete($this->input->post('postID')); 

			$result['error'] = false; 
            echo json_encode($result);
            return;
		}
	}
}