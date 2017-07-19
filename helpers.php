<?php
	function format_weather_day($day, $temp, $temp_min, $temp_max, $weather_description, $clouds, $wind) {
		return "

			$day $weather_description
			Current temperature is: $temp °C, max: $temp_max °C, min: $temp_min °C
			Clouds: $clouds %
			Wind: $wind kn
		";
	}

	function get_weather($weather_day, $id) {
		$temp = k_to_c($weather_day->main->temp);

		$temp_max = k_to_c($weather_day->main->temp_max);

		$temp_min = k_to_c($weather_day->main->temp_min);

		//doesnt work
		$weather_description = $weather_day->weather{0}->description;

		$clouds = $weather_day->clouds->all;

		$wind = $weather_day->wind->speed;

		$day = format_day($id);

		return format_weather_day($day, $temp, $temp_min, $temp_max, $weather_description, $clouds, $wind);
	}

	function k_to_c($degree) {
		if ( !is_numeric($degree) ) {
			return false; 
		}

		return ($degree - 273.15);
	}

		function format_weather($weather_days) {

			$weather_output = "**Forecast:**
			";

			foreach ($weather_days as $day) {
				$weather_output .= $day;
				
			}

			$weather_output .= "```css Have a great day! ```";

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
?>