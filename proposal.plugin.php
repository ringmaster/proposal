<?php

class ProposalPlugin extends Plugin
{
	public function action_plugin_activation( $plugin_file )
	{
		Post::add_new_type( 'proposal' );
	}

	public function action_plugin_deactivation( $plugin_file )
	{
		Post::deactivate_post_type( 'proposal' );
	}

	public function filter_post_type_display($type, $foruse) 
	{ 
		$names = array( 
			'proposal' => array(
				'singular' => _t( 'Proposal', 'proposal' ),
				'plural' => _t( 'Proposals', 'proposal' ),
			)
		); 
		return isset($names[$type][$foruse]) ? $names[$type][$foruse] : $type; 
	}

	public function configure()
	{
		$groups_array = array();
		foreach(UserGroups::get_all() as $group) {
			$groups_array[$group->id] = $group->name;
		}

		$form = new FormUI( 'proposal' );
		$form->append( new FormControlSelect('type', 'staff__group', 'Group To Use for Staff', $groups_array));
		$form->append( new FormControlSubmit('save', _t( 'Save' )));

		return $form;
	}

	public function action_init()
	{
		$this->add_template('proposal', dirname($this->get_file()) . '/proposal.php');
	}

	public function action_form_publish_proposal( $form, $post )
	{
		$users = Users::get_all();
		$client_options = array();
		foreach($users as $user) {
			if($user->client) {
				$client_options[$user->id] = $user->client->title . ' : ' . $user->displayname;
			}
		}
		$form->insert('content', new FormControlSelect('client_contact', $post, 'Client Contact', $client_options, 'admincontrol_select'));

		$group = UserGroups::get(array('id' => Options::get('staff__group'), 'fetch_fn' => 'get_row'));
		$user_options = array();
		foreach($group->users as $user) {
			$user_options[$user->id] = $user->displayname;
		}
		$form->insert('content', new FormControlSelect('staff', $post, 'Staff', $user_options, 'admincontrol_select'));
	}

	public function filter_post_client_contact($client, $post)
	{
		if(intval($post->info->client_contact) != 0) {
			$client = User::get($post->info->client_contact);
		}
		return $client;
	}

	public function filter_post_staff($staff, $post)
	{
		if(intval($post->info->staff) != 0) {
			$staff = User::get($post->info->staff);
		}
		return $staff;
	}

	public function filter_url_args($args, $post) {
		if($post instanceof Post && $post->typename == 'proposal') {
			$client_user = User::get_by_id($post->info->client_contact);
			$client = Post::get(array('id'=>$client_user->info->client, 'ignore_permissions'=>true));
			$args['client_slug'] = $client->slug;
		}
		return $args;
	}

	public function filter_default_rewrite_rules( $rules ) {

		$rules[] = array(
			'name'			=>	'display_proposal',
			'parse_regex'	=>	'%^(?P<client_slug>.+?)/(?P<slug>.+?)/?$%i',
			'build_str'		=>	'{$client_slug}/{$slug}',
			'handler'		=>	'UserThemeHandler',
			'action'		=>	'display_post',
			'priority'		=>	1,
			'description'	=>	'Display Proposal',
			'parameters' => serialize( array( 'require_match' => array('Posts', 'rewrite_match_type'), 'content_type'=>'proposal') ),
		);

		return $rules;
	}

}

?>
