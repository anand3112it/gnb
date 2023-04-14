<?php
require('config/config.php');

$action = $input['action'] ?? '';
if ($action === 'create') {
	validate_employees_input($input);

	$exist_data = read_employees();

	$exist_data[] = [
		'employee_name' => $input['employee_name'],
		'team_name' => $input['team_name'],
	];

	write_employees($exist_data);

	api_response([], 'Employees created successfully', true);
}

$teams = read_teams();

$teams_dd_html = '<option value=\'\'>Select Team</option>';
if (!empty($teams)) {
	foreach ($teams as $info) {
		$teams_dd_html .= '<option value=\''.$info['name'].'\'>'.$info['name'].'</option>';
	}
}

$page = 'employees';
$title = 'Employees';

require('templates/layout/main.php');

?>