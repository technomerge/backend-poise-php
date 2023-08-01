<?php

class Data
{
	function load_suppliers($name)
	{
		include '../../../models/mysql/db_functions.php';
		
		return (get_suppliers_list($name));
	}
	
	function load_states($param)
	{
		include '../../../models/mysql/db_functions.php';
		
		return (get_states_list($param));
	}	
}

/* End of file Data.php */
/* Location: ./application/controllers/Data.php */

?>