<?php
namespace Search\Aleph;

use Browse;
use User;

class Data
{
    public $hit;
    public $images;
    
    public function getMarc($response)
    {
	    $this->images = new Browse\CoverImages();
	    $user = new User();
        $result = array();
        $answer = $this->get_record_information($response, "//record");
        
        foreach ($answer as $item) {
            $newDom = new \DOMDocument;
            $newDom->appendChild($newDom->importNode($item, true));
            $this->xpath = new \DOMXPath($newDom);
            //Fields for Music
            $music_title     = $this->myget("//varfield[@id='245']/subfield[@label='a']");
            $music_title    .= ' ';
            $music_title    .= $this->myget("//varfield[@id='245']/subfield[@label='b']");
            $music_author =  rtrim($this->myget("//varfield[starts-with(@id, '1')]/subfield[@label='a']"), ".,");
           // $music_author2 = rtrim($this->myget("//varfield[@id='110']/subfield[@label='a']"), ".,");
           	$music_year = $this->myget("//varfield[@id='264']/subfield[@label='c']");
		   	$holdings_record = $this->myget("//varfield[@id='LKR']/subfield[@label='b']");
            
            $docid  = $this->myget("//doc_number");
            if(!empty($record_id)) {
            	$record_docid = $this->myget("//fixfield[@id='001']");
            } else {
	            $record_docid = $this->myget("//fixfield[@id='SYS']");
            }	
            $this->fixed_008 = $this->myget("//fixfield[@id=008]");
            //Get Title Data

            $title           = $this->myget("//varfield[@id='245']/subfield[@label='a']");
            $title .= $this->myget("//varfield[@id='245']/subfield[@label='p']");
            $title .= $this->myget("//varfield[@id='245']/subfield[@label='h']");
            $title .= $this->myget("//varfield[@id='245']/subfield[@label='b']");
            
            $sub_title = $this->myget("//varfield[@id='245']/subfield[@label='h']");
            //Title for Books 
            $main_title      = $this->myget("//varfield[@id='245']/subfield[@label='a']");
            $secondary_title = $this->myget("//varfield[@id='245']/subfield[@label='b']");
            
            //Video Games
            $system = $this->myget("//varfield[@id='250']/subfield[@label='a']");
            
            //Get Author
            $main_author = rtrim($this->myget("//varfield[@id='100']/subfield[@label='a']"), ".,");
            $author      = rtrim($this->myget("//varfield[@id='100']/subfield[@label='a']"), ".");
            $author .= $this->myget("//varfield[@id='100']/subfield[@label='q']");
            $author .= $this->myget("//varfield[@id='100']/subfield[@label='d']");
            //Get ISBN
            $this->isbn = filter_var($this->myget("//varfield[@id='020']/subfield[@label='a']"), FILTER_SANITIZE_NUMBER_INT);
            //Get OCLC
            $oclc       = filter_var($this->myget("//varfield[@id='035']/subfield[@label='a']"), FILTER_SANITIZE_NUMBER_INT);
            //Television series number
            $season 	= $this->myget("//varfield[starts-with(@id, '2')]/subfield[@label='n' or @label='p']");

            //Get Added Title Data
            $add_title  = $this->myget("//varfield[@id='245']/subfield[@label='n']");
            $add_title .= $this->myget("//varfield[@id='245']/subfield[@label='p']");
            //Get Available Status
            $available_status = $this->myget("//varfield[@id='AVA']/subfield[@label='g']");
            $available        = $this->myget("//varfield[@id='AVA']/subfield[@label='t']");
            //Get Reserve Status
            $reserve_status   = $this->myget("//varfield[@id='AVA']/subfield[@label='j']");
            $circulation   = $this->myget("//varfield[@id='AVA']/subfield[@label='i']");
            //Get Call Numbers
            $cal_num          = $this->myget("//varfield[@id='852']/subfield[@label='h']");
            $cal_num .= $this->myget("//varfield[@id='852']/subfield[@label='i']");
            //
            $sup           = $this->myget("//varfield[@id='STA']/subfield[@label='a']");
            //Get Description
            $description   = $this->myget("//varfield[@id='520']/subfield[@label='a']");
            //Get Year
            $year          = $this->myget("//varfield[@id='046']/subfield[@label='k']");
            //Get Type
            $type          = $this->myget("//varfield[@id='TYP']/subfield[@label='a']");
            //Get Mat Type
            $mat_type      = $this->myget("//fixfield[@id='FMT']");
            $external_links = $this->myget("//varfield[@id='856']/subfield[@label='u']");
            $imdb          = $this->xpath->query("//varfield[@id='856']/subfield[@label='u']");
            $url		   = $this->myget("//varfield[@id='856']/subfield[@label='u']");
            $own           = $this->myget("//varfield[@id='OWN']/subfield[@label='a']");
            $cat_date 	   = $this->myget("//varfield[@id='CDA']/subfield[@label='c']");
            $tc 		   = $this->myget("//varfield[@id='505']/subfield[@label='a']"); 
			
            
            //Early learning language field
            $ell           = $this->myget("//varfield[@id='521']");
            
            $local_imdb  = $user->getIMDBId($record_docid);
            if (filter_var($external_links, FILTER_VALIDATE_URL) !== FALSE) {
	            $external_link = $external_links;
	        } else {
		        $external_link = '';
	        }
	        
	        if($mat_type == "CF" && !empty($url))
	        {
		        $vgameid = $this->giantbombId($url);
	        }  else {
		        $vgameid = '';
	        }   
         
            if(!empty($local_imdb)) {
	            $imdb_id = $local_imdb["imdb_id"];
            } else if (!empty($imdb)) {
	             $imdb_id = $this->imdb_id($imdb);
            } else {
	            $imdb_id = '';
            }
            
             //Getting television data from subjects
            $television = '';
            foreach ($this->xpath->query("//varfield[@id=655]/subfield[@label='a']") as $subjects) {
                $television .= trim($subjects->nodeValue);
            }
            
            if (strpos($cal_num, 'PN1992.77') !== false || strpos($cal_num, 'PN 1992') !== false) {
	        	$mat_type = 'tv';
	        }
	        if(strpos(strtolower($television), 'television') !== false ) {
		        $mat_type = 'tv';
	        }
	        
	        if(strtolower($type) == "game") {
		        $mat_type = 'game';
	        }
	        
	        if($mat_type == "VM" && (strtolower($type) == 'object'))
	        {
		        $mat_type = 'CF';
	        }
	        
	            
            
            $note = $this->xpath->query("//varfield[starts-with(@id, '5')]/subfield[@label='a']");
            if (!empty($note) && $mat_type == 'VM' || $mat_type == 'tv') {
                $return_year = $this->validYear($note);
                if (!empty($return_year)) {
                    $clean_year = $return_year;
                } else {
                    $clean_year = $this->valid008($this->fixed_008);
                }
                
            } else if (!empty($note) && $mat_type == 'BK') {
                $clean_year = $this->valid008($this->fixed_008);
            } 
            
            $foreign_titles = '';
            foreach ($this->xpath->query("//varfield[@id=245]/subfield[@label='a']") as $foreign_title) {
                $foreign_titles .= $foreign_title->nodeValue . "|";
            }
            
            $people = '';
            foreach ($this->xpath->query("//varfield[@id=700]/subfield[@label='a']") as $actors) {
                $people .= $actors->nodeValue . "|";
            }
            
            $genres = '';
            foreach ($this->xpath->query("//varfield[@id=655]/subfield[@label='a']") as $genre) {
                $genres .= $genre->nodeValue . "|";
            }
            
            $subjects = '';
            foreach ($this->xpath->query("//varfield[@id='650' or @id='651' or  @id='630']/subfield[@label='a' or @label='v' or @label='z']") as $subject) {
                $subjects .= $subject->nodeValue . "|";
            }
            
             $full_notes = '';
             foreach ($this->xpath->query("//varfield[starts-with(@id, '5')]/subfield[@label='a']") as $full_note)
             {
	             $full_notes .= $full_note->nodeValue . "|";
             }
                        
            
            $additional_note = '';
            foreach ($this->xpath->query("//varfield[@id='538']/subfield[@label='a']") as $additional) {
                $additional_note .= trim($additional->nodeValue);
            }
            
			if (strpos($additional_note, 'Blu-ray') !== false) {
				$bluray = true;
			} else {
				$bluray = false;
			}
			
//			if($mat_type == 'VM'){
//			if(!empty($imdb_id)){ 
//	            $image = $this->images->get_movie_images_by_id($imdb_id);
//	        } else {
//		        $new_title = $this->clean($title);
//				$short = substr($new_title, 0, 40);
//                $image = $this->images->get_movie_images($short, $clean_year);
//            }	
//			} else if($mat_type == 'tv') {
//				$new_title = $this->clean($title);
//				$short = substr($new_title, 0, 40);
//				$image = $this->images->get_tv_images($short);
//			} else if($mat_type == 'BK') {
//				$image = $this->images->google($oclc, $docid);
//			} else if($mat_type == 'CF') {
//				$image = $this->images->giantbomb($title, $record_docid);
//			} else if($mat_type == 'game') {
//				$image = $this->images->boardGameGeek($title, $record_docid);
//			}

			$holdings_location = array();
            //foreach ($this->myget("//varfield[@id=PST]/subfield[@label='4']") as $loc) {
              foreach ($this->xpath->query("//varfield[@id='PST']/subfield[@label='4']") as $hol) {
	              $holdings_location[] = $hol->nodeValue;
              }
              
              $holdings_id = array();
              foreach ($this->xpath->query("//varfield[@id='PST']/subfield[@label='1']") as $hol) {
	              $records = str_replace("FCL60-", "", $hol->nodeValue);
	              $holdings_id[] = $records;
              }
            
            $result[] = array(
                'suppressed' => $sup,
                'doc_id' => $docid,
                'record_id' => $record_docid,
                'fixed_008' => $this->fixed_008,
                'notes' => (isset($clean_year)) ? $clean_year : null,
                "television" => (isset($television)) ? $television : null,
                'music_title' => $this->clean($music_title),
                'music_author' => $this->clean($music_author),
                'music_year' => $this->clean($music_year),
                //'music_author2' => (isset($music_author2)) ? $music_author2 : null,
                'title' => $this->clean($title),
                'main_title' => $this->clean($main_title),
                'secondary_title' => $this->clean($secondary_title),
                'main_author' => $this->clean($main_author),
                'author' => $this->displayAuthor($author),
                'author_link' => $this->clean($author),
                'isbn' => (isset($this->isbn)) ? $this->isbn : null,
                'oclc' => (isset($oclc)) ? $oclc : null,
                'season' => $this->convert_numbers($season),
                'additional_title' => $add_title,
                'additional_note' => (isset($additional_note)) ? $additional_note : null,
                'available_status' => $available_status,
                'available' => $available,
                'reserve_status' => $reserve_status,
                'call_number' => $cal_num,
                'type' => (isset($type)) ? $type : null,
                'mat_type' => (isset($mat_type)) ? $mat_type : null,
                'description' => $description,
                'external_link' => (isset($external_link)) ? $external_link : null,
                'imdb' => (isset($imdb_id)) ? $imdb_id : null,
                'year' => $year,
                "actors" => (isset($people)) ? $people : null,
                'foreign_title' => (isset($foreign_titles)) ? $foreign_titles : null,
                'genre' => (isset($genres)) ? $genres : null,
                "subjects" => (isset($subjects)) ? $subjects : null,
                "hits"	=> $this->hit,
                'background' => (isset($background)) ? $background : null,
                'circulation' => $circulation,
                'bluray' => $bluray,
                'own'	=> $own,
                'cat_date' => $cat_date,
                'vgameid'  => $vgameid,
                'system'    => $system,
                'ell'      => (isset($ell)) ? $ell : null,
                'full_note' => $full_notes,
                'holdings_location' => (isset($holdings_location)) ? $holdings_location : null,
                'holdings_id' => (isset($holdings_id)) ? $holdings_id : null,
                'holdings_record' => (isset($holdings_record)) ? $holdings_record : null,
                'tc'		=> (isset($tc)) ? $tc : null,
                //'img' => $image
            );
        }
        
        return $result;
        
    }
    
