
$(document).ready(function () {
    createUserDatatable();
});

function createUserDatatable(){
    $('#userDatatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/user-datatable',
            dataType: 'json',
            type: "POST",
            data:{
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { title: 'ID', "bSortable": false, "defaultContent": '', 'width': '5%' },
            { title: 'Avatar',"bSortable": false, "defaultContent": '', 'width': '15%' },
            { title: 'Full Name',"bSortable": false, "defaultContent": '', 'width': '20%' },
            { title: 'Email',"bSortable": false, "defaultContent": '', 'width': '20%' },
            { title: 'Total Posts',"bSortable": false, "defaultContent": '', 'width': '15%' },
            { title: 'Created At',"bSortable": false, "defaultContent": '', 'width': '15%' },
            { title: 'Action',"bSortable": false, "defaultContent": '', 'width': '10%' }
        ],
        createdRow: function (row, data, dataIndex) {
            $(row).children('td').eq(0).append(data.id);
            let col1Html = '';
            col1Html += '<img src="http://localhost:8000'+data.avatar+'" alt="avatar" />';
            $(row).children('td').eq(1).html(col1Html);

            let col2Html = '';
            col2Html += '<a href="/admin/user/wall/'+data.id+'">'+data.full_name+'</a>';
            $(row).children('td').eq(2).html(col2Html);

            $(row).children('td').eq(3).html(data.email);

            $(row).children('td').eq(4).html(data.total_post);

            $(row).children('td').eq(5).html(data.created_at);

            let col5Html = '<div class="actionZone'+data.id+'">';
            col5Html += createButtonAction(data.is_ban,data.id);
            col5Html += '</div>';
            $(row).children('td').eq(6).html(col5Html);
        }
    });
}

$('body').on( "click", ".actionBtn", function() {
    let userId = $(this).data('id');
    let action = $(this).data('action');

    let title = "Ban this user?";
    let is_ban = 1;
    if (action === "unban"){
        title = "Unban this user?";
        is_ban = 0;
    }
    Swal.fire({
        icon: 'warning',
        title: title,
        showCancelButton: true,
        confirmButtonText: "Confirm",
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            let btnAction = $("#btnAction"+userId);
            let btnLoading = $("#btnLoading"+userId);
            let actionZone = $(".actionZone"+userId);

            const userData = {};
            userData.userId = userId;
            userData.action = is_ban;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/admin/user-action',
                data: JSON.stringify(userData),
                contentType: "application/json; charset=utf-8",
                traditional: true,
                processData: false,
                type: 'POST',
                beforeSend: function() {
                    btnAction.prop("disabled",true);
                    btnLoading.show();
                },
                success: function (response) {
                    notify.open({ type: response.result, message: response.message });
                    btnAction.prop("disabled",false);
                    btnLoading.hide();

                    if (response.result === 'success'){
                        actionZone.empty();
                        let html = createButtonAction(is_ban,userId);
                        actionZone.html(html);
                    }
                },
                error: function (response) {
                    notify.open({ type: 'error', message: 'Unexpected error.' });
                    btnAction.prop("disabled",false);
                    btnLoading.hide();
                }
            });
        }
    })
});

function createButtonAction(is_ban,id){
    let html = '';
    if (is_ban === 0){
        html += '<button class="btn btn-sm btn-danger actionBtn px-2 btn-width-custom" id="btnAction'+id+'" data-id="'+id+'" data-action="ban">';
        html += '<i id="btnLoading'+id+'" class="fa fa-circle-o-notch fa-spin mr-2" style="display: none"></i>Ban';
        html += '</button>';
    }
    if (is_ban === 1){
        html += '<button class="btn btn-sm btn-warning actionBtn px-2 btn-width-custom" id="btnAction'+id+'" data-id="'+id+'" data-action="unban">';
        html += '<i id="btnLoading'+id+'" class="fa fa-circle-o-notch fa-spin mr-2" style="display: none"></i>Unban';
        html += '</button>';
    }
    return html;
}
