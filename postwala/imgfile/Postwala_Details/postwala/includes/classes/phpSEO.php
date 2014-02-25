<?php
/////////////////////////////////////////////////////////////
/*
 * Name:	phpSEO 
 * URL:		http:/neo22s.com/phpseo
 * Version:	v0.2 
 * Date:	12/09/2010
 * Author:	Chema Garrido
 * Support: http://forum.neo22s.com
 * License: GPL v3
 * Notes:	Based on http://neo22s.com/seo-functions-for-php/
 */

class phpSEO{
	
	//private variables 
	private $text;//text to work with 
	private $maxDescriptionLen=220;//max len for the meta description
	private $maxKeywords=25;//mix number of keywords to return
	private $minWordLen=3;//min len for a word
	//banned words in english feel free to change them
	private $bannedWords=array("able", "about", "above", "act", "add", "afraid", "after", "again", "against", "age", "ago", "agree", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "amount", "an", "and", "anger", "angry", "animal", "another", "answer", "any", "appear", "apple", "are", "arrive", "arm", "arms", "around", "arrive", "as", "ask", "at", "attempt", "aunt", "away", "back", "bad", "bag", "bay", "be", "became", "because", "become", "been", "before", "began", "begin", "behind", "being", "bell", "belong", "below", "beside", "best", "better", "between", "beyond", "big", "body", "bone", "born", "borrow", "both", "bottom", "box", "boy", "break", "bring", "brought", "bug", "built", "busy", "but", "buy", "by", "call", "came", "can", "cause", "choose", "close", "close", "consider", "come", "consider", "considerable", "contain", "continue", "could", "cry", "cut", "dare", "dark", "deal", "dear", "decide", "deep", "did", "die", "do", "does", "dog", "done", "doubt", "down", "during", "each", "ear", "early", "eat", "effort", "either", "else", "end", "enjoy", "enough", "enter", "even", "ever", "every", "except", "expect", "explain", "fail", "fall", "far", "fat", "favor", "fear", "feel", "feet", "fell", "felt", "few", "fill", "find", "fit", "fly", "follow", "for", "forever", "forget", "from", "front", "gave", "get", "gives", "goes", "gone", "good", "got", "gray", "great", "green", "grew", "grow", "guess", "had", "half", "hang", "happen", "has", "hat", "have", "he", "hear", "heard", "held", "hello", "help", "her", "here", "hers", "high", "hill", "him", "his", "hit", "hold", "hot", "how", "however", "I", "if", "ill", "in", "indeed", "instead", "into", "iron", "is", "it", "its", "just", "keep", "kept", "knew", "know", "known", "late", "least", "led", "left", "lend", "less", "let", "like", "likely", "likr", "lone", "long", "look", "lot", "make", "many", "may", "me", "mean", "met", "might", "mile", "mine", "moon", "more", "most", "move", "much", "must", "my", "near", "nearly", "necessary", "neither", "never", "next", "no", "none", "nor", "not", "note", "nothing", "now", "number", "of", "off", "often", "oh", "on", "once", "only", "or", "other", "ought", "our", "out", "please", "prepare", "probable", "pull", "pure", "push", "put", "raise", "ran", "rather", "reach", "realize", "reply", "require", "rest", "run", "said", "same", "sat", "saw", "say", "see", "seem", "seen", "self", "sell", "sent", "separate", "set", "shall", "she", "should", "side", "sign", "since", "so", "sold", "some", "soon", "sorry", "stay", "step", "stick", "still", "stood", "such", "sudden", "suppose", "take", "taken", "talk", "tall", "tell", "ten", "than", "thank", "that", "the", "their", "them", "then", "there", "therefore", "these", "they", "this", "those", "though", "through", "till", "to", "today", "told", "tomorrow", "too", "took", "tore", "tought", "toward", "tried", "tries", "trust", "try", "turn", "two", "under", "until", "up", "upon", "us", "use", "usual", "various", "verb", "very", "visit", "want", "was", "we", "well", "went", "were", "what", "when", "where", "whether", "which", "while", "white", "who", "whom", "whose", "why", "will", "with", "within", "without", "would", "yes", "yet", "you", "young", "your", "br", "img", "p","lt", "gt", "quot", "copy");
	
