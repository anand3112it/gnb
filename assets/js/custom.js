loadIpErrors = (errorMsg) => {
	if (typeof errorMsg == "string") {
        loadSwal('error', errorMsg);
    } else {
        if (errorMsg.common != undefined) {
            loadSwal('error', errorMsg.common);
        } else {
            $.each(errorMsg, function(ind, error){
                $("#err_"+ind+"").text(error);
            });
        }
    }

    return true;
}

removeIpErrors = () => {
	$(".error").html("");

	return true;
}

loadSwal = (type, msg = '', redirect = '', callback = '', swalTitleText = '') => {
    var swalTitle = '';
    if (type === 'success') {
        swalTitle = (swalTitleText != '') ? swalTitleText : 'Success';
    } else if (type === 'error') {
        swalTitle = 'Error';
    } else if (type === 'info') {
        return swal(msg);
    } else if (type === 'fill_required') {
        return swal('', msg, 'error').then((confirm) => {
            callback();
        });
    }

    if (redirect == '') {
        swal(swalTitle, msg, type);
    } else {
        swal(swalTitle, msg, type).then((close) => {
            window.location.href = redirect;
        });
    }
}

loaderOpenAjax = () => {
    swal({
        title: 'Please Wait...',
        buttons: false,      
        closeOnClickOutside: false
    });
}

loaderCloseAjax = () => {
    swal.close();
}

loadSpinnerAjax = (divId, type = 1) => {
    if (type === 1) {
        var html = '';
        html += '<div class=\'d-flex justify-content-center\' style=\'padding: 150px;\'>';
        html += '<div class=\'spinner-border\' role=\'status\'>';
        html += '<span class=\'visually-hidden\'></span>';
        html += '</div>';
        html += '</div>';

        $('#'+divId).html(html);
    } else {
        $('#'+divId).html('');
    }
}

initLoaderAjax = (type, domId) => {
    if (type === 1) {
        loaderOpenAjax();
    } else if (type === 2) {
        loadSpinnerAjax(domId);
    }

    return true;
}

finalLoaderAjax = (type, domId) => {
    if (type === 1) {
        loaderCloseAjax();
    } else if (type === 2) {
        loadSpinnerAjax(domId, 2);
    }

    return true;
}

callAjax = (obj, callback, loader = 1, domId = '') => {
    initLoaderAjax(loader, domId);
    removeIpErrors();

    $.ajax({
        type: "POST",
        url: obj.url,
        data: obj.formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response){   
            let resJson = JSON.parse(response);
            finalLoaderAjax(loader, domId);
            callback(resJson);
        }  
    });

    return true;
}

getFormDataByID = (formId) => {
    var postdata = $("#"+formId).serializeArray();

    var formData = new FormData();
    $.each(postdata, function(index, data){
        formData.append(data.name, data.value);
    });

    return formData;
}