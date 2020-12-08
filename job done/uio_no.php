<?php
    ini_set('display_errors',0);
    ini_set('display_startup_errors',0);
    error_reporting(E_ALL);
    
    /*
    Plugin Name: UIO NO
    Plugin URI: 
    Description: uio.no crawler
    Version: 1.0
    Author: Rex Anthony
    Author Email: rexthonyy@gmail.com
    */
    
    define('UIO_NO_DIR', plugin_dir_path(__FILE__));
    define('UIO_NO_URL', plugin_dir_url(__FILE__));
    
    add_action('wp_loaded', 'getJobsFromSite');
    
    function uio_no_load(){
       require_once(UIO_NO_DIR.'includes/lib.php');
    }
    
    function uio_no_activate() {
    	global $wpdb;
    	$sCrSQL   = 'CREATE TABLE IF NOT EXISTS `jobs` (
    	  `id` int(11) NOT NULL AUTO_INCREMENT,
    	  `uid` varchar(255) NOT NULL,
    	  `title` varchar(255) NOT NULL,
    	  `description` mediumtext NOT NULL,
    	  `location` varchar(255) NOT NULL,
    	  `unit` varchar(255) NOT NULL,
    	  `campus` varchar(255) NOT NULL,
    	  `deadline` date NOT NULL,
    	  `email` varchar(255) NOT NULL,
    	  `phone` varchar(255) NOT NULL,
    	  `url` varchar(255) NOT NULL,
    	  `source` varchar(255) NOT NULL,
    	  PRIMARY KEY (`id`),
    	  UNIQUE KEY `uid_2` (`uid`),
    	  KEY `uid` (`uid`)
    	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;';
    	$wpdb->query($sCrSQL);
    }
    
    register_activation_hook( __FILE__, 'uio_no_activate' );
    
    function getJobsFromSite() {
        global $wpdb;
    	if(isset($_REQUEST['crawler'])&&$_REQUEST['crawler']=='run') {
    	    $sBaseURL = 'https://www.uio.no/english/about/jobs/vacancies/';
            $sJobs = getJobListFromURL($sBaseURL);
            
            // Iterate through the jobs
            foreach($sJobs as $job){
                $sUID = $job['uid'];
                $sTitle = $job['title'];
                $sDescription = $job['description'];
                $sLocation = $job['location'];
                $sUnit = $job['unit'];
                $sCampus = $job['campus'];
                $sDate = $job['deadline'];
                $sEmail = $job['email'];
                $sPhone = $job['phone'];
                $sDetailLink = $job['url'];
                $sSource = $job['source'];
                
                $iCategoryID = '';
                
                //Determine the category id
                if(
					strpos($sUnit,'Communication and Psychology')!==false||
					strpos($sUnit,'Culture and Global Studies')!==false||
					strpos($sUnit,'Learning and Philosophy')!==false
				)	
				{
					$iCategoryID = 1;
				}
				if(
					strpos($sUnit,'Design and Media Technology')!==false||
					strpos($sUnit,'Civil Engineering')!==false||
					strpos($sUnit,'Computer Science')!==false||
					strpos($sUnit,'Electronic Systems')!==false||
					strpos($sUnit,'Building Research Institute')!==false||
					strpos($sUnit,'Manufacturing Engineering')!==false
			    )
				{
					$iCategoryID = 7;
				}
				if(
					strpos($sUnit,'Business and Management')!==false||
					strpos($sUnit,'Department of Law')!==false||
					strpos($sUnit,'Political Science')!==false||
					strpos($sUnit,'Social Work')!==false
			    )
				{
					$iCategoryID = 3;
				}
				if(
					strpos($sUnit,'Chemistry and Bioscience')!==false||
					strpos($sUnit,'Clinical Medicine')!==false||
					strpos($sUnit,'Science and Technology')!==false
			    )	
				{
					$iCategoryID = 39;
				}
				if(
					strpos($sUnit,'Development and Planning')!==false||
					strpos($sUnit,'Mathematical Sciences')!==false||
					strpos($sUnit,'Physics and Nanotechnology')!==false||
					strpos($sUnit,'Energy Technology')!==false
			    )
				{
					$iCategoryID = 9;
				}
				
				$sChSQL = 'SELECT count(*) as iRec FROM `jobs` WHERE `uid`="'.$sUID.'"';
                $oCheck = $wpdb->get_row( $wpdb->prepare($sChSQL,'') );
                    
                if(
                    $oCheck->iRec==0 && (strpos($sTitle,'phd')!==false||strpos($sTitle,'postdoc')!==false||strpos($sTitle,'Ph.D.')!==false||strpos($sTitle,'PhD')!==false||(strpos($sTitle,'postdoc')!==false||strpos($sTitle,'doc')!==false))	
                )
                {
                    $sISQL  = 'INSERT INTO  `jobs` SET ';
                    $sISQL .= '`title`="'.addslashes($sTitle).'",';
                    $sISQL .= '`uid`="'.addslashes($sUID).'",';
                    $sISQL .= '`description`="'.addslashes($sDescription).'",';
                    $sISQL .= '`location`="'.addslashes($sLocation).'",';
                    $sISQL .= '`unit`="'.addslashes($sUnit).'",';
                    $sISQL .= '`campus`="'.addslashes($sCampus).'",';
                    $sISQL .= '`deadline`="'.addslashes($sDate).'",';
                    $sISQL .= '`email`="'.addslashes($sEmail).'",';
					$sISQL .= '`phone`="'.addslashes($sPhone).'",';
                    $sISQL .= '`source`="'.addslashes($sSource).'",';
                    $sISQL .= '`url`="'.addslashes($sDetailLink).'"';
                    $wpdb->query($sISQL);

                    $sDescription .= "<br /><strong>Deadline:</strong> ".date('j F Y',strtotime($sDate));
                    $sDescription .= "<br /><strong>Unit:</strong> ".$sUnit;
                    $sDescription .= "<br /><strong>Campus:</strong> ".$sCampus;
                    $sDescription .= "<br /><a target=\"_blank\" href='".$sDetailLink."'>Read the job description and apply online</a> ";
                    
                    $aPost = array(
						 'post_title' => $sTitle,
						 'post_content' => $sDescription,
						 'post_category' => array($iCategoryID),
						 'post_status' => 'draft',
						 'post_author' => 1
					  );

					$iPostID = wp_insert_post($aPost);

					wp_set_post_tags($iPostID,'UiO');
					$opts = array();
					$opts['expireType'] = 'draft';
					$opts['id'] = $iPostID;
					$tExpiredTime = strtotime($sDate);
					$tExpiredTime = date('Y-m-d 00:00:00',$tExpiredTime);
					$tExpiredTime = get_gmt_from_date($tExpiredTime,'U');
					_scheduleExpiratorEvent($iPostID,$tExpiredTime,$opts);
                }
            }
    	}
    }
    
    uio_no_load();
    
?>