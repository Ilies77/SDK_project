<?php

define("CLIENT_ID", '67dc2be521bec2ff862d3ab057de216b');
define("CLIENT_SECRET", '04054cf433eeb3976252c81b6d657fda');
define("FB_CLIENT_ID", '439698237646679');
define("FB_CLIENT_SECRET", 'f388515768af186b09562a9897c2605d');

define("GOOGLE_CLIENT_ID", '717148236814-gnf3acamnmn8dhqmlsj2nfmpqfuqmhcr.apps.googleusercontent.com');
define("GOOGLE_CLIENT_SECRET", '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');

define("TWITCH_CLIENT_ID", 'tsy0kkksrmh9ijkhlg8ledyadl39pp');
define("TWITCH_CLIENT_SECRET", 'h1f0h1mmm3tbc9pswh23z7zb57b77l');


// Create a login page with a link to oauth
function login()
{
    $queryParams = http_build_query([
        "state"=>bin2hex(random_bytes(16)),
        "client_id"=> CLIENT_ID,
        "scope"=>"profile",
        "response_type"=>"code",
        "redirect_uri"=>"http://localhost:8081/oauth_success",
    ]);
    echo "
        <form method=\"POST\" action=\"/oauth_success\">
            <input type=\"text\" name=\"username\"/>
            <input type=\"password\" name=\"password\"/>
            <input type=\"submit\" value=\"Login\"/>
        </form>
    ";
    $fbQueryParams = http_build_query([
        "state"=>bin2hex(random_bytes(16)),
        "client_id"=> FB_CLIENT_ID,
        "scope"=>"public_profile,email",
        "redirect_uri"=>"https://localhost/fb_oauth_success",
    ]);
    $TwitchQueryParams = http_build_query([
        "state"=>bin2hex(random_bytes(16)),
        "response_type"=>"token",
        "client_id"=> TWITCH_CLIENT_ID,
        "redirect_uri"=>"https://localhost/twitch_oauth_success",
        "scope"=>"public_profile",
        
    ]);
    echo "<a href=\"http://localhost:8080/auth?{$queryParams}\">Login with Oauth-Server</a><br>";
    echo "<a href=\"https://www.facebook.com/v13.0/dialog/oauth?{$fbQueryParams}\">Login with Facebook</a><br>";
    echo "<a href=\"https://id.twitch.tv/oauth2/authorize?{$TwitchQueryParams}\">Login with Twitch</a>";
    
   
}

// get token from code then get user info
function callback()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        ["username"=> $username, "password" => $password] = $_POST;
        $specifParams = [
            "grant_type" => "password",
            "username" => $username,
            "password" => $password,
       ];
    } else {
        ["code"=> $code, "state" => $state] = $_GET;
        $specifParams = [
            "grant_type" => "authorization_code",
            "code" => $code
        ];
    }
    $queryParams = http_build_query(array_merge(
        $specifParams,
        [
            "redirect_uri" => "http://localhost:8081/oauth_success",
            "client_id" => CLIENT_ID,
            "client_secret" => CLIENT_SECRET,
        ]
    ));
    $response = file_get_contents("http://server:8080/token?{$queryParams}");
    if (!$response) {
        echo $http_response_header;
        return;
    }
    ["access_token" => $token] = json_decode($response, true);


    $context = stream_context_create([
        "http"=>[
            "header"=>"Authorization: Bearer {$token}"
        ]
    ]);
    $response = file_get_contents("http://server:8080/me", false, $context);
    if (!$response) {
        echo $http_response_header;
        return;
    }
    var_dump(json_decode($response, true));
}

function twittercallback()
{
    $token = getTToken("https://graph.twitter.com/v13.0/oauth/access_token", TWITTER_CLIENT_ID, TWITTER_CLIENT_SECRET);
    $user = getTwitterUser($token);
    $unifiedUser = (fn () => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "firstName" => $user['first_name'],
        "lastName" => $user['last_name'],
    ])();
    var_dump($unifiedUser);
}
function getTwitterUser($token)
{
    $context = stream_context_create([
        "http"=>[
            "header"=>"Authorization: Bearer {$token}"
        ]
    ]);
    $response = file_get_contents("https://graph.twitter.com/v13.0/me?fields=last_name,first_name,email", false, $context);
    if (!$response) {
        echo $http_response_header;
        return;
    }
    return json_decode($response, true);
}
function getTToken($baseUrl, $clientId, $clientSecret)
{
    ["code"=> $code, "state" => $state] = $_GET;
    $queryParams = http_build_query([
        "client_id"=> $clientId,
        "client_secret"=> $clientSecret,
        "redirect_uri"=>"https://localhost/twitter_oauth_success",
        "code"=> $code,
        "grant_type"=>"authorization_code",
    ]);

    $url = $baseUrl . "?{$queryParams}";
    $response = file_get_contents($url);
    if (!$response) {
        echo $http_response_header;
        return;
    }
    ["access_token" => $token] = json_decode($response, true);

    return $token;
}

// Facebook oauth: exchange code with token then get user info
function fbcallback()
{
    $token = getToken("https://graph.facebook.com/v13.0/oauth/access_token", FB_CLIENT_ID, FB_CLIENT_SECRET);
    $user = getFbUser($token);
    $unifiedUser = (fn () => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "firstName" => $user['first_name'],
        "lastName" => $user['last_name'],
    ])();
    var_dump($unifiedUser);
}
function getFbUser($token)
{
    $context = stream_context_create([
        "http"=>[
            "header"=>"Authorization: Bearer {$token}"
        ]
    ]);
    $response = file_get_contents("https://graph.facebook.com/v13.0/me?fields=last_name,first_name,email", false, $context);
    if (!$response) {
        echo $http_response_header;
        return;
    }
    return json_decode($response, true);
}
function getToken($baseUrl, $clientId, $clientSecret)
{
    ["code"=> $code, "state" => $state] = $_GET;
    $queryParams = http_build_query([
        "client_id"=> $clientId,
        "client_secret"=> $clientSecret,
        "redirect_uri"=>"https://localhost/fb_oauth_success",
        "code"=> $code,
        "grant_type"=>"authorization_code",
    ]);

    $url = $baseUrl . "?{$queryParams}";
    $response = file_get_contents($url);
    if (!$response) {
        echo $http_response_header;
        return;
    }
    ["access_token" => $token] = json_decode($response, true);

    return $token;
}

$route = $_SERVER["REQUEST_URI"];
switch (strtok($route, "?")) {
    case '/login':
        login();
        break;
    case '/oauth_success':
        callback();
        break;
    case '/fb_oauth_success':
        fbcallback();
        break;
    case '/twitch_oauth_success':
        twitchcallback();
        break;
    default:
        http_response_code(404);
}
