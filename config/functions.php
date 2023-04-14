<?php

function api_response($error = [], $message = '', $status = false, $data = [])
{
	send_response([
		'status' => $status,
		'message' => $message,
		'error' => $error,
		'data' => $data,
	]);
}

function send_response($data)
{
	die(json_encode($data));
}

function read_weeks()
{
	$data = read_xml('weeks');

	$temp = [];
	if (!empty($data['week']) && !isset($data['week'][0])) {
		$temp[] = $data['week'];
	} elseif (!empty($data['week'])) {
		$temp = $data['week'];
	}

	return $temp;
}

function write_weeks($data)
{
	$xml = new SimpleXMLElement('<?xml version=\'1.0\' encoding=\'UTF-8\'?><weeks></weeks>');

	$i = 1;
	foreach ($data as $week) {
		$new_xml = $xml->addChild('week');
		$new_xml->addChild('name', $week['name']);
		$new_xml->addChild('from', $week['from']);
		$new_xml->addChild('to', $week['to']);
	}

	return $xml->asXML(DATA_URL.'weeks.xml');
}

function read_xml($file)
{
	if (!file_exists(DATA_URL.$file.'.xml')) {
		return [];
	}

	$file = file_get_contents(DATA_URL.$file.'.xml');

	$xml = simplexml_load_string($file);

	$json = json_encode($xml);

	return json_decode($json, true);
}

function read_teams()
{
	$data = read_xml('teams');

	$temp = [];
	if (!empty($data['team']) && !isset($data['team'][0])) {
		$temp[] = $data['team'];
	} elseif (!empty($data['team'])) {
		$temp = $data['team'];
	}

	return $temp;
}

function write_teams($data)
{
	$xml = new SimpleXMLElement('<?xml version=\'1.0\' encoding=\'UTF-8\'?><teams></teams>');

	$i = 1;
	foreach ($data as $team) {
		$new_xml = $xml->addChild('team');
		$new_xml->addChild('name', $team['name']);
	}

	return $xml->asXML(DATA_URL.'teams.xml');
}

function read_employees()
{
	$data = read_xml('employees');

	$temp = [];
	if (!empty($data['employee']) && !isset($data['employee'][0])) {
		$temp[] = $data['employee'];
	} elseif (!empty($data['employee'])) {
		$temp = $data['employee'];
	}

	return $temp;
}

function write_employees($data)
{
	$xml = new SimpleXMLElement('<?xml version=\'1.0\' encoding=\'UTF-8\'?><employees></employees>');

	$i = 1;
	foreach ($data as $employee) {
		$new_xml = $xml->addChild('employee');
		$new_xml->addChild('employee_name', $employee['employee_name']);
		$new_xml->addChild('team_name', $employee['team_name']);
	}

	return $xml->asXML(DATA_URL.'employees.xml');
}

function get_week_by_name($week_name)
{
	$weeks = read_weeks();

	$temp = [];
	if (!empty($weeks)) {
		foreach ($weeks as $week) {
			if ($week['name'] === $week_name) {
				$temp = $week;
				break;
			}
		}
	}

	return $temp;
}

function read_plans()
{
	$data = read_xml('plans');

	$temp = [];
	if (!empty($data['plan']) && !isset($data['plan'][0])) {
		$temp[] = $data['plan'];
	} elseif (!empty($data['plan'])) {
		$temp = $data['plan'];
	}

	return $temp;
}

function write_plans($data)
{
	$xml = new SimpleXMLElement('<?xml version=\'1.0\' encoding=\'UTF-8\'?><plans></plans>');

	$i = 1;
	foreach ($data as $plan) {
		$new_xml = $xml->addChild('plan');
		$new_xml->addChild('week_name', $plan['week_name']);
		$new_xml->addChild('team', $plan['team']);
		$new_xml->addChild('date', $plan['date']);
		$new_xml->addChild('employee', $plan['employee']);
		$new_xml->addChild('hours', $plan['hours']);
	}

	return $xml->asXML(DATA_URL.'plans.xml');
}

function consolidate_plans($plans)
{
	$result = [];
	if (!empty($plans)) {
		foreach ($plans as $plan) {
			$result[$plan['week_name']][$plan['team']][$plan['date']][$plan['employee']] = $plan['hours'];

			if (!isset($result[$plan['week_name']][$plan['team']][$plan['date']]['total'])) {
				$result[$plan['week_name']][$plan['team']][$plan['date']]['total'] = 0;
			}

			$result[$plan['week_name']][$plan['team']][$plan['date']]['total'] = calculate_hours($result[$plan['week_name']][$plan['team']][$plan['date']]['total'], $plan['hours']);

			if (!isset($result[$plan['week_name']][$plan['team']][$plan['employee']]['total'])) {
				$result[$plan['week_name']][$plan['team']][$plan['employee']]['total'] = 0;
			}

			$result[$plan['week_name']][$plan['team']][$plan['employee']]['total'] = calculate_hours($result[$plan['week_name']][$plan['team']][$plan['employee']]['total'], $plan['hours']);

			if (!isset($result[$plan['week_name']][$plan['team']]['total'])) {
				$result[$plan['week_name']][$plan['team']]['total'] = 0;
			}

			$result[$plan['week_name']][$plan['team']]['total'] = calculate_hours($result[$plan['week_name']][$plan['team']]['total'], $plan['hours']);

			if (!isset($result[$plan['week_name']][$plan['date']]['total'])) {
				$result[$plan['week_name']][$plan['date']]['total'] = 0;
			}

			$result[$plan['week_name']][$plan['date']]['total'] = calculate_hours($result[$plan['week_name']][$plan['date']]['total'], $plan['hours']);

			if (!isset($result[$plan['week_name']]['total'])) {
				$result[$plan['week_name']]['total'] = 0;
			}

			$result[$plan['week_name']]['total'] = calculate_hours($result[$plan['week_name']]['total'], $plan['hours']);
		}
	}

	return $result;
}

function calculate_hours($hour1, $hour2)
{
	$hour1_array = explode(':', $hour1);
	$hour2_array = explode(':', $hour2);

	$hour = !empty($hour1_array[0]) ? $hour1_array[0] : 0;
	if (!empty($hour2_array[0])) {
		$hour += $hour2_array[0];
	}

	$minute = 0;
	if (!empty($hour1_array[1]) && !empty($hour2_array[1])) {
		$minute = $hour1_array[1] + $hour2_array[1];
	} elseif (!empty($hour1_array[1])) {
		$minute = $hour1_array[1];
	} elseif (!empty($hour2_array[1])) {
		$minute = $hour2_array[1];
	}

	if ($minute >= 60) {
		$hour += intdiv($minute, 60);
		$minute = $minute % 60;
	}

	return $hour.':'.$minute;
}

function between_two_dates($from, $to)
{
	$period = new DatePeriod(
		new DateTime($from),
		new DateInterval('P1D'),
		new DateTime(date('Y-m-d', strtotime($to.' +1 days')))
	);

	$result = [];
	foreach ($period as $key => $value) {
	    $result[] = $value->format('Y-m-d');       
	}

	return $result;
}

function map_employees()
{
	$employees = read_employees();

	$result = [];
	if (!empty($employees)) {
		foreach ($employees as $employee) {
			$result[$employee['team_name']][] = $employee['employee_name'];
		}
	}

	return $result;
}

?>