	function phpSEO($text=null){//constructor
		$this->setText($text);
	}
	
	///SEO for meta description
	function getMetaDescription($len=null){//returns a text with meta description
		$this->setDescriptionLen($len);//by param set len
		return mb_substr($this->getText(), 0, $this->getDescriptionLen(),CHARSET);//shrink X chars
	}
	
	function getKeyWords($mKw=null){//from the given text
		$this->setMaxKeywords($mKw);//by param max keywords
		$text = $this->getText();
		$text = str_replace (array('–','(',')','+',':','.','?','!','_','*','-'), '', $text);//replace not valid character
		$text = str_replace (array(' ','.'), ',', $text);//replace for comas
		
		//erase minor words, banned words and count
		
		$wordCounter=array();//array to keep word->number of repetitions
		
		$arrText=explode(",",$text);
		unset($text);
		foreach ($arrText as $value)  {
			$value=trim($value);//bye spaces
			if ( strlen($value)>=$this->getMinWordLen() && !in_array($value,$this->getBannedWords()) ) {//no smaller than X and not in banned
				//I can count how many time we ad and update the record in an array
				if (array_key_exists($value,$wordCounter)){//if the key exists we ad 1 more
					$wordCounter[$value]=$wordCounter[$value]+1;
				}
				else $wordCounter[$value]=1;//creating the key
			}
		}
		unset($arrText);
	
		uasort($wordCounter,array($this,cmp));//short from bigger to smaller
		
		$i=1;//start countg how many keywords
		foreach($wordCounter as $key=>$value){
			$keywords.=$key.", ";
			if ($i<$this->getMaxKeywords()) $i++;//to control the limit of keywords to display
			else break;//we did all of them! bye!
		}
		unset($wordCounter);
		
		$keywords=substr($keywords,0,-2);//erase last coma
	
		return $keywords;
	}

	//config params:
	/////////$text
	function setText($text){
		$text = strip_tags($text);//erase possible html tags
		$text = str_replace (array('\r\n', '\n', '+'), ',', $text);//replace possible returns
		$text = trim($text);
		$text = mb_strtolower($text,CHARSET);//everything to lower case
		$this-> text=$text;//set text
	}
	function getText(){
		return $this->text;
	}
	/////////////$maxDescriptionLen
	function setDescriptionLen($len){
		if (is_numeric($len)) $this->maxDescriptionLen=$len;
	}
	function getDescriptionLen(){
		return $this->maxDescriptionLen;
	}
	//////////////maxKeywords
	function setMaxKeywords($len){
		if (is_numeric($len)) $this->maxKeywords=$len;
	}
	function getMaxKeywords(){
		return $this->maxKeywords;
	}
	////////////minWordLen
	function setMinWordLen($len){
		if (is_numeric($len)) $this->minWordLen=$len;
	}
	function getMinWordLen(){
		return $this->minWordLen;
	}
	////////////bannedWords
	function setBannedWords($words){
		if (isset($words)){
			$arrText=explode(",",$words);
			if (is_array($arrText)) $this->bannedWords=$arrText;
		}
	}
	function getBannedWords(){
		return $this->bannedWords;
	}	
	//end config params
	
	//function tools:
	private function countWordArray($search,$array){//count the $search word in the given array
		if (is_array($array) && $search!=""){
			$count=0;
			foreach ($array as $e) if ($e==$search) $count++;
			return $count;
		}
		else return false;
	}
	
	private function cmp($a, $b) {//sort for uasort descendent numbers
	    if ($a == $b) return 0;
	    return ($a < $b) ? 1 : -1;
	}
	//end function tools
}
//end phpSEO class

?>