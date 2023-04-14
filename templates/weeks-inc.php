<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form action="weeks.php" name="create_form" id="create_form" method="post">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="text-center to_p_h">
                            <h3>Create Weeks</h3>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="inputWeekName" class="form-label">Week Name</label>
                        <input type="text" class="form-control" name="week_name" id="week_name" value="">
                        <span class="error" id="err_week_name"></span>
                    </div>
                    <div class="col-md-3">
                        <label for="inputFromDate" class="form-label">From Date</label>
                        <input type="date" class="form-control" name="from_date" id="from_date" value="">
                        <span class="error" id="err_from_date"></span>
                    </div>
                    <div class="col-md-3">
                        <label for="inputFromDate" class="form-label">To Date</label>
                        <input type="date" class="form-control" name="to_date" id="to_date" value="">
                        <span class="error" id="err_to_date"></span>
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
        url: "weeks.php",
        formData: ipData, 
    }, function(resJson){
        if (resJson.status === true) {
            loadSwal('success', resJson.message, "weeks.php");
        } else {
            loadIpErrors(resJson.error);
        }
    });
}
</script>