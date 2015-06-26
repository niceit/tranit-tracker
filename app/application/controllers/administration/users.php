<?php

class Administration_Users_Controller extends Base_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'permission:administration');
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
	public function get_index()
	{
		return $this->layout->with('active', 'dashboard')->nest('content', 'administration.users.index', array(
			'roles' => Role::order_by('id', 'DESC')->get()
		));
	}

	public function get_add()
	{
		return $this->layout->with('active', 'dashboard')->nest('content', 'administration.users.add');
	}

	public function post_add()
	{
        $avatar = 'uploads/avatar/avatarDefault.png';
        if(\Laravel\Input::has_file('avatar')){
            $img = \Laravel\Input::file('avatar');


            if($img['size'] >100000 )
            {
                return Redirect::to('administration/users/add/')
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_sizes_errors'));
            }

            $arr_size = (getimagesize($img['tmp_name']));
            if($arr_size[0] > 200 && $arr_size[1] > 200 )
            {
                return Redirect::to('administration/users/add/')
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_size_errors'));
            }

            $destination = "../uploads/avatar/";
            $extensions = array('image/png','image/jpg','image/jpeg');
            if(in_array($img['type'],$extensions)){
                $name = md5($img['name'].(rand(11111,99999))).".".$this->extensions($img['type']);
                \Laravel\Input::upload('avatar',$destination, $name);
                $avatar = 'uploads/avatar/'.$name;
            }else{
                return Redirect::to('administration/users/add/')
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_type_errors'));
            }

        }

        $add = User::add_user(Input::all(),$avatar);

		if(!$add['success'])
		{
			return Redirect::to('administration/users/add/')
				->with_input()
				->with_errors($add['errors'])
				->with('notice-error', __('tinyissue.we_have_some_errors'));
		}

		return Redirect::to('administration/users')
			->with('notice', __('tinyissue.user_added'));
	}

	public function get_edit($user_id)
	{
		return $this->layout->with('active', 'dashboard')->nest('content', 'administration.users.edit', array(
			'user' => User::find($user_id)
		));
	}

	public function post_edit($user_id)
	{
        $avatar = '';
        if(\Laravel\Input::has_file('avatar')){
            $img = \Laravel\Input::file('avatar');
            if($img['size'] >100000 )
            {
                return Redirect::to('administration/users/edit/' . $user_id)
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_sizes_errors'));
            }

            $arr_size = (getimagesize($img['tmp_name']));
            if($arr_size[0] > 200 && $arr_size[1] > 200 )
            {
                return Redirect::to('administration/users/edit/' . $user_id)
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_size_errors'));
            }

            $destination = "../uploads/avatar/";
            $extensions = array('image/png','image/jpg','image/jpeg');
            if(in_array($img['type'],$extensions)){
                $name = md5($img['name'].(rand(11111,99999))).".".$this->extensions($img['type']);
                \Laravel\Input::upload('avatar',$destination, $name);
                $avatar = 'uploads/avatar/'.$name;
            }else{
                return Redirect::to('administration/users/edit/' . $user_id)
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_type_errors'));
            }

        }
		$update = User::update_user(Input::all(),$user_id,$avatar);

		if(!$update['success'])
		{
			return Redirect::to('administration/users/edit/' . $user_id)
				->with_input()
				->with_errors($update['errors'])
				->with('notice-error', __('tinyissue.we_have_some_errors'));
		}

		return Redirect::to('administration/users')
			->with('notice', __('tinyissue.user_updated'));
	}

	public function get_delete($user_id)
	{
		User::delete_user($user_id);

		return Redirect::to('administration/users')
				->with('notice', __('tinyissue.user_deleted'));
	}
}