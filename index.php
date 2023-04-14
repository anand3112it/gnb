<?php
require('config/config.php');

$action = $input['action'] ?? '';
if ($action === 'get_plan_for_the_week_details') {
	if (empty($input['week'])) {
		api_response('Please choose the week');
	}

	$week = get_week_by_name($input['week']);
	if (empty($week)) {
		api_response('Please choose the valid week');
	}

	$dates = between_two_dates($week['from'], $week['to']);
	if (empty($dates)) {
		api_response('Invalid dates');
	}

	$teams = read_teams();
	if (empty($teams)) {
		api_response('Teams not found');
	}

	$employees = map_employees();
	if (empty($employees)) {
		api_response('Employees not found');
	}

	$plans = consolidate_plans(read_plans());

	$html = '';
	$html .= '<tr style=\'background-color: #87CEEB;\'>';
	$html .= '<td>#</td>';
	foreach ($dates as $date) {
		$html .= '<td>'.date('D d M', strtotime($date)).'</td>';
	}
	$html .= '<td>total</td>';
	$html .= '</tr>';

	foreach ($teams as $team) {
		$html .= '<tr style=\'background-color: #90EE90;\'>';
		$html .= '<td>'.$team['name'].'</td>';
		foreach ($dates as $date) {
			$html .= '<td>'.($plans[$week['name']][$team['name']][$date]['total'] ?? '00:00').'</td>';
		}
		$html .= '<td>'.($plans[$week['name']][$team['name']]['total'] ?? '00:00').'</td>';
		$html .= '</tr>';

		if (!empty($employees[$team['name']])) {
			foreach ($employees[$team['name']] as $employee) {
				$html .= '<tr>';
				$html .= '<td>'.$employee.'</td>';
				foreach ($dates as $date) {
					$key = 'plan_'.md5($team['name'].'_'.$date.'_'.$employee);

					$html .= '<td><input type=\'text\' class=\'form-control\' name=\''.$key.'\' id=\''.$key.'\' value=\''.($plans[$week['name']][$team['name']][$date][$employee] ?? '00:00').'\' onchange="create()"><span class=\'error\' id=\'err_'.$key.'\'></span></td>';
				}
				$html .= '<td>'.($plans[$week['name']][$team['name']][$employee]['total'] ?? '00:00').'</td>';
				$html .= '</tr>';
			}
		}
	}

	$html .= '<tr style=\'background-color: #90EE90;\'>';
	$html .= '<td>Total</td>';
	foreach ($dates as $date) {
		$html .= '<td>'.($plans[$week['name']][$date]['total'] ?? '00:00').'</td>';
	}
	$html .= '<td>'.($plans[$week['name']]['total'] ?? '00:00').'</td>';
	$html .= '</tr>';

	api_response([], 'Plan details fetch successfully', true, [
		'html' => $html
	]);
} elseif ($action === 'create') {
	if (empty($input['week'])) {
		api_response('Please choose the week');
	}

	$week = get_week_by_name($input['week']);
	if (empty($week)) {
		api_response('Please choose the valid week');
	}

	$dates = between_two_dates($week['from'], $week['to']);
	if (empty($dates)) {
		api_response('Invalid dates');
	}

	$teams = read_teams();
	if (empty($teams)) {
		api_response('Teams not found');
	}

	$employees = map_employees();
	if (empty($employees)) {
		api_response('Employees not found');
	}

	validate_plan_inputs($input, $dates, $teams, $employees);

	$plans = read_plans();
	$map_plans = [];
	foreach ($plans as $ind => $plan) {
		$key = 'plan_'.md5($plan['team'].'_'.$plan['date'].'_'.$plan['employee']);

		if ($week['name'] === $plan['week_name'] && isset($input[$key])) {
			$plans[$ind]['hours'] = $input[$key];
		}

		$map_plans[md5($plan['week_name'].'_'.$plan['team'].'_'.$plan['date'].'_'.$plan['employee'])] = $plan;
	}

	$new_plans = [];
	foreach ($dates as $date) {
		foreach ($teams as $team) {
			if (!empty($employees[$team['name']])) {
				foreach ($employees[$team['name']] as $employee) {
					$key = md5($week['name'].'_'.$team['name'].'_'.$date.'_'.$employee);
					if (isset($map_plans[$key])) {
						continue;
					}

					$ip_key = 'plan_'.md5($team['name'].'_'.$date.'_'.$employee);
					if (empty($input[$ip_key])) {
						continue;
					}

					$new_plans[] = [
						'week_name' => $week['name'],
						'team' => $team['name'],
						'date' => $date,
						'employee' => $employee,
						'hours' => $input[$ip_key],
					];
				}
			}
		}
	}

	$plans = array_merge($plans, $new_plans);

	write_plans($plans);

	api_response([], 'Plans created successfully', true);
}

$weeks = read_weeks();

$weeks_dd_html = '<option value=\'\'>Select Weeks</option>';
if (!empty($weeks)) {
	foreach ($weeks as $week) {
		$weeks_dd_html .= '<option value=\''.$week['name'].'\'>'.$week['name'].' ('.date('d M Y', strtotime($week['from'])).' - '.date('d M Y', strtotime($week['to'])).')</option>';
	}
}

$page = 'report';
$title = 'Plan for the week';

require('templates/layout/main.php');

?>