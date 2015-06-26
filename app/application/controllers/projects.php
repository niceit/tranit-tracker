<?php

class Projects_Controller extends Base_Controller {

	public function get_index()
	{
		$status = Input::get('status', 1);
		$projects_active = Project\User::active_projects(true);
		$projects_inactive = Project\User::inactive_projects(true);

		return $this->layout->with('active', 'projects')->nest('content', 'projects.index', array(
			'projects' => $status == 1 ? $projects_active : $projects_inactive,
			'active' => $status == 1 ? 'active' : 'archived',
			'active_count' => (int) count($projects_active),
			'archived_count' => (int) count($projects_inactive)
		));
	}

	public function get_new()
	{
		Asset::script('project-new', '/app/assets/js/project-new.js', array('app'));

		return $this->layout->with('active', 'projects')->nest('content', 'projects.new');
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
	public function post_new()
	{
        $image = 'uploads/project/projectDefault.png';
        if(\Laravel\Input::has_file('image')){
            $img = \Laravel\Input::file('image');
            if($img['size'] >100000 )
            {
                return Redirect::to('projects/new')
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_sizes_errors'));
            }

            $arr_size = (getimagesize($img['tmp_name']));
            if($arr_size[0] > 200 && $arr_size[1] > 200 )
            {
                return Redirect::to('projects/new')
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
                return Redirect::to('projects/new')
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_type_errors'));
            }

        }
		$create = Project::create_project(Input::all(),$image);

		if($create['success'])
		{
			return Redirect::to($create['project']->to());
		}

		return Redirect::to('projects/new')
			->with_errors($create['errors'])
			->with('notice-error', __('tinyissue.we_have_some_errors'));
	}

}