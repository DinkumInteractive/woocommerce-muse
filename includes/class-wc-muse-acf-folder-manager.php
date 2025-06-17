<?php 

class WC_Muse_ACF_Folder_Manager {

	/*	Local path of JSON folder.
	 *
	 */
	private $path;

	/*	Field group post ID.
	 *
	 */
	private $field_group_ids;

	/*	Hook to fire when a field group is updating.
	 *
	 */
	public function __construct( $path, $field_group_ids ) {

		$this->path = $path;

		$this->field_group_ids = $field_group_ids;

		add_action( 'acf/update_field_group', array( $this, 'maybe_add_local_save_point' ), 1, 1 );

		add_filter( 'acf/settings/load_json', array( $this, 'add_local_load_point' ) );
	}

	/*	Check to see if it's the right group ID.
	 *
	 */
	public function maybe_add_local_save_point( $group ) {

		if ( in_array( $group['ID'], $this->field_group_ids ) )
			add_filter( 'acf/settings/save_json', array( $this, 'add_local_save_point' ), 99, 1 );
	}

	/*	Add local save folder.
	 *
	 */
	public function add_local_save_point( $path ) {

		$path = $this->path;

	    return $path;
	}

	/*	Add local load folder.
	 *
	 */
	public function add_local_load_point( $paths ) {
    
	    $paths[] = $this->path;

	    return $paths;
	}
}
