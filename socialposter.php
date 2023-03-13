<?php
require_once('./dbconnection.php');

function post_to_misskey($title, $author, $url) {
  $host = getenv('MISSKEY_INSTANCE');
  $token = getenv('MISSKEY_TOKEN');
  if ($host == null || $token == null || strlen($host) == 0 || strlen($token) == 0) return null;
  
  $url = "$host/api/notes/create";
  $req_data = json_encode([
    'i' => $token,
    'text' => "$title\n\n$url",
    'noExtractMentions' => true,
    'noExtractHashtags' => false,
    "noExtractEmojis" => true,
  ]);

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER,
          array("Content-type: application/json"));
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $req_data);

  $json_response = curl_exec($curl);

  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);
  if ($status == 200) {
    $res_data = json_decode($json_response);
    return $res_data->createdNote->id;
  } else {
    return null;
  }
}
