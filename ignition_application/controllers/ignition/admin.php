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

class IG_Admin extends CI_Controller {
	
	// admin home
	public function home()
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Admin", "Admin");

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
		$data = $this->Page->create("Edit Blog Post", "Admin");

		// get blog posts
		$this->load->model('Blog');
		$data['posts'] = $this->Blog->getPosts(100); // get 100 most recent posts

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

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("New Blog Post", "Admin");
		$data['formSuccess'] = $this->form_validation->run();
		$data['formType'] = "new";

		// form validation
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('post', 'Post', 'trim|required|xss_clean');
		$this->form_validation->set_rules('deck', 'Deck', 'trim|required|xss_clean');
		$this->form_validation->set_rules('image', 'Image', 'trim|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

		if ($this->form_validation->run() == TRUE)
		{
			// try to upload new image
			$postImage = $this->uploadImage();
			// if no image uploaded, use value in form
	        if($postImage == null) $postImage = $this->input->post('image');

	        // add to db
			$this->load->model('Blog');
			$title = $this->input->post('title');
			$postID = $this->Blog->add($title, $this->getUrl($title), $this->input->post('post'), $data['sessionUserID'], $this->input->post('deck'), $postImage);

			header("location: " . base_url() . "admin/blog/edit/" . $postID);
		}

		// empty post object required (same view used for editing post)
		$post = new stdClass();
		$post->PostID = 0;
		$post->Title = $this->input->post('title');
		$post->Post = $this->input->post('post');
		$post->Deck = $this->input->post('deck');
		$post->Image = $this->input->post('image');
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
		$this->form_validation->set_rules('post', 'Post', 'trim|required|xss_clean');
		$this->form_validation->set_rules('deck', 'Deck', 'trim|required|xss_clean');
		$this->form_validation->set_rules('image', 'Image', 'trim|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

		if ($this->form_validation->run() == TRUE)
		{
	        // try to upload new image
			$postImage = $this->uploadImage();
			// if no image uploaded, use value in form
	        if($postImage == null) $postImage = $this->input->post('image');

	        // update db
			$this->load->model('Blog');
			$title = $this->input->post('title');
			$this->Blog->update($PostID, $title, $this->getUrl($title), $this->input->post('post'), $this->input->post('deck'), $postImage);
		}
		
		// get blog posts
		$this->load->model('Blog');
		$post = $this->Blog->getPostByID($PostID); 

		// fail if post doesnt exist
		if($post == null)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Edit \"" . $post->Title . "\"", "Admin");
		$data['formSuccess'] = $this->form_validation->run();
		$data['formType'] = "edit/" . $PostID;
		$data['post'] = $post;
		
		$this->load->view('templates/header', $data);
		$this->load->view('admin/blogPostEditor', $data);
		$this->load->view('templates/footer', $data);
	}

	private function uploadImage()
	{
		// configure file upload
        $config['upload_path'] = './images/blog/';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $this->load->library('upload', $config);

        // upload file
        if ($this->upload->do_upload())
        {
            // if successfull, return image file name
            $uploadData = $this->upload->data();
            return '/images/blog/' . $uploadData["file_name"];
        }
	}

	private function getUrl($title)
	{
		// build url
		$url = strtolower($title);							// lowercase
		$url = preg_replace('/[^a-z0-9]/', ' ', $url);		// remove special characters
		$url = preg_replace('/ +/', ' ', $url);				// replace multiple spaces with single space
		$url = trim($url);									// trim leading/tailing space
		$url = preg_replace('/ /', '-', $url);				// replace spaces with hythen 

		return $url;
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