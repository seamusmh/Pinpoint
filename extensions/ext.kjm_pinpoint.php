<?php

class KJM_pinpoint
{
    var $settings        = array();
    
    var $name            = 'Pinpoint';
    var $version         = '1.0';
    var $description     = 'This extension adds the url_title field to the edit entries page allowing you to find entries with similar titles but varied urls';
    var $settings_exist  = 'n';
    var $docs_url        = '';//'http://expressionengine.com';

    // -------------------------------
    //   Constructor - Extensions use this for settings
    // -------------------------------
    
    function KJM_pinpoint($settings='')
    {
        $this->settings = $settings;
    }
    // END
    
    // --------------------------------
    //  Activate Extension
    // --------------------------------

    function activate_extension()
    {
        global $DB;

        $DB->query($DB->insert_string('exp_extensions',
        array(
            'extension_id' => '',
            'class'        => "KJM_pinpoint",
            'method'       => "add_table_header",
            'hook'         => "edit_entries_additional_tableheader",
            'settings'     => "",
            'priority'     => 10,
            'version'      => $this->version,
            'enabled'      => "y"
            )
            )
            );
        $DB->query($DB->insert_string('exp_extensions',
        array(
            'extension_id' => '',
            'class'        => "KJM_pinpoint",
            'method'       => "add_url_title_column",
            'hook'         => "edit_entries_additional_celldata",
            'settings'     => "",
            'priority'     => 10,
            'version'      => $this->version,
            'enabled'      => "y"
            )
            )
            );
    }
    // END
    
    // --------------------------------
    //  Update Extension
    // --------------------------------  

    function update_extension($current='')
    {
        global $DB;
        

        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }

        $DB->query("UPDATE exp_extensions 
                    SET version = '".$DB->escape_str($this->version)."' 
                    WHERE class = 'KJM_pinpoint'");
    }
    // END
    
    //---------------------------------
    // Adds a table header for url_title
    //---------------------------------
    
    function add_table_header(){
    	global $EXT, $DSP, $LANG;

        
    	if ($EXT->last_call !== FALSE)
    	{
    		return $EXT->last_call;
    	}
        
        
    	return "<td class='tableHeadingAlt'>\n".$LANG->line('url_title')."\n</td>";
       
    }
    
    //---------------------------------
    // Adds the url_title column
    //---------------------------------
    
    function add_url_title_column($row){
    	global $EXT, $DB, $row_counter;
        if(empty($row_counter)){
            $row_counter = 0;
        }
    	if ($EXT->last_call !== FALSE)
    	{
    		$row = $EXT->last_call;
    	}

       $query = $DB->query('SELECT url_title FROM exp_weblog_titles WHERE `entry_id` ='.$row['entry_id'].''); 
       $url_title = "";
       
       $style = $row_counter % 2 == 0 ? 'tableCellTwo': 'tableCellOne'; 
       
       if ($query->num_rows == 1)
       	{
       		foreach($query->result as $row)
       		{
       			$url_title = $row['url_title'];
       			$row_counter +=1;
       		}
       	}
       	

       return "<td class='".$style."'><div class='smallNoWrap'>".$url_title."</div></td>";
    }
    
    // --------------------------------
    //  Disable Extension
    // --------------------------------

    function disable_extension()
    {
        global $DB;

        $DB->query("DELETE FROM exp_extensions WHERE class = 'KJM_pinpoint'");
    }
    // END
    
}
// END CLASS