<?php
    ini_set('display_errors',0);
    ini_set('display_startup_errors',0);
    error_reporting(E_ALL);
    

    /*
    Plugin Name: UIO NO
    Plugin URI: 
    Description: uio.no crawler
    Version: 1.1
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
                $sApplyLink = $job['applyLink'];
                $sLocation = $job['location'];
                $sUnit = $job['unit'];
                $sCampus = $job['campus'];
                $sDate = $job['deadline'];
                $sEmail = $job['email'];
                $sPhone = $job['phone'];
                $sDetailLink = $job['url'];
                $sSource = $job['source'];
                
                $iCategoryID = 32;
                
                //Determine the category id
                if(
					stripos($sUnit,'Archaeology Conservation and History')!==false||
					stripos($sUnit,'Culture Studies and Oriental Languages')!==false||
					stripos($sUnit,'Philosophy, Classics, History of Art and Ideas')!==false||
					stripos($sUnit,'Literature, Area Studies and European Languages')!==false||
					stripos($sUnit,'Linguistics and Scandinavian Studies')!==false||
					stripos($sUnit,'Media and Communication')!==false||
					stripos($sUnit,'Musicology')!==false||
					stripos($sUnit,'Ibsen Studies')!==false||
					stripos($sUnit,'Interdisciplinary Studies in Rhythm, Time and Motion (RITMO)')!==false||
					stripos($sUnit,'Multilingualism in Society across the Lifespan')!==false||
					stripos($sUnit,'The Norwegian Institute in Rome (Norwegian)')!==false||
					stripos($sUnit,'Nordic')!==false||
					stripos($sUnit,'Museum of Cultural History')!==false||
					stripos($sUnit,'Historical Museum')!==false||
					stripos($sUnit,'The Viking Ship Museum')!==false||
					stripos($sUnit,'Natural History Museum')!==false||
					stripos($sUnit,'Museum of University and Science History')!==false||
					stripos($sUnit,'Humanities and Social Sciences Library')!==false
				)	
				{
					$iCategoryID = 1;
				}
				if(
					stripos($sUnit,'Criminology and the Sociology of Law')!==false||
					stripos($sUnit,'Private Law')!==false||
					stripos($sUnit,'Public and International Law')!==false||
					stripos($sUnit,'Scandinavian Institute of Maritime Law')!==false||
					stripos($sUnit,'European Law')!==false||
					stripos($sUnit,'Norwegian Centre for Human Rights')!==false||
					stripos($sUnit,'Sociology and Human Geography')!==false||
					stripos($sUnit,'Political Science')!==false||
					stripos($sUnit,'Psychology')!==false||
					stripos($sUnit,'Social Anthropology')!==false||
					stripos($sUnit,'Economics')!==false||
					stripos($sUnit,'European Studies (Arena)')!==false||
					stripos($sUnit,'the Study of Equality, Social Organization and Performance (ESOP)')!==false||
					stripos($sUnit,'TIK Centre for Technology, Innovation and Culture')!==false||
					stripos($sUnit,'Energy')!==false||
					stripos($sUnit,'Social Sciences')!==false||
					stripos($sUnit,'Theology')!==false||
					stripos($sUnit,'Educational Sciences')!==false||
					stripos($sUnit,'Teacher Education and School Research')!==false||
					stripos($sUnit,'Special Needs Education')!==false||
					stripos($sUnit,'Education')!==false||
					stripos($sUnit,'CEMO - Centre for Educational Measurement')!==false||
					stripos($sUnit,'Research, Innovation and Competence Development (FIKS)')!==false||
					stripos($sUnit,'Quality in Nordic Teaching (Quint)')!==false||
					stripos($sUnit,'Professional learning in Teacher education (ProTed)')!==false||
					stripos($sUnit,'Law Library')!==false||
					stripos($sUnit,'PluriCourts - Centre for the Study of the Legitimate Roles of the Judiciary in the Global Order')!==false
			    )
				{
					$iCategoryID = 3;
				}
				if(
				    stripos($sUnit,'Health and Society')!==false||
				    stripos($sUnit,'Basic Medical Sciences')!==false||
				    stripos($sUnit,'Clinical Medicine')!==false||
				    stripos($sUnit,'CanCell - Centre for Cancer Cell Reprogramming')!==false||
				    stripos($sUnit,'Centre for Molecular Medicine Norway (NCMM)')!==false||
				    stripos($sUnit,'Norwegian Centre for Mental Disorders Research (NORMENT)')!==false||
				    stripos($sUnit,'Life Science')!==false||
				    stripos($sUnit,'Sustainable Healthcare Education (SHE)')!==false||
				    stripos($sUnit,'Oral Biology')!==false||
				    stripos($sUnit,'Clinical Dentistry')!==false||
				    stripos($sUnit,'Library of medicine and science')!==false
				)
				{
				    $iCategoryId = 16;
				}
				if(
					stripos($sUnit,'Biosciences')!==false||
					stripos($sUnit,'Pharmacy')!==false||
					stripos($sUnit,'Institute of Theoretical Astrophysics')!==false||
					stripos($sUnit,'Physics')!==false||
					stripos($sUnit,'Informatics')!==false||
					stripos($sUnit,'Geosciences')!==false||
					stripos($sUnit,'Chemistry')!==false||
					stripos($sUnit,'Mathematics')!==false||
					stripos($sUnit,'Technology Systems')!==false||
					stripos($sUnit,'Earth Evolution and Dynamics (CEED)')!==false||
					stripos($sUnit,'Biogeochemistry in the Anthropocene (CBA)')!==false||
					stripos($sUnit,'Bioinformatics (SBI)')!==false||
					stripos($sUnit,'Ecological and Evolutionary Synthesis (CEES)')!==false||
					stripos($sUnit,'Materials Science and Nanotechnology (SMN)')!==false||
					stripos($sUnit,'Entrepreneurship (SFE)')!==false||
					stripos($sUnit,'Hylleraas Centre for Quantum Molecular Sciences')!==false||
					stripos($sUnit,'Norwegian Centre for Science Education')!==false||
					stripos($sUnit,'The Centre for Theoretical and Computational Chemistry (CTCC)')!==false||
					stripos($sUnit,'Computing in Science Education (CCSE)')!==false||
					stripos($sUnit,'Teaching and Learning in Science and Technology (KURT)')!==false||
					stripos($sUnit,'The Njord Centre')!==false
			    )
				{
					$iCategoryID = 9;
				}
				
				$sChSQL = 'SELECT count(*) as iRec FROM `jobs` WHERE `uid`="'.$sUID.'"';
                $oCheck = $wpdb->get_row( $wpdb->prepare($sChSQL,'') );
                    
                if(
                    $oCheck->iRec==0 && (
                        stripos($sTitle,'phd')!==false||
                        stripos($sTitle,'postdoc')!==false||
                        stripos($sTitle,'researcher')!==false||
                        stripos($sTitle,'Ph.D.')!==false||
                        stripos($sTitle,'PhD')!==false||
                        stripos($sTitle,'doc')!==false
                    )
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
                    $sDescription .= "<br /><a target=\"_blank\" href='".$sApplyLink."'>Apply for job</a> ";
                    
                    $aPost = array(
                         'post_status' => 'delete',
						 'post_title' => $sTitle,
						 'post_content' => $sDescription,
						 'post_category' => array($iCategoryID),
						 'post_author' => 1
					  );

					$iPostID = wp_insert_post($aPost);

					wp_set_post_tags($iPostID,'UiO');
					$opts = array();
					$opts['expireType'] = 'draft';
					$opts['id'] = $iPostID;
					$tExpiredTime = strtotime($sDate);
					$tExpiredTime = date('Y-m-d 23:59:00',$tExpiredTime);
					$tExpiredTime = get_gmt_from_date($tExpiredTime,'U');
					_scheduleExpiratorEvent($iPostID,$tExpiredTime,$opts);
                }else{
                    foreach($wpdb->get_results("SELECT * FROM jobs WHERE title LIKE '".addslashes($sTitle)."';") as $key => $row){
                        $sql = "UPDATE jobs SET description = '".addslashes($sDescription)."' WHERE id=".$row->id;
                        $wpdb->query($sql);
                    }
                    
                    $post_id = get_page_by_title($sTitle)->ID;
                    if($post_id != null){
                        $current_post = get_post( $post_id, 'ARRAY_A' );
                        $current_post['post_content'] = $sDescription;
                        $current_post['post_category'] = array($iCategoryID);
                        $current_post['post_status'] = 'delete';
                        $current_post['post_author'] = 1;
                        wp_update_post($current_post);
                    }
                }
                //     $fields = array();
                //     $fields['description'] = addslashes($sDescription);

                //     $conditions = array();
                //     $conditions['title'] = addslashes($sTitle);
                //     $conditions['uid'] = addslashes($sUID);

                //     $wpdb::update('jobs', $fields, $conditions);

                //     $iPostID = get_page_by_title($sTitle)->ID;

                //     $aPost = array(
                //             'ID' => $iPostID,
                //             'post_content' => $sDescription
                //     );

                //     wp_update_post($aPost);
                // }
            }
    	}
    }
    
    uio_no_load();
    
?>
