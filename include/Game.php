<?php

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

class Game {
	private $email = "help0001%40gmail.com";
	private $country = "NL";
	private $firstname = "Peter";
	private $lastname = "v.d.Laan";
	
	public $time_good = 350000;
	public $time_bad = 2000000;
	public $time_end = 1000000;
	
	public $start_time = 0;
	
	public $counter = 0;
	
	public $found;
	public $memory;
	
	private $first_card_id = -1;
	public $next = -1;
	
	function Initialize() {
		$this->found = array(
						false, false, false, false, 
						false, false, false, false, 
						false, false, false, false, 
						false, false, false, false
					);
		
		$this->memory = array(
						-1, -1, -1, -1, 
						-1, -1, -1, -1, 
						-1, -1, -1, -1, 
						-1, -1, -1, -1
					);
		
		Page::setHeader(Header::JSON, false);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/game/ajax.php?function=init");
		curl_exec(Page::$curl_sessie);
	}
	
	function Start() {
		Page::setHeader(Header::JSON, false);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/game/ajax.php?function=start");
		$this->start_time = microtime_float();
		curl_exec(Page::$curl_sessie);
		
	}
	
	function Click() {
		$id = Page::$game->GetCard();
		
		Page::setHeader(Header::JSON, false);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/game/ajax.php?function=click&cardindex=" . $id);
		$output = curl_exec(Page::$curl_sessie);
		
		$json = json_decode($output, true);
		$this->memory[$id] = $json["cardid"];
		
		$this->counter++;
		if($this->counter % 2) { //Eerste kaart
			$this->first_card_id = $id;
			
			usleep($this->time_good);
			
			return true;
		} else { //Tweede kaart
			if($json["cardmatch"] == true) {
				//Found!
				$this->found[$this->first_card_id] = true;
				$this->found[$id] = true;

				$this->first_card_id = -1; //reset
				
				usleep($this->time_good);
				
				return true;
			} else {
				$this->first_card_id = -1; //reset
				
				usleep($this->time_bad);
				
				return false;
			}
		}
		return false; //error
	}
	
	function UpdateNext() {
		for($i = 0; $i < 16; $i++) {
			if($this->memory[$i] != -1 && $this->found[$i] == false) {
				for($j = 0; $j < 16; $j++) {
					if($i != $j && $this->memory[$i] == $this->memory[$j] && $this->found[$j] == false) {
						if($this->first_card_id == -1) {
							return $i;
						} else if($i == $this->first_card_id) {
							return $j;
						} else if($j == $this->first_card_id) {
							return $i;
						}
					}
				}
			}
		}
		
		return -1;
	}
	
	function GetCard() {
		$card = 0;
		
		if($this->next != -1) {
			$id = $this->next;
			$this->next = -1;
			return $id;
		}
		
		while($card != -1) {
			$id = rand(0, 15);
			$card = $this->memory[$id];
		}
		
		return $id;
	}
	
	function CardsFound() {
		$count = 0;
		
		for($i = 0; $i < 16; $i++) {
			if($this->found[$i])
				$count++;
		}
		
		return $count;
	}
	
	function End() {
		$new_time = microtime_float() - $this->start_time;
		
		if($new_time + 1 < 12)
			$this->time_end += round((11 - $new_time) * 1000000, 0);
		
		usleep($this->time_end);
		
		Page::setHeader(Header::JSON, false);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/game/ajax.php?function=end");
		curl_exec(Page::$curl_sessie);
		
		Page::setHeader(Header::JSON, false);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/game/ajax.php?function=result");
		$output = curl_exec(Page::$curl_sessie);

		var_dump($output);	//DEBUG
		echo "<br />";		//DEBUG
		
		Page::setHeader(Header::JSON, false);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/game/ajax.php?email=" . $this->email . "&function=autocomplete");
		curl_exec(Page::$curl_sessie);
		
		Page::setHeader(Header::JSON, false);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/game/ajax.php?country=" . $this->country . "&minigran_game=&email=" . $this->email . "&firstname=" . $this->firstname . "&name=" . $this->lastname . "&function=submit");
		$output = curl_exec(Page::$curl_sessie);

		var_dump($output);	//DEBUG
		echo "<br />";		//DEBUG
		
		Page::setHeader(Header::JSON, false);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/game/ajax.php?function=rank&month=3");
		curl_exec(Page::$curl_sessie);
	}
}

?>