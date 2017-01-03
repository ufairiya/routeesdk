<?php
 // To receive the routee callback result 
$webhookContent = '';
$webhook = fopen('php://input' , 'rb');
while (!feof($webhook)) {
$webhookContent .= fread($webhook, 4096);
}
fclose($webhook);
$post = ($webhookContent);
mail("email@domain.com","Test",$post);
?>