<?php 

class Blog extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // add blog post
    function add($title, $url, $post, $userID, $deck, $image, $youTube)
    {
        $post = array(
            'Title' => $title,
            'URL' => $url,
            'Post' => $post,
            'UserID' => $userID,
            'Date' => date('Y-m-d H:i:s'),
            'Deck' => $deck,
            'Image' => $image,
            'YouTube' => $youTube
        );

        return $this->db->insert('blog', $post); 
    }

    // update blog post
    function update($postID, $title, $url, $post, $deck, $image, $youTube)
    {
        $post = array(
            'Title' => $title,
            'URL' => $url,
            'Post' => $post,
            'Deck' => $deck,
            'Image' => $image,
            'YouTube' => $youTube
        );

        $this->db->where('PostID', $postID);
        $this->db->update('blog', $post); 
    }

    // update blog post
    function delete($postID)
    {
        $this->db->where('PostID', $postID);
        $this->db->delete('blog'); 
    }

    // get recent blog posts
    function getPosts($numberOfPosts)
    {
        $this->db->select('*');
        $this->db->from('blog');
        $this->db->join('users', 'blog.UserID = users.UserID');
        $this->db->order_by("Date desc, PostID desc"); 
        $this->db->limit($numberOfPosts);
        return $this->db->get()->result();
    }

    // get post by URL
    function getPostByURL($URL)
    {
        $this->db->select('*');
        $this->db->from('blog');
        $this->db->join('users', 'blog.UserID = users.UserID');
        $this->db->where('URL', $URL); 
        $query = $this->db->get();

        if ($query->num_rows() == 1)
            return $query->first_row();
        else
            return null;
    }

    // get post by PostID
    function getPostByID($PostID)
    {
        $this->db->select('*');
        $this->db->from('blog');
        $this->db->join('users', 'blog.UserID = users.UserID');
        $this->db->where('PostID', $PostID); 
        $query = $this->db->get();

        if ($query->num_rows() == 1)
            return $query->first_row();
        else
            return null;
    }
}