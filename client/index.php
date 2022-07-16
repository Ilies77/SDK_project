<?php

define("CLIENT_ID", '67dc2be521bec2ff862d3ab057de216b');
define("CLIENT_SECRET", '04054cf433eeb3976252c81b6d657fda');
define("GOOGLE_CLIENT_ID", '717148236814-gnf3acamnmn8dhqmlsj2nfmpqfuqmhcr.apps.googleusercontent.com');
define("GOOGLE_CLIENT_SECRET", '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');
define("DISCORD_CLIENT_ID", '989429994465927180');
define("DISCORD_CLIENT_SECRET", 'KQbdBsrmh19E1UfrP_YuP7DDjEqByjAs');
define("FB_CLIENT_ID", '439698237646679');
define("FB_CLIENT_SECRET", 'f388515768af186b09562a9897c2605d');

// Create a login page with a link to oauth
function login()
{
    $queryParams = http_build_query([
        "state" => bin2hex(random_bytes(16)),
        "client_id" => CLIENT_ID,
        "scope" => "profile",
        "response_type" => "code",
        "redirect_uri" => "http://localhost:8081/oauth_success",
    ]);
    echo "
        <form method=\"POST\" action=\"/oauth_success\">
            <input type=\"text\" name=\"username\"/>
            <input type=\"password\" name=\"password\"/>
            <input type=\"submit\" value=\"Login\"/>
        </form>
    ";
    $googleQueryParams = http_build_query([
        "state" => bin2hex(random_bytes(16)),
        "client_id" => GOOGLE_CLIENT_ID,
        "scope" => "email",
        "response_type" => "code",
        "redirect_uri" => "https://localhost/google_oauth_success",
    ]);
    $discordQueryParams = http_build_query([
        "state" => bin2hex(random_bytes(16)),
        "client_id" => DISCORD_CLIENT_ID,
        "scope" => "identify",
        "response_type" => "code",
        "redirect_uri" => "https://localhost/discord_oauth_success",
    ]);
    $fbQueryParams = http_build_query([
        "state" => bin2hex(random_bytes(16)),
        "client_id" => FB_CLIENT_ID,
        "scope" => "public_profile,email",
        "redirect_uri" => "https://localhost/fb_oauth_success",
    ]);
    echo "<a href=\"http://localhost:8080/auth?{$queryParams}\">Login with Oauth-Server</a><br>";
    echo "<a href=\"https://accounts.google.com/o/oauth2/v2/auth?{$googleQueryParams}\">Login with Google</a><br>";
    echo "<a href=\"https://discord.com/api/oauth2/authorize?{$discordQueryParams}\">Login with Discord</a><br>";
    echo "<a href=\"https://www.facebook.com/v13.0/dialog/oauth?{$fbQueryParams}\">Login with Facebook</a><br>";
}

// get token from code then get user info
function callback()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        ["username" => $username, "password" => $password] = $_POST;
        $specifParams = [
            "grant_type" => "password",
            "username" => $username,
            "password" => $password,
        ];
    } else {
        ["code" => $code, "state" => $state] = $_GET;
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
        "http" => [
            "header" => "Authorization: Bearer {$token}"
        ]
    ]);
    $response = file_get_contents("http://server:8080/me", false, $context);
    if (!$response) {
        echo $http_response_header;
        return;
    }
    var_dump(json_decode($response, true));

    echo 'Bonjour ' . $username . ', vous êtes connectés avec succès !';
}

function googlecallback()
{
    /*if ($_SERVER["REQUEST_METHOD"] === "POST") {
        ["username" => $username, "password" => $password] = $_POST;
        $specifParams = [
            "grant_type" => "password",
            "username" => $username,
            "password" => $password,
        ];
    } else {
        ["code" => $code, "state" => $state] = $_GET;
        $specifParams = [
            "grant_type" => "authorization_code",
            "code" => $code
        ];
    }
    $queryParams = http_build_query(array_merge(
        $specifParams,
        [
            "redirect_uri" => "https://localhost/google_oauth_success",
            "client_id" => GOOGLE_CLIENT_ID,
            "client_secret" => GOOGLE_CLIENT_SECRET,
        ]
    ));
    $response = file_get_contents("https://accounts.google.com/o/oauth2/v2/token?{$queryParams}");
    if (!$response) {
        echo $http_response_header;
        return;
    }
    ["access_token" => $token] = json_decode($response, true);


    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Bearer {$token}"
        ]
    ]);
    $response = file_get_contents("https://www.googleapis.com/oauth2/v3/userinfo", false, $context);
    if (!$response) {
        echo $http_response_header;
        return;
    }
    var_dump(json_decode($response, true));
    $token = getTokenGoogle("https://accounts.google.com/o/oauth2/v2/token", GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET);
    $user = getGoogleUser($token);
    $unifiedUser = (fn () => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "firstName" => $user['first_name'],
        "lastName" => $user['last_name'],
        "code" => $_GET['code'],
        "id_token" => $user['id_token'],
    ])();
    var_dump($unifiedUser);*/
    echo 'Vous êtes désormais connecté avec votre compte Google !';
}

