<?php

function validate_weeks_input($input)
{
	$error = [];
	if (empty($input['week_name'])) {
		$error['week_name'] = 'Week name must be required';
	}

	if (empty($input['from_date'])) {
		$error['from_date'] = 'From date must be required';
	}

	if (empty($input['to_date'])) {
		$error['to_date'] = 'To date must be required';
	} elseif (!empty($input['from_date']) && $input['to_date'] <= $input['from_date']) {
		$error['to_date'] = 'To date must be greater than of from date';
	}

	return !empty($error) ? api_response($error) : true;
}

function validate_teams_input($input)
{
	$error = [];
	if (empty($input['team_name'])) {
		$error['team_name'] = 'Team name must be required';
	} else {
		$teams = read_teams();

		if (!empty($teams)) {
			foreach ($teams as $info) {
				if (strtolower($input['team_name']) === strtolower($info['name'])) {
					$error['team_name'] = 'Team name already exists';
					break;
				}
			}
		}
	}

	return !empty($error) ? api_response($error) : true;
}

function validate_employees_input($input)
{
	$error = [];
	if (empty($input['employee_name'])) {
		$error['employee_name'] = 'Employee name must be required';
	}

	if (empty($input['team_name'])) {
		$error['team_name'] = 'Team name must be required';
	} elseif (!empty($input['employee_name'])) {
		$employees = read_employees();

		if (!empty($employees)) {
			foreach ($employees as $info) {
				if (strtolower($input['employee_name']) === strtolower($info['employee_name']) && strtolower($input['team_name']) === strtolower($info['team_name'])) {
					$error['employee_name'] = 'This employee name already exist on this team';
					break;
				}
			}
		}
	}

	return !empty($error) ? api_response($error) : true;
}

function validate_plan_inputs($input, $dates, $teams, $employees)
{
	$error = [];
	foreach ($dates as $date) {
		foreach ($teams as $team) {
			if (!empty($employees[$team['name']])) {
				foreach ($employees[$team['name']] as $employee) {
					$key = 'plan_'.md5($team['name'].'_'.$date.'_'.$employee);

					if (empty($input[$key])) {
						continue;
					}

					$split_data = explode(':', $input[$key]);

					if (isset($split_data[0]) && !isInt($split_data[0])) {
						$error[$key] = 'Hours invalid format';
					} elseif (isset($split_data[0]) && ($split_data[0] < 0 || $split_data[0] > 24)) {
						$error[$key] = 'Hours invalid format';
					}

					if (isset($split_data[1]) && !isInt($split_data[1])) {
						$error[$key] = 'Miniutes invalid format';
					} elseif (isset($split_data[1]) && ($split_data[1] < 0 || $split_data[1] > 59)) {
						$error[$key] = 'Miniutes invalid format';
					}
				}
			}
		}
	}

	return !empty($error) ? api_response($error) : true;
}

function isInt($val)
{
	return (preg_match('/^\d+$/', $val));
}

?>