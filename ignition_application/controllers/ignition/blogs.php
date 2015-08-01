<?php

/*
|--------------------------------------------------------------------------
| Ignition v0.4.0 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class is a core part of Ignition. It is advised that you extend
| this class rather than modifying it, unless you wish to contribute
| to the project.
|
*/

class IG_Blogs extends CI_Controller {
	
	// blog home
	public function home()
	{
		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Blog", "Blog");

		// get blog posts
		$this->load->model('Blog');
		$posts = $this->Blog->getPosts(10); // get 10 most recent posts

        $this->load->library('md');
        foreach($posts as $post)
        {
        	// transform markdown to HTML
	        $post->Post = $this->md->defaultTransform($post->Post);

	        // add label for number of comments to posts
	        switch($post->Comments)
			{
				case 0:
					$post->CommentsLabel = "No Comments";
					break;
				case 1:
					$post->CommentsLabel = "1 Comment";
					break;
				default:
					$post->CommentsLabel = $post->Comments . " Comments";
					break;
			} 
        }
		$data['recentPosts'] = $posts;
		
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

		if($post == null)
			show_404();

		// transform markdown to HTML
        $this->load->library('md');
        $post->Post = $this->md->defaultTransform($post->Post);

		// get comments
		$this->load->model('Comment');
        $post->comments = $this->Comment->getComments($post->PostID, 1, $this->session->userdata('DateTimeFormat')); // 1 = Blog Comment

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create($post->Title, "BlogPost");
		$data['post'] = $post;
		$data['recentPosts'] = $this->Blog->getPosts(10); // get 10 most recent posts

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
		$data['recentPosts'] = $this->Blog->getPosts(10); // get 10 most recent posts
		$months = $this->Blog->getMonthlyArchive(); // get monthly archive of blog posts

		// convert month number into month name
		foreach($months as $month)
		{
			$month->MonthName = $this->getMonthName($month->Month);
		}
		$data['months'] = $months;

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('blog/archive', $data);
		$this->load->view('blog/sidebar', $data);
		$this->load->view('templates/footer', $data);
	}

	// blog archive for a month
	public function month($year, $month)
	{
		if($month < 1 || $month > 12)
			show_404();

		// page title
		$title = $this->getMonthName($month) . " " . $year;

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create($title, "Blog");

		// get blog posts
		$this->load->model('Blog');
		$data['recentPosts'] = $this->Blog->getPosts(10); // get 10 most recent posts
		$data['title'] = $title;
		$posts = $this->Blog->getPostsForMonth($year, $month); // get monthly archive of blog posts

		if($posts == null)
			show_404();

		$data['posts'] = $posts;

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('blog/month', $data);
		$this->load->view('blog/sidebar', $data);
		$this->load->view('templates/footer', $data);
	}

    // get month name from number
    function getMonthName($monthNumber) {
        return DateTime::createFromFormat('!m', $monthNumber)->format('F');
    }
}