function discordcallback()
{
    //$token = getTokenDiscord("https://discord.com/api/oauth2/token", DISCORD_CLIENT_ID, DISCORD_CLIENT_SECRET);
    // $user = getDiscordUser($token);
    // $unifiedUser = (fn () => [
    //     "id" => $user["id"],
    //     "name" => $user["name"],
    //     "email" => $user["email"],
    //     "firstName" => $user['first_name'],
    //     "lastName" => $user['last_name'],
    //     "code" => $_GET['code'],
    //     "client_id" => DISCORD_CLIENT_ID,
    //     "token" => $token
    // ])();
    //var_dump($unifiedUser);
    echo 'Vous êtes désormais connecté avec votre compte Discord !';
}

// Facebook oauth: exchange code with token then get user info
function fbcallback()
{
    $token = getToken("https://graph.facebook.com/v13.0/oauth/access_token", FB_CLIENT_ID, FB_CLIENT_SECRET);
    $user = getFbUser($token);
    $unifiedUser = (fn () => [
        "id" => $user["id"],
        "email" => $user["email"],
        "firstName" => $user['first_name'],
        "lastName" => $user['last_name'],
    ])();
    var_dump($unifiedUser);
    echo "<br>";

    echo "Bonjour " . $user['last_name'] . " " . $user['first_name'] . " , vous êtes bien connecté à votre compte Facebook: " . $user["email"];
}

function getFbUser($token)
{
    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Bearer {$token}"
        ]
    ]);
    $response = file_get_contents("https://graph.facebook.com/v13.0/me?fields=last_name,first_name,email", false, $context);
    if (!$response) {
        echo $http_response_header;
        return;
    }
    return json_decode($response, true);
}

function getGoogleUser($token)
{
    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Bearer {$token}"
        ]
    ]);
    $response = file_get_contents("https://accounts.google.com/o/oauth2/token", false, $context);
    if (!$response) {
        echo $http_response_header;
        return;
    }

    return json_decode($response, true);
}

function getDiscordUser($token)
{
    $context = stream_context_create([
        "http" => [
            "header" => "Authorization: Bearer {$token}"
        ]
    ]);
    $response = file_get_contents("https://discordapp.com/api/users/@me", false, $context);
    if (!$response) {
        echo $http_response_header;
        echo 'FAILED';
        return;
    }
    return json_decode($response, true);
}

function getToken($baseUrl, $clientId, $clientSecret)
{
    ["code" => $code, "state" => $state] = $_GET;
    $queryParams = http_build_query([
        "client_id" => $clientId,
        "client_secret" => $clientSecret,
        "redirect_uri" => "https://localhost/fb_oauth_success",
        "code" => $code,
        "grant_type" => "authorization_code",
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

function getTokenGoogle($baseUrl, $clientId, $clientSecret)
{
    ["code" => $code, "state" => $state] = $_GET;
    $queryParams = http_build_query([
        "client_id" => $clientId,
        "client_secret" => $clientSecret,
        "redirect_uri" => "https://localhost/google_oauth_success",
        "code" => $code,
        "grant_type" => "authorization_code",
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

function getTokenDiscord($baseUrl, $clientId, $clientSecret)
{
    //["code" => $code, "state" => $state] = $_GET;
    $code = $_GET['code'];
    $queryParams = http_build_query([
        "client_id" => $clientId,
        "client_secret" => $clientSecret,
        "redirect_uri" => "https://localhost/discord_oauth_success",
        "code" => $code,
        "grant_type" => "authorization_code",
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
    case '/google_oauth_success':
        googlecallback();
        break;
    case '/discord_oauth_success';
        discordcallback();
        break;
    case '/fb_oauth_success':
        fbcallback();
        break;
    default:
        http_response_code(404);
}
