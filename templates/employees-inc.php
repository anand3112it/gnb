<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form action="employees.php" name="create_form" id="create_form" method="post">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="text-center to_p_h">
                            <h3>Create Weeks</h3>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="inputEmployeeName" class="form-label">Employee Name</label>
                        <input type="text" class="form-control" name="employee_name" id="employee_name" value="">
                        <span class="error" id="err_employee_name"></span>
                    </div>
                    <div class="col-md-4">
                        <label for="inputTeamName" class="form-label">Team Name</label>
                        <select class="form-control" name="team_name" id="team_name">
                            <?=$teams_dd_html?>
                        </select>
                        <span class="error" id="err_team_name"></span>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary sub-btn" onclick="create()">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("#team_name").select2({});
});

create = () => {
    var ipData = getFormDataByID("create_form");
    ipData.append('action', 'create');

    return callAjax({
        url: "employees.php",
        formData: ipData, 
    }, function(resJson){
        if (resJson.status === true) {
            loadSwal('success', resJson.message, "employees.php");
        } else {
            loadIpErrors(resJson.error);
        }
    });
}
</script>