<?php
    include_once('simple_html_dom.php');
    
    
    function getJobListFromURL($url) {
        
        // The list of jobs
        $jobList = array();
        
        // Get the site content as a html object
        $html = file_get_html($url);
        
        // Loop through all the jobs
        for($i = 0; ; $i++) {
            
            // Get the job title
            $title = $html->find('.item-title', $i);

			// Get the faculty and departments
			$department = $html->find('.department', $i);
			$topDepartmentName = $html->find('.topDepartmentName', $i);
			
			// Get the deadline for the job
			$deadline = $html->find('.deadline', $i);
			
			if(isset($title)) {
			    
			    $job = array();
			    
			    $job['uid'] = getJobId($title->href);
				$job['title'] = $title->innertext;
				$job['description'] = getDescription($title->href);
				$job['location'] = 'Oslo';
				$unit = '';
				if(isset($topDepartmentName)){
					$unit = str_replace('Department: ', '', $department->innertext).' '.str_replace('Faculty: ', '', $topDepartmentName->innertext);
				}else{
					$unit = str_replace('Department: ', '', $department->innertext);
				}
				$job['unit'] = $unit;
				$job['campus'] = 'University of Oslo';
				$job['deadline'] = getFormattedDeadline($deadline->innertext);
				$job['email'] = '';
				$job['phone'] = '';
				$job['url'] = $title->href;
				$job['source'] = 'uio.no';

				$jobList[] = $job;
			}else{
			    break;
			}
        }
        
        return $jobList;
    }
    
    function getFormattedDeadline($deadline){
		$deadlineString = str_replace('Deadline: ', '', $deadline);
		$deadlineArray = explode(',', $deadlineString);
		$year = trim($deadlineArray[2]);
		$month = '';
		$day = '';
		for($i = 1; $i < 13; $i++){
			if(strpos($deadlineArray[1], getMonth($i)) !== false){
				$month = $i;
				$day = trim(str_replace(getMonth($i), '', $deadlineArray[1]));
				break;
			}
		}

		if($month < 10){
			$month = '0'.$month;
		}

		if($day < 10){
			$day = '0'.$day;
		}

		$year = substr($year, 0, 4);
		
		$dateString = $month.'-'.$day.'-'.$year;

		$dateObj = DateTime::createFromFormat('m-d-Y', $dateString);

		if(!$dateObj){
			echo "Error parsing the date : ". $dateString;
			exit;
		}else{
			return $dateObj->format("Y-m-d");
		}
	}

	function getMonth($monthNumber){
		switch($monthNumber){
			case 1:
				return 'January';
			case 2:
				return 'February';
			case 3:
				return 'March';
			case 4:
				return 'April';
			case 5:
				return 'May';
			case 6:
				return 'June';
			case 7:
				return 'July';
			case 8:
				return 'August';
			case 9:
				return 'September';
			case 10:
				return 'October';
			case 11:
				return 'November';
			case 12:
				return 'December';
			default:
				return null;
		}
	}

	function getJobId($url){
		$urlList = explode('/', $url);
		return $urlList[count($urlList) - 2];
	}

	function getDescription($url){
		$jobId = getJobId($url);
		$url = "https://id.jobbnorge.no/api/joblisting?jobId=$jobId&languageId=2&v=23c18cc5-9a3a-4bc3-80db-c890ab4c9173";
		$data = json_decode(curlGET($url));

		if(isset($data->components[1]->text)){
			return $data->components[1]->text;
		}else{
			for($i = 0; $i < count($data->components); $i++){
				if(isset($data->components[$i]->heading)){
					if($data->components[$i]->heading == 'Job description'){
						if(isset($data->components[$i]->text)){
							return $data->components[$i]->text;
						}
					}
				}
			}
			return '';
		}
	}

	function curlGET($postUrl) {
		$cookie = 'cookie.txt';
		$timeout = 30;

        $_ch = curl_init(); // Initialising cURL session

        // Setting cURL options
        curl_setopt($_ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // Prevent cURL from verifying SSL certificate
        curl_setopt($_ch, CURLOPT_FAILONERROR, TRUE);   // Script should fail silently on error
        curl_setopt($_ch, CURLOPT_COOKIESESSION, TRUE); // Use cookies
        curl_setopt($_ch, CURLOPT_FOLLOWLOCATION, TRUE);    // Follow Location: headers
        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, TRUE);    // Returning transfer as a string
        curl_setopt($_ch, CURLOPT_COOKIEFILE, $cookie);    // Setting cookiefile
        curl_setopt($_ch, CURLOPT_COOKIEJAR, $cookie); // Setting cookiejar
        curl_setopt($_ch, CURLOPT_URL, $postUrl);   // Setting URL to POST to
        curl_setopt($_ch, CURLOPT_CONNECTTIMEOUT, $timeout);   // Connection timeout
        curl_setopt($_ch, CURLOPT_TIMEOUT, $timeout); // Request timeout

        $results = curl_exec($_ch); // Executing cURL session
        curl_close($_ch);   // Closing cURL session

        return $results;
    }
?>