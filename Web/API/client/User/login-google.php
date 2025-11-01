<?php
require_once '../../../../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('718966963833-1rvf6e4hv3lkuvlt6rs8b24anl7i45vq.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://127.0.0.1/WebLinhKienDienTu/Web/API/client/User/google-callback.php');
$client->addScope(['email', 'profile']);
$client->setPrompt('select_account consent');

$login_url = $client->createAuthUrl();
header('Location: ' . $login_url);
exit;
?>
