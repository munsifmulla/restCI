<?php

$passphrase = 'vinay035';
$message = 'Someone Just voted on your post';
$cert_file = 'ck.pem';
$deviceToken="";
$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', $cert_file);
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

$fp = stream_socket_client(
        'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp) {
    echo "Failed to connect: $err $errstr" . PHP_EOL;
}

echo 'Connected to APNS' . PHP_EOL;
$body['aps'] = array(
    'alert' => $message,
    'sound' => 'default',    
);
$payload = json_encode($body);
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
$result = fwrite($fp, $msg, strlen($msg));

if (!$result) {
    echo 'Message not delivered' . PHP_EOL;
} else {
    echo 'Message successfully delivered' . PHP_EOL;    
}
?>