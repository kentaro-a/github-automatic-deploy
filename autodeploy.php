// Config
$LOG_FILE = 'path to logfile';
$SECRET_KEY = 'your secretkey';

$header = getallheaders();
// MagicVariable "$HTTP_RAW_POST_DATA" was deleted since php7, So using "php://input".
$HTTP_RAW_POST_DATA = file_get_contents("php://input");
$hmac = hash_hmac('sha1', $HTTP_RAW_POST_DATA, $SECRET_KEY);

if (isset($header['X-Hub-Signature']) && $header['X-Hub-Signature'] === 'sha1='.$hmac) {
	// Recieved data from webhook.
	$payload = json_decode($HTTP_RAW_POST_DATA, true);
	// Execute pull.
	shell_exec('sudo git --git-dir=[path to .git] --work-tree=[path to workdir] pull');
	file_put_contents($LOG_FILE, date("[Y-m-d H:i:s]")." ".$_SERVER['REMOTE_ADDR']." git pulled: ".$payload['after']." ".$payload['commits'][0]['message']."\n", FILE_APPEND);

} else {
	file_put_contents($LOG_FILE, date("[Y-m-d H:i:s]")." invalid access: ".$_SERVER['REMOTE_ADDR']."\n", FILE_APPEND);
}
