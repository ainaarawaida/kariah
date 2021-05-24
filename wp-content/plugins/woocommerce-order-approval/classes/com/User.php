<?php 
namespace WCOA\classes\com;

class User
{
	public function __construct()
	{
		
	}
	//rplc: woocommerce-order-approval, wcoa, WCOA
	public function get_roles_list()
	{
		 global $wp_roles;

		$all_roles = $wp_roles->roles;
		//$editable_roles = apply_filters('editable_roles', $all_roles);
		return $all_roles;
	}
	public function belogs_to_roles($roles)
	{
		if(!$roles || empty($roles) || !is_user_logged_in() )
			return false;
		
		global $current_user;
		foreach($roles as $index => $role_id)
		{
			if(in_array($role_id, $current_user->roles))
					return true;
		}
		
		return false;
	}
}

?>