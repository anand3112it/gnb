<?php
require('config/config.php');

$action = $input['action'] ?? '';
if ($action === 'create') {
	$input['from_date'] = !empty($input['from_date']) ? date('Y-m-d', strtotime($input['from_date'])) : '';
	$input['to_date'] = !empty($input['to_date']) ? date('Y-m-d', strtotime($input['to_date'])) : '';

	validate_weeks_input($input);

	$exist_data = read_weeks();

	$exist_data[] = [
		'name' => $input['week_name'],
		'from' => $input['from_date'],
		'to' => $input['to_date'],
	];

	write_weeks($exist_data);

	api_response([], 'Weeks created successfully', true);
}

$page = 'weeks';
$title = 'Weeks';

require('templates/layout/main.php');

?>