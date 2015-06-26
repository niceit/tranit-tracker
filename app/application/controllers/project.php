<?php

class Project_Controller extends Base_Controller {

	public $layout = 'layouts.project';

	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'project');
		$this->filter('before', 'permission:project-modify')->only('edit');
	}

	/**
	 * Display activity for a project
	 * /project/(:num)
	 *
	 * @return View
	 */
	public function get_index()
	{
		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/activity', array(
				'project' => Project::current(),
				'activity' => Project::current()->activity(10)
			)),
			'active' => 'activity',
			'open_count' => Project::current()->issues()
				 ->where('status', '=', 1)
				 ->count(),
			'closed_count' => Project::current()->issues()
				 ->where('status', '=', 0)
				 ->count(),
			'assigned_count' => Project::current()->count_assigned_issues()
		));
	}

	/**
	 * Display issues for a project
	 * /project/(:num)
	 *
	 * @return View
	 */
	public function get_issues()
	{
		$status = Input::get('status', 1);
		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/issues', array(
				'issues' => Project::current()->issues()
				->where('status', '=', $status)
				->order_by('updated_at', 'DESC')
				->get(),
			)),
			'active' => $status == 1 ? 'open' : 'closed',
			'open_count' => Project::current()->issues()
				->where('status', '=', 1)
				->count(),
			'closed_count' => Project::current()->issues()
				->where('status', '=', 0)
				->count(),
			'assigned_count' => Project::current()->count_assigned_issues()
		));
	}

	/**
	 * Display issues assigned to current user for a project
	 * /project/(:num)
	 *
	 * @return View
	 */
	public function get_assigned()
	{
		$status = Input::get('status', 1);

		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/issues', array(
				'issues' => Project::current()->issues()
					->where('status', '=', $status)
					->where('assigned_to', '=', Auth::user()->id)
					->order_by('updated_at', 'DESC')
					->get(),
			)),
			'active' => 'assigned',
			'open_count' => Project::current()->issues()
				->where('status', '=', 1)
				->count(),
			'closed_count' => Project::current()->issues()
				->where('status', '=', 0)
				->count(),
			'assigned_count' => Project::current()->count_assigned_issues()
		));
	}

	/**
	 * Edit the project
	 * /project/(:num)/edit
	 *
	 * @return View
	 */
	public function get_edit()
	{
		return $this->layout->nest('content', 'project.edit', array(
			'project' => Project::current()
		));
	}
    public function  extensions($type){
        $temp = '';
        switch($type){
            case 'image/png': $temp = 'png';   break;
            case 'image/jpg': $temp = 'jpg';   break;
            case 'image/jpeg': $temp = 'jpeg';   break;
        }
        return $temp;
    }

	public function post_edit()
	{
		/* Delete the project */
		if(Input::get('delete'))
		{
			Project::delete_project(Project::current());

			return Redirect::to('projects')
				->with('notice', __('tinyissue.project_has_been_deleted'));
		}

        $image = '';
        if(\Laravel\Input::has_file('image')){
            $img = \Laravel\Input::file('image');
            if($img['size'] >100000 )
            {
                return Redirect::to(Project::current()->to('edit'))
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_sizes_errors'));
            }

            $arr_size = (getimagesize($img['tmp_name']));
            if($arr_size[0] > 200 && $arr_size[1] > 200 )
            {
                return Redirect::to(Project::current()->to('edit'))
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_size_errors'));
            }

            $destination = "../uploads/project/";
            $extensions = array('image/png','image/jpg','image/jpeg');
            if(in_array($img['type'],$extensions)){
                $name = md5($img['name'].(rand(11111,99999))).".".$this->extensions($img['type']);
                \Laravel\Input::upload('image',$destination, $name);
                $image = 'uploads/project/'.$name;
            }else{
                return Redirect::to(Project::current()->to('edit'))
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_type_errors'));
            }

        }

		/* Update the project */
		$update = Project::update_project(Input::all(), Project::current(),$image);

		if($update['success'])
		{
			return Redirect::to(Project::current()->to('edit'))
				->with('notice', __('tinyissue.project_has_been_updated'));
		}

		return Redirect::to(Project::current()->to('edit'))
			->with_errors($update['errors'])
			->with('notice-error', __('tinyissue.we_have_some_errors'));
	}
}