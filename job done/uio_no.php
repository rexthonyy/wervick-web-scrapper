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
                
                $iCategoryID = 32;
                
                //Determine the category id
                if(
                    strpos($sUnit,'Archaeology Conservation and History')!==false||
                    strpos($sUnit,'Culture Studies and Oriental Languages')!==false||
                    strpos($sUnit,'Philosophy, Classics, History of Art and Ideas')!==false||
                    strpos($sUnit,'Literature, Area Studies and European Languages')!==false||
                    strpos($sUnit,'Linguistics and Scandinavian Studies')!==false||
                    strpos($sUnit,'Media and Communication')!==false||
                    strpos($sUnit,'Musicology')!==false||
                    strpos($sUnit,'Ibsen Studies')!==false||
                    strpos($sUnit,'Interdisciplinary Studies in Rhythm, Time and Motion (RITMO)')!==false||
                    strpos($sUnit,'Multilingualism in Society across the Lifespan')!==false||
                    strpos($sUnit,'The Norwegian Institute in Rome (Norwegian)')!==false||
                    strpos($sUnit,'Nordic')!==false||
                    strpos($sUnit,'Museum of Cultural History')!==false||
                    strpos($sUnit,'Historical Museum')!==false||
                    strpos($sUnit,'The Viking Ship Museum')!==false||
                    strpos($sUnit,'Natural History Museum')!==false||
                    strpos($sUnit,'Museum of University and Science History')!==false||
                    strpos($sUnit,'Humanities and Social Sciences Library')!==false
                )   
                {
                    $iCategoryID = 1;
                }
                if(
                    strpos($sUnit,'Criminology and the Sociology of Law')!==false||
                    strpos($sUnit,'Private Law')!==false||
                    strpos($sUnit,'Public and International Law')!==false||
                    strpos($sUnit,'Scandinavian Institute of Maritime Law')!==false||
                    strpos($sUnit,'European Law')!==false||
                    strpos($sUnit,'Norwegian Centre for Human Rights')!==false||
                    strpos($sUnit,'Sociology and Human Geography')!==false||
                    strpos($sUnit,'Political Science')!==false||
                    strpos($sUnit,'Psychology')!==false||
                    strpos($sUnit,'Social Anthropology')!==false||
                    strpos($sUnit,'Economics')!==false||
                    strpos($sUnit,'European Studies (Arena)')!==false||
                    strpos($sUnit,'the Study of Equality, Social Organization and Performance (ESOP)')!==false||
                    strpos($sUnit,'TIK Centre for Technology, Innovation and Culture')!==false||
                    strpos($sUnit,'Energy')!==false||
                    strpos($sUnit,'Social Sciences')!==false||
                    strpos($sUnit,'Theology')!==false||
                    strpos($sUnit,'Educational Sciences')!==false||
                    strpos($sUnit,'Teacher Education and School Research')!==false||
                    strpos($sUnit,'Special Needs Education')!==false||
                    strpos($sUnit,'Education')!==false||
                    strpos($sUnit,'CEMO - Centre for Educational Measurement')!==false||
                    strpos($sUnit,'Research, Innovation and Competence Development (FIKS)')!==false||
                    strpos($sUnit,'Quality in Nordic Teaching (Quint)')!==false||
                    strpos($sUnit,'Professional learning in Teacher education (ProTed)')!==false||
                    strpos($sUnit,'Law Library')!==false||
                    strpos($sUnit,'PluriCourts - Centre for the Study of the Legitimate Roles of the Judiciary in the Global Order')!==false
                )
                {
                    $iCategoryID = 3;
                }
                if(
                    strpos($sUnit,'Health and Society')!==false||
                    strpos($sUnit,'Basic Medical Sciences')!==false||
                    strpos($sUnit,'Clinical Medicine')!==false||
                    strpos($sUnit,'CanCell - Centre for Cancer Cell Reprogramming')!==false||
                    strpos($sUnit,'Centre for Molecular Medicine Norway (NCMM)')!==false||
                    strpos($sUnit,'Norwegian Centre for Mental Disorders Research (NORMENT)')!==false||
                    strpos($sUnit,'Life Science')!==false||
                    strpos($sUnit,'Sustainable Healthcare Education (SHE)')!==false||
                    strpos($sUnit,'Oral Biology')!==false||
                    strpos($sUnit,'Clinical Dentistry')!==false||
                    strpos($sUnit,'Library of medicine and science')!==false
                )
                {
                    $iCategoryId = 16;
                }
                if(
                    strpos($sUnit,'Biosciences')!==false||
                    strpos($sUnit,'Pharmacy')!==false||
                    strpos($sUnit,'Institute of Theoretical Astrophysics')!==false||
                    strpos($sUnit,'Physics')!==false||
                    strpos($sUnit,'Informatics')!==false||
                    strpos($sUnit,'Geosciences')!==false||
                    strpos($sUnit,'Chemistry')!==false||
                    strpos($sUnit,'Mathematics')!==false||
                    strpos($sUnit,'Technology Systems')!==false||
                    strpos($sUnit,'Earth Evolution and Dynamics (CEED)')!==false||
                    strpos($sUnit,'Biogeochemistry in the Anthropocene (CBA)')!==false||
                    strpos($sUnit,'Bioinformatics (SBI)')!==false||
                    strpos($sUnit,'Ecological and Evolutionary Synthesis (CEES)')!==false||
                    strpos($sUnit,'Materials Science and Nanotechnology (SMN)')!==false||
                    strpos($sUnit,'Entrepreneurship (SFE)')!==false||
                    strpos($sUnit,'Hylleraas Centre for Quantum Molecular Sciences')!==false||
                    strpos($sUnit,'Norwegian Centre for Science Education')!==false||
                    strpos($sUnit,'The Centre for Theoretical and Computational Chemistry (CTCC)')!==false||
                    strpos($sUnit,'Computing in Science Education (CCSE)')!==false||
                    strpos($sUnit,'Teaching and Learning in Science and Technology (KURT)')!==false||
                    strpos($sUnit,'The Njord Centre')!==false
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
                }else{
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