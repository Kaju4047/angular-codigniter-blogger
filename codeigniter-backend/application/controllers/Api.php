<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api_model');
		$this->load->helper('url');
		$this->load->helper('text');
	}

	public function getAllBlogPosts()
	{
		header("Access-Control-Allow-Origin: *");

		$blogs = $this->api_model->get_blogs();

		$posts = array();
		if(!empty($blogs)){
			foreach($blogs as $blog){

				$short_desc = strip_tags(character_limiter($blog->description, 70));
				$posts[] = array(
					'id' => $blog->id,
					'title' => $blog->title,
					'subtitle' => $blog->subtitle,
					'tag' => $blog->tag,
					'short_desc' => html_entity_decode($short_desc),
					'created_at' => $blog->created_at
				);
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($posts));
	}

	public function getPostsByTag($tag)
	{
		header("Access-Control-Allow-Origin: *");
		$this->db->where('tag',$tag);
		$blogs = $this->api_model->get_blogs();

		$posts = array();
		if(!empty($blogs)){
			foreach($blogs as $blog){

				$short_desc = strip_tags(character_limiter($blog->description, 70));
				$posts[] = array(
					'id' => $blog->id,
					'title' => $blog->title,
					'subtitle' => $blog->subtitle,
					'tag' => $blog->tag,
					'short_desc' => html_entity_decode($short_desc),
					'created_at' => $blog->created_at
				);
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($posts));
	}

	public function getPost($id)
	{
		header("Access-Control-Allow-Origin: *");
		
		$blog = $this->api_model->get_blog($id);

		$post = array(
			'id' => $blog->id,
			'title' => $blog->title,
			'subtitle' => $blog->subtitle,
			'tag' => $blog->tag,
			'description' => $blog->description,
			'created_at' => $blog->created_at
		);
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($post));
	}

	public function login() 
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
		header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

		$formdata = json_decode(file_get_contents('php://input'), true);

		$username = $formdata['username'];
		$password = $formdata['password'];

		$user = $this->api_model->login($username, $password);
		if($user) {
			$response = array(
				'user_id' => $user->id,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'token' => $user->token
			);
		}
		else {
			$response = array();
		}

		$this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
	}
	public function signup() 
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
		header('Access-Control-Allow-Headers: Accept,Accept-Language,Content-Language,Content-Type');

		$formdata = json_decode(file_get_contents('php://input'), true);
         // print_r($formdata);
		// $username = 'Kajal';
		// $email = 'K@gmail.com';
		// $password = '123';
		$username = $formdata['name'];
		$email = $formdata['username'];
		$password = $formdata['password'];

		$Data = array(
				'first_name' => $username,
				'username' => $email,
				'is_active' => 1,
				'token' => rand(10,100),
				'password' => md5($password),
				'created_at' => date('Y-m-d H:i:s', time())
			);
			
		$id = $this->api_model->insertData('users',$Data);

		if($id) {
			$response = array(
				'user_id' => $id,
			);
		}
		else {
			$response = array();
		}

		$this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
	}

	public function adminBlogs()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		$posts = array();
		if($isValidToken) {
			$blogs = $this->api_model->get_admin_blogs();
			foreach($blogs as $blog) {
				$posts[] = array(
					'id' => $blog->id,
					'title' => $blog->title,
					'subtitle' => $blog->subtitle,
					'tag' => $blog->tag,
					'created_at' => $blog->created_at
				);
			}

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($posts)); 
		}
	}

	public function adminBlog($id)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$blog = $this->api_model->get_admin_blog($id);

			$post = array(
				'id' => $blog->id,
				'title' => $blog->title,
				'subtitle' => $blog->subtitle,
				'tag' => $blog->tag,
				'description' => $blog->description,
				'is_active' => $blog->is_active
			);
			

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($post)); 
		}
	}

	public function createPost()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Request-Headers: GET,POST,OPTIONS,DELETE,PUT");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$title = $this->input->post('title');
			$description = $this->input->post('description');
			$tag = $this->input->post('tag');
			$subtitle = $this->input->post('subtitle');
	        	$blogData = array(
					'title' => $title,
					'subtitle' => $subtitle,
					'tag' => $tag,
					'user_id' => 1,
					'description' => $description,
					'is_active' =>'1',
					'created_at' => date('Y-m-d H:i:s', time())
				);

				$id = $this->api_model->insertBlog($blogData);

				$response = array(
					'status' => 'success'
				);

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($response)); 
		}
	}

	public function updatePost($id)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: authorization, Content-Type");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$blog = $this->api_model->get_admin_blog($id);

			$title = $this->input->post('title');
			$subtitle = $this->input->post('subtitle');
			$tag = $this->input->post('tag');
			$description = $this->input->post('description');
			
	        	$blogData = array(
					'title' => $title,
					'subtitle' => $subtitle,
					'tag' => $tag,
					'user_id' => 1,
					'description' => $description,
				);

				$this->api_model->updateBlog($id, $blogData);

				$response = array(
					'status' => 'success'
				);
			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($response)); 
		}
	}

	public function deletePost($id)
	{
		header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Allow-Headers: authorization, Content-Type");

		$token = $this->input->get_request_header('Authorization');

		$isValidToken = $this->api_model->checkToken($token);

		if($isValidToken) {

			$blog = $this->api_model->get_admin_blog($id);

			$this->api_model->deleteBlog($id);

			$response = array(
				'status' => 'success'
			);

			$this->output
				->set_status_header(200)
				->set_content_type('application/json')
				->set_output(json_encode($response)); 
		}
	}
}
