<?php

class Blogs extends CI_Controller {
	
	// blog home
	public function home()
	{
		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Blog", "Blog");

		// get blog posts
		$this->load->model('Blog');
		$posts = $this->Blog->getPosts(10); // get 10 most recent posts

		// transform markdown to HTML
        $this->load->library('md');
        foreach($posts as $post)
        {
	        $post->Post = $this->md->defaultTransform($post->Post);
        }
		$data['posts'] = $posts;
		
		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('blog/home', $data);
		$this->load->view('blog/sidebar', $data);
		$this->load->view('templates/footer', $data);
	}

	// blog post
	public function post($URL)
	{
		// get blog post
		$this->load->model('Blog');
		$post = $this->Blog->getPostByURL($URL); 

		// transform markdown to HTML
        $this->load->library('md');
        $post->Post = $this->md->defaultTransform($post->Post);
        
		if($post == null)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create($post->Title, "Blog");
		$data['post'] = $post;
		$data['posts'] = $this->Blog->getPosts(10); // get 10 most recent posts

		// add meta tags
		$data['metaTags'] = $this->Page->getBlogMetaTags($post);

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('blog/post', $data);
		$this->load->view('blog/sidebar', $data);
		$this->load->view('templates/footer', $data);
	}

	// blog archive
	public function archive()
	{
		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Blog Archive", "Blog");

		// get blog posts
		$this->load->model('Blog');
		$data['posts'] = $this->Blog->getPosts(10); // get 10 most recent posts

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('blog/archive', $data);
		$this->load->view('blog/sidebar', $data);
		$this->load->view('templates/footer', $data);
	}
}