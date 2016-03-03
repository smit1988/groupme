<?php

/*
 *   Output a random fact to GroupMe via a bot.
 *
 *   Trigger the bot with "!randomfact" or "!random fact".
 *   Enter the bot ID on line 43.
 *
 */

class GroupMeBot {

	public $group, $id, $user_id, $group_id;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function post($message) {
		$post_text = utf8_encode($message);
		
		$ch = curl_init();
		$post_contents = array(
			'bot_id' => $this->id,
			'text' => $post_text,
		);
		
		$post = json_encode($post_contents);
		
		$arr = array();
		array_push($arr, 'Content-Type: application/json; charset=utf-8');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);
		curl_setopt($ch, CURLOPT_URL, 'https://api.groupme.com/v3/bots/post');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_exec($ch);
		curl_close($ch);
	}
}

$cmd = 'wget randomfunfacts.com -O - 2>/dev/null | grep \<strong\> | sed "s;^.*<i>\(.*\)</i>.*$;\1;"';
// ENTER BOT ID INTO CONSTRUCTOR BELOW
$bot = new GroupMeBot('12345678901234567890123456');

$msg_json = file_get_contents('php://input');
$msg = json_decode($msg_json);
$user_id = $msg->user_id;
$group_id = $msg->group_id;
$inquirer_name = $msg->name;
$from = $msg->name;
$text = $msg->text;

if (!substr($text, 0, 1) == '!')
	exit();

$available_keywords = array(
	'randomfact',
	'random',
	'fact'
);

$no_trigger = strtolower(substr($text, 1));
$unstripped_keywords = explode(' ', $no_trigger);
$keywords = array();
foreach ($unstripped_keywords as $keyword) {
	$keywords[] = trim($keyword, ' ,');
}

if (in_array('randomfact', $keywords) || 
	(in_array('random', $keywords) && in_array('fact', $keywords))) {
	$output = exec($cmd);
}

if (strlen($output) < 1)
	exit();

$bot->post($output);

?>