    public function get_record_information($data, $path)
    {
        $html = new \DOMDocument();
        $html->load($data);
        $this->xpath = new \DOMXPath($html);
        $answer      = $this->xpath->query($path);
   
        return $answer;
    }
    
    public function myget($query)
    {
        $result = $this->xpath->query($query)->item(0);
        if (!empty($result->nodeValue)) {
            return trim($result->nodeValue);
        } else {
            return "";
        }
    }
    
    
    public function get_pagination($page)
    {
        $this->hit = ltrim($this->hits, 0);
        $perPage   = 25;
        $staticKey = 1;
        $keySet    = 1;
        if ($this->hit > $perPage) {
            $this->total_pages = ceil($this->hit / $perPage);
        } else {
            $this->total_pages = 1;
        }
        
        if ($this->hit < $perPage) {
            $response = "http://fcaa.library.umass.edu/X?op=present&set_no=" . $this->set . "&set_entry=" . $staticKey . "-" . $this->hit;
            return $response;
        }
        for ($x = $perPage, $y = 1; $x <= $this->hit + 100; $y += $perPage, $x += $perPage, $keySet++) {
            $responseArray[] = array(
                $keySet => "http://fcaa.library.umass.edu/X?op=present&set_no=" . $this->set . "&set_entry=" . $y . "-" . $x
            );
            foreach ($responseArray as $responses) {
                $pageKey = $page;
                if (isset($responses[$pageKey])) {
                    $response = $responses[$pageKey];
                    return $response;
                }
            }
        }
    }
    
