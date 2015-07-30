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

class IG_Blog extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // add blog post
    function add($title, $url, $post, $userID, $deck, $image)
    {
        $post = array(
            'Title' => $title,
            'URL' => $url,
            'Post' => $post,
            'UserID' => $userID,
            'Date' => date('Y-m-d H:i:s'),
            'Deck' => $deck,
            'Image' => $image
        );

        $this->db->insert('blog', $post); 

        return $this->db->insert_id(); // return PostID
    }

    // update blog post
    function update($postID, $title, $url, $post, $deck, $image)
    {
        $post = array(
            'Title' => $title,
            'URL' => $url,
            'Post' => $post,
            'Deck' => $deck,
            'Image' => $image
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
        $this->db->select('blog.*, users.*, COUNT(comments.CommentID) AS Comments');
        $this->db->from('blog');
        $this->db->join('users', 'blog.UserID = users.UserID');
        $this->db->join('comments', 'comments.LinkID = blog.PostID AND comments.CommentTypeID = 1', 'left'); // 1 = Blog Comment
        $this->db->group_by("PostID"); 
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

    // get blog post archive
    function getMonthlyArchive()
    {
        $this->db->select('YEAR(Date) AS Year, MONTH(Date) AS Month');
        $this->db->from('blog');
        $this->db->group_by("YEAR(Date), MONTH(Date)"); 
        $this->db->order_by("YEAR(Date) desc, MONTH(Date) desc"); 
        return $this->db->get()->result();
    }

    // get blog posts for a month
    function getPostsForMonth($year, $month)
    {
        $this->db->select('*');
        $this->db->from('blog');
        $this->db->where('YEAR(Date)', $year); 
        $this->db->where('MONTH(Date)', $month); 
        $this->db->order_by("Date desc, PostID desc"); 
        return $this->db->get()->result();
    }
}