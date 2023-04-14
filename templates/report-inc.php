<div class="container mb-3">
    <div class="row">
        <div class="col-md-12">
            <form action="index.php" name="create_form" id="create_form" method="post">
                <div class="table-responsive">
                    <div class="table-wrap" style="padding-top: 0px;">
                        <table class="table table-bordered table-striped table-hover js-basic-example dataTable display nowrap" style="width:100%" id="dataTableBasic">
                            <thead class="thead-light">
                                <tr style="background-color: #87CEEB;">
                                    <td colspan="9" style="text-align: center;">
                                        <select class="form-control" name="week" id="week">
                                            <?=$weeks_dd_html?>
                                        </select>
                                    </td> 
                                </tr>
                            </thead>
                            <tbody id="plan_for_the_week_box">
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("#week").select2({});

    $("#week").on("change", function(){
        $("#plan_for_the_week_box").html("");
        if ($(this).val() != '') {
            getPlanForTheWeekDetails($(this).val());
        }
    });
});

create = () => {
    var ipData = getFormDataByID("create_form");
    ipData.append('action', 'create');

    return callAjax({
        url: "index.php",
        formData: ipData, 
    }, function(resJson){
        if (resJson.status === true) {
            getPlanForTheWeekDetails($("#week").val());
        } else {
            loadIpErrors(resJson.error);
        }
    });
}

getPlanForTheWeekDetails = (week) => {
    var ipData = new FormData();
    ipData.append('week', week);
    ipData.append('action', 'get_plan_for_the_week_details');

    return callAjax({
        url: "index.php",
        formData: ipData, 
    }, function(resJson){
        if (resJson.status === true) {
            $("#plan_for_the_week_box").html(resJson.data.html);
        } else {
            loadIpErrors(resJson.error);
        }
    }, 2, 'plan_for_the_week_box');
}
</script>