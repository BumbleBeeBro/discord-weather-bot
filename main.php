<?php

	require_once 'vendor/autoload.php';

	include 'helpers.php';

	use \GuzzleHttp\Client;

	//get city_id
	$config_json = file_get_contents("./config.json");

	$weather_config = json_decode($config_json);
	
	//check if city code is set
	if (!isset($weather_config->city_id)) {
		echo 'Please set the city_id in config.json, all available city_ids are here http://bulk.openweathermap.org/sample/city.list.json.gz (file has to be unpacked)';
		exit();
	}

	$city_id = $weather_config->city_id;

	//check if openweatherapi is set
	if (!isset($weather_config->api_key)) {
		echo 'Please set the api_key in config.json, api_key can be requested at https://home.openweathermap.org/users/sign_up';
		exit();
	}

	$api_key = $weather_config->api_key;

	//check if days are set
	if (!isset($weather_config->days)) {
		echo 'Please set the number of days to forecast';
		exit();
	}

	$days = $weather_config->days;

	//check if days are set
	if (!isset($weather_config->discord_webhook)) {
		echo 'Please set the discord_webhook https://support.discordapp.com/hc/de/articles/228383668-Webhooks-verwenden you ll find an englisch version ;)';
		exit();
	}

	$discord_webhook = $weather_config->discord_webhook;

	//check if days are set
	if (!isset($weather_config->mentions)) {
		echo 'Please set the mentions so not everyone gets pinged, taking everyone as default';

		$mentions = ['everyone'];
	} 
	//else needed because it does not break the script if mentions is not set
	else {
		$mentions = $weather_config->mentions;
	}

	$mentions = $weather_config->mentions;

	//initialize new guzzle client
	$client = new Client([
	    // Base URI is used with relative requests
	    'base_uri' => 'api.openweathermap.org/data/2.5/',
	    // You can set any number of default request options.
	    'timeout'  => 5.0,
	]);

while (true) {

	//get weather
	$response = $client->request('GET', 'forecast', ['query' => ['id' => $city_id, 'APPID' => $api_key ]]);

	//check if openweatherapi is set
	if ($response->getStatusCode() !== 200) {
		echo $response->getStatusCode() . 'could not retrieve weather data' . $response->body();
		exit();
	}

	//get the body of the request
	$weather = json_decode($response->getBody());

	$weather_days = array();

	//get forecast for all days
	for ($i=0; $i < $days; $i++) { 
		$weather_day = $weather->list{$i};

		$weather_days[$i] = get_weather($weather_day, $i);
	}

	//append all weather forecasts
	$weather_output = format_weather($weather_days, $mentions);

	//send to Discord webhook in json format
	$response = $response = $client->request('POST', $discord_webhook, ['json' => [
					'content' => $weather_output, 
					'username' => 'discord_weather_bot'
	]]);

	//discord send a 204 back if everything worked.
	if ($response->getStatusCode() !== 204) {
		echo $response->getStatusCode() . 'could not send weather forecast to discord' . $response->body();
		exit();
	} else {
		echo 'congrats it worked';
	}

	sleep(21600);
}
?>