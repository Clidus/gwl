<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//$route['default_controller'] = 'pages/view';
$route['default_controller'] = 'blogs/home';

$route['blog/(:any)'] = 'blogs/post/$1';

$route['changelog'] = 'pages/view/changelog';

$route['register'] = 'auth/register';
$route['logout'] = 'auth/logout';
$route['login'] = 'auth/login';

$route['search'] = 'search';
$route['search/(:any)'] = 'search/index/$1';
$route['search/(:any)/(:any)'] = 'search/index/$1/$2';

$route['game/(:any)'] = 'games/view/$1';

$route['user/edit'] = 'users/edit';
$route['user/save'] = 'users/save';
$route['user/(:any)'] = 'users/view/$1';

$route['admin'] = 'admin/home';
$route['admin/blog/new'] = 'admin/newBlogPost';
$route['admin/blog/edit'] = 'admin/blogPostList';
$route['admin/blog/edit/(:any)'] = 'admin/editBlogPost/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */