<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form action="teams.php" name="create_form" id="create_form" method="post">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="text-center to_p_h">
                            <h3>Create Weeks</h3>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="inputTeamName" class="form-label">Team Name</label>
                        <input type="text" class="form-control" name="team_name" id="team_name" value="">
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
create = () => {
    var ipData = getFormDataByID("create_form");
    ipData.append('action', 'create');

    return callAjax({
        url: "teams.php",
        formData: ipData, 
    }, function(resJson){
        if (resJson.status === true) {
            loadSwal('success', resJson.message, "teams.php");
        } else {
            loadIpErrors(resJson.error);
        }
    });
}
</script>