<?php

		use \Carbon\Carbon;

	function format_weather_day($day, $weather_description, $weather_icon, $temp, $temp_min, $temp_max, $clouds, $cloud_icon, $wind) {
		return "
			$day $weather_description $weather_icon
			Current temperature is: $temp °C, :chart_with_upwards_trend: max: $temp_max °C, :chart_with_downwards_trend:  min: $temp_min °C
			Clouds: $clouds % $cloud_icon
			Wind: $wind kn
		";
	}

	function get_weather($weather_day, $id) {

		$weather_description = $weather_day->weather{0}->description;

		$weather_icon = get_icon($weather_day->weather{0}->main);

		$temp = k_to_c($weather_day->main->temp);

		$temp_max = k_to_c($weather_day->main->temp_max);

		$temp_min = k_to_c($weather_day->main->temp_min);

		$clouds = $weather_day->clouds->all;

		$cloud_icon = get_cloudiness($weather_day->clouds->all);

		$wind = $weather_day->wind->speed;

		$day = format_day($id);

		return format_weather_day($day, $weather_description, $weather_icon, $temp, $temp_min, $temp_max, $clouds, $cloud_icon, $wind);
	}

	function k_to_c($degree) {
		if ( !is_numeric($degree) ) {
			return false; 
		}

		return ($degree - 273.15);
	}

		function format_weather($weather_days, $mentions) {

			$weather_output = get_mentions($mentions);

			$weather_output .= '
			**Forecast '. Carbon::now() .':**
			';

			foreach ($weather_days as $day) {
				$weather_output .= $day;
				
			}

			$weather_output .= "```css
	Have a great day! ```";

		return $weather_output;
		}

	function format_day($id) {
		if ($id == 0) {
			return 'Todays weather:';
		}
		if ($id == 1) {
			return 'The Weather tomorrow:';
		}
			else {
			return "The weather in $id days:";
		}
		}

	function get_icon($main) {
		//get conditions 
		$weather_discord_mapping_json = file_get_contents("./weather_discord_mapping.json");

		$weather_conditions = json_decode($weather_discord_mapping_json);

		foreach ($weather_conditions as $condition => $icon) {
			if (is_numeric(stripos($main, $condition))) {
				unset($weather_discord_mapping_json);
				unset($weather_conditions);
				echo $icon;
				return $icon;
			}
		}
		unset($weather_discord_mapping_json);
		unset($weather_conditions);
		return "";
	}

	function get_cloudiness($percent) {
		//get conditions
		$cloudiness_mapping_json = file_get_contents("./cloudiness_mapping.json");

		$cloudiness_mapping = json_decode($cloudiness_mapping_json);

		foreach ($cloudiness_mapping as $condition => $icon) {
			if ($percent <= intval($condition)) {
				unset($cloudiness_mapping_json);
				unset($cloudiness_mapping);
				return $icon;
			}
		}
		return "";
	}

	//get an @ in front of every name to mention
	function get_mentions ($mentions) {

		$mention_output = '';

		foreach ($mentions as $mention) {
			$mention_output .= "@$mention ";
		}

		return $mention_output;
	}


?>