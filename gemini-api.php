<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$apiKey = 'AIzaSyAkVs752Drx1yMZ6vJVLHsCQDjK9lzWXTQ';
$input  = json_decode(file_get_contents('php://input'), true);
$prompt = trim($input['prompt'] ?? '');

if (!$prompt) {
  echo json_encode(['reply' => 'No prompt provided.']);
  exit;
}
$contents = $_SESSION['chat'] ?? [];

$contents[] = ['role' => 'user', 'parts' => [['text' => $prompt]]];

$payload = ['contents' => $contents];
// Updated endpoint for Gemini 2.0 Flash
$chatModel = "gemini-1.5-pro"; // or "gemini-pro"
$url = "https://generativelanguage.googleapis.com/v1beta/models/{$chatModel}:generateContent?key={$apiKey}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
if (curl_errno($ch)) {
  echo json_encode(['reply' => 'Curl error: ' . curl_error($ch)]);
  exit;
}
curl_close($ch);

$data = json_decode($response, true);
$reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No answer returned.';

$contents[] = ['role' => 'model', 'parts' => [['text' => $reply]]];

$_SESSION['chat'] = $contents;

echo json_encode(['reply' => $reply]);
?>