    private function clean_notes($note)
    {
        foreach ($note as $n) {
            $notes = trim($n->nodeValue);
            if (!empty($notes)) {
                if (preg_match('/(\d{4})/', $notes, $regs)) {
                    $result_notes = $regs[0];
                    return substr($result_notes, -4);
                }
            }
        }
    }
    
    private function validYear($note)
    {
        foreach ($note as $n) {
            $notes = trim($n->nodeValue);
            
            if (!empty($notes)) {
                if (preg_match('/(\d{4})/', $notes, $regs)) {
                    $result_notes = $regs[0];
                }
                if (!empty($result_notes)) {
                    if (preg_match('/
        # Match a 20th or 21st century year (or range of years).
        ^                # Anchor to start of string.
        (                # $1: Required year.
          (?:19|20)      # Century is 19 or 20.
          [0-9]{2}       # Year is 00 to 99.
        )$                # Anchor to end of string.
        /x', $result_notes, $matches)) {
                        return $matches[0];
                    }
                }
            }
        }
    }
    
    private function valid008($data)
    {
        $dataYear = substr($data, 7);
        return substr($dataYear, 0, -29);
    }
    
    
    
    public function imdb_id($link)
    {
        foreach ($link as $l) {
            $links = trim($l->nodeValue);
            if (!empty($links)) {
                if (preg_match("/tt\\d{7}/", $links, $ids)) {
                    return $ids[0];
                }
            }
        }
    }
    
    public function giantbombId($link)
    {
        return $this->getUriSegment(2, $link);           

    }
    
    public function getUriSegments($url) {
    	return explode("/", parse_url($url, PHP_URL_PATH));
	}
 
	public function getUriSegment($n, $url) {
    	$segs = $this->getUriSegments($url);
		return count($segs)>0&&count($segs)>=($n-1)?$segs[$n]:'';
	} 
	
	public function displayAuthor($authors)
    {
	  $author = str_replace('"', '', $authors);
      if (substr($author, strlen($author) - 1, 1) == ",") {
          $author = substr($author, 0, strlen($author) - 1);
      }
      $author = explode(',', $author);
	  // Create First Name
      $fname = '';
      if (isset($author[1])) {
        $fname = $author[1];
           if (isset($author[2])) {
                    // Remove punctuation
                    if ((strlen($author[2]) > 2)
                        && (substr($author[2], -1) == '.')
                    ) {
                        $author[2] = substr($author[2], 0, -1);
                    }
                    $fname = $author[2] . ' ' . $fname;
                }
            }

            // Remove dates
            $fname = preg_replace('/[0-9]+-[0-9]*/', '', $fname);

            // Build Author name to display.
            if (substr($fname, -3, 1) == ' ') {
                // Keep period after initial
                $authorName = $fname . ' ';
            } else {
                // No initial so strip any punctuation from the end
                if ((substr(trim($fname), -1) == ',')
                    || (substr(trim($fname), -1) == '.')
                ) {
                    $authorName = substr(trim($fname), 0, -1) . ' ';
                } else {
                    $authorName = $fname . ' ';
                }
            }
            $authorName .= $author[0];
            return $authorName;
    }
    
    
    
}
