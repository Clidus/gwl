<?php

class Blogs extends CI_Controller {
	
	// blog home
	public function home()
	{
		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Home", "Home");

		// get blog posts
		$this->load->model('Blog');
		$data['posts'] = $this->Blog->getPosts(10); // get 10 most recent posts

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('blog/home', $data);
		$this->load->view('templates/footer', $data);
	}

	// blog post
	public function post($URL)
	{
		// get blog post
		$this->load->model('Blog');
		$post = $this->Blog->getPostByURL($URL); 

		if($post == null)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create($post->Title, "Home");
		$data['post'] = $post;

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('blog/post', $data);
		$this->load->view('templates/footer', $data);
	}
}