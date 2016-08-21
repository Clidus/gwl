<?php

/*
|--------------------------------------------------------------------------
| Ignition v0.5.0 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class is a core part of Ignition. It is advised that you extend
| this class rather than modifying it, unless you wish to contribute
| to the project.
|
*/

class IG_Admin_Blog extends CI_Controller {

	// blog post list
	public function get($page = 1)
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Edit Blog Post", "Admin");

		// get blog posts
		$resultsPerPage = 10;
		$offset = ($page-1) * $resultsPerPage;
		$this->load->model('Blog');
		$posts = $this->Blog->getPosts($resultsPerPage, $offset, true);

		// return 404, if not blog homepage and no posts found
		if($page != 1 && $posts == null)
			show_404();

		$data['posts'] = $posts;
		$data['page'] = $page;

		$this->load->view('templates/header', $data);
		$this->load->view('admin/blog/list', $data);
		$this->load->view('templates/footer', $data);
	}

	// new blog post
	public function create()
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("New Blog Post", "Admin");

		// form validation
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('post', 'Post', 'trim|required|xss_clean');
		$this->form_validation->set_rules('deck', 'Deck', 'trim|required|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

		if ($this->form_validation->run())
		{
	        // add to db
			$this->load->model('Blog');
			$title = $this->input->post('title');
			$postID = $this->Blog->add($title, $this->getUrl($title), $this->input->post('post'), $data['sessionUserID'], $this->input->post('deck'));

			header("location: " . base_url() . "admin/blog/edit/post/" . $postID);
		}

		$this->load->view('templates/header', $data);
		$this->load->view('admin/blog/new', $data);
		$this->load->view('templates/footer', $data);
	}

	// edit blog post
	public function edit($PostID)
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		$this->form_validation->set_rules('formType', 'Form Type', 'trim|required|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

		$errorMessage = '';
		$success = false;

		if($this->input->post('formType') == "post")
		{
			// form validation
			$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
			$this->form_validation->set_rules('post', 'Post', 'trim|required|xss_clean');
			$this->form_validation->set_rules('deck', 'Deck', 'trim|required|xss_clean');
			$this->form_validation->set_rules('image', 'Image', 'trim|xss_clean');
			$this->form_validation->set_rules('published', 'Published', 'trim|xss_clean');
			$this->form_validation->set_rules('date', 'Date', 'trim|xss_clean');
			$this->form_validation->set_rules('time', 'Time', 'trim|xss_clean');
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

			if ($this->form_validation->run())
			{
				// update db
				$this->load->model('Blog');
				$title = $this->input->post('title');
				$this->Blog->update($PostID, $title, $this->getUrl($title), $this->input->post('post'), $this->input->post('deck'), $this->input->post('image'), $this->input->post('published'), $this->input->post('date'), $this->input->post('time'));
				$success = true;
			}
		} 
		else if($this->input->post('formType') == "image")
		{
			// try to upload new image		
			$imageUpload = $this->uploadImage();

			// check if upload succeeded
			if(!$imageUpload->error)
			{
				// update db
				$this->load->model('Blog');
				$this->Blog->updateImage($PostID, $imageUpload->fileName);
				$success = true;
			} else {
				// return error
				$errorMessage = $imageUpload->errorMessage;
			}
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
		$data['formSuccess'] = $success;
		$data['postID'] = $PostID;
		$data['post'] = $post;
		$data['errorMessage'] = $errorMessage;
		
		$this->load->view('templates/header', $data);
		$this->load->view('admin/blog/edit', $data);
		$this->load->view('templates/footer', $data);
	}

	public function delete()
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

	private function uploadImage()
	{
		// configure file upload
		$config['upload_path'] = './images/blog/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$this->load->library('upload', $config);

		// initialise return object
		$result = new stdClass();

		// upload file
		if ($this->upload->do_upload())
		{
			// if successfull, return image file name
			$uploadData = $this->upload->data();

			$result->error = false;
			$result->fileName = '/images/blog/' . $uploadData["file_name"];
			
			return $result;
		} else {
			$result->error = true;
			$result->errorMessage = $this->upload->display_errors();
			
			return $result;
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
}