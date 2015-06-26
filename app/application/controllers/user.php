<?php
class User_Controller extends Base_Controller {

	/**
	 * Edit the user's settings
	 * /user/settings
	 *
	 * @return View
	 */
	public function get_settings()
	{
		return $this->layout->with('active', 'settings')->nest('content', 'user.settings', array(
			'user' => Auth::user()
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
	public function post_settings()
	{
        $avatar = '';
        if(\Laravel\Input::has_file('avatar')){
            $img = \Laravel\Input::file('avatar');
            if($img['size'] >100000 )
            {
                return Redirect::to('user/settings')
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_sizes_errors'));
            }

            $arr_size = (getimagesize($img['tmp_name']));
            if($arr_size[0] > 200 && $arr_size[1] > 200 )
            {
                return Redirect::to('user/settings')
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
                return Redirect::to('user/settings')
                    ->with_input()
                    ->with('notice-error', __('tinyissue.file_type_errors'));
            }

        }
        $settings = User\Setting::update_user_settings(Input::all(), Auth::user()->id,$avatar);



		if(!$settings['success'])
		{
			return Redirect::to('user/settings')
				->with_input()
				->with_errors($settings['errors'])
				->with('notice-error', __('tinyissue.we_have_some_errors'));
		}

		return Redirect::to('user/settings')
			->with('notice', __('tinyissue.settings_updated'));
	}

	/**
	 * Shows the user's assigned issues
	 * /user/issues
	 *
	 * @return View
	 */
	public function get_issues()
	{
		return $this->layout->with('active', 'issues')->nest('content', 'user.issues', array(
			'projects' => Project\User::users_issues(),
		));
	}

	/**
	 * Log the user out
	 * /user/logout
	 *
	 * @return Redirect
	 */
	public function get_logout()
	{
		Auth::logout();

		return Redirect::to('/');
	}

}