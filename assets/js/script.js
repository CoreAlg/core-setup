
// Ajax function
var ajaxCall = function (method, url, data, callback) {

    $("#ajaxloader").show();

    $.ajax({
        url: url,
        type: method,
        data: data,
        complete: function (response) {
            $("#ajaxloader").hide();

            var output = {
                "code": response.status,
                "json": response.responseJSON,
                "text": response.responseText,
                "raw": response,
            };

            callback(output);
        }
    });
};

// Auto Log Off
var autoLogoffCall = function () {
    var logoff = BASE_URL+"/logoff";
    var current_url = window.location.href;
    ajaxCall("GET", logoff, { current_url: current_url }, function (response) {
        window.location.replace(response.json.data);
    });
};

$(document).ready(function (e) {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".alert-dialog").click(function (e) {

        e.preventDefault();

        var message = $(this).attr("data-message");
        $("#delete-alert-modal #deleteAlertFrm").attr("action", $(this).attr("data-action"));
        $("#delete-alert-modal #deleteAlertFrm .modal-body").html(message);
        $("input[name=id]").val($(this).attr("data-id"));
        $("input[name=metaData]").val($(this).attr("data-meta"));

        $("#delete-alert-modal").modal("show");
    });

    $(".select2").select2({
        placeholder: "--Select One--",
        allowClear: true
    });

    // $(".datepicker").datepicker({
    //     autoclose: true,
    //     format: ""
    // });
});