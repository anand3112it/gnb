<?php
require('config/config.php');

$action = $input['action'] ?? '';
if ($action === 'create') {
	validate_teams_input($input);

	$exist_data = read_teams();

	$exist_data[] = [
		'name' => $input['team_name'],
	];

	write_teams($exist_data);

	api_response([], 'Teams created successfully', true);
}

$page = 'teams';
$title = 'Teams';

require('templates/layout/main.php');

?>