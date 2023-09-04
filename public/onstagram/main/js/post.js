
$('body').on( "click", ".btnLike", function() {
    let postId = $(this).data('id');

    let btnLike = $("#btnLike"+postId);

    const data = {};
    data.post_id = postId;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/admin/post/like',
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        traditional: true,
        processData: false,
        type: 'POST',
        beforeSend: function() {
            btnLike.prop("disabled",true);
        },
        success: function (response) {
            btnLike.prop("disabled",false);
            if (response.result === 'success'){
                btnLike.empty();
                let html = createButtonLike(response.message);
                btnLike.html(html);

                let totalLike = $("#totalLike"+postId);
                totalLike.text(response.data);
            } else {
                notify.open({ type: 'error', message: response.message });
            }
        },
        error: function (response) {
            notify.open({ type: 'error', message: 'Unexpected error.' });
            btnLike.prop("disabled",false);
        }
    });
});

function createButtonLike(message){
    let html = "";
    if (message === "Liked!"){
        html += '<i class="fas fa-thumbs-up"></i> Liked';
    } else {
        html += '<i class="far fa-thumbs-up"></i> Like';
    }
    return html;
}

$(".inputComment").keyup(function(event) {
    if (event.keyCode === 13) {
        let postId = $(this).data('id');
        let comment = $(this).val();
        if (comment.trim().length <= 0){
            return;
        }
        let inputComment = $('#inputComment'+postId);
        let commentSection = $('#commentSection'+postId);

        const data = {};
        data.post_id = postId;
        data.comment = comment;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/admin/post/comment',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            traditional: true,
            processData: false,
            type: 'POST',
            beforeSend: function() {
                inputComment.prop("disabled",true);
            },
            success: function (response) {
                inputComment.prop("disabled",false);
                if (response.result === 'success'){
                    let html = createComment(response.data);
                    commentSection.append(html);

                    let totalComment = $("#totalComment"+postId);
                    totalComment.text(response.totalComment);
                } else {
                    notify.open({ type: 'error', message: response.message });
                }
                inputComment.val("");
            },
            error: function (response) {
                notify.open({ type: 'error', message: 'Unexpected error.' });
                inputComment.prop("disabled",false);
            }
        });
    }
});

function createComment(data){
    let html = "";
    html += '<div class="card-comment" id="commentZone'+data.id+'">';
    html += '<img class="img-circle img-sm" src="'+data.user.photo+'" alt="User Image">';
    html += '<div class="comment-text">';
    html += '<span class="username">';
    html += data.user.name + " " + data.user.last_name;
    html += '<span class="text-muted float-right">'+data.created_at;
    html += '<i class="fas fa-trash ml-3 deleteComment" data-id="'+data.id+'" id="deleteComment'+data.id+'" style="cursor: pointer"></i>';
    html += '</span>';
    html += '</span>';
    html += data.comment;
    html += '</div>';
    html += '</div>';
    return html;
}

$('body').on( "click", ".deleteComment", function() {
    let comment_id = $(this).data('id');
    let commentZone = $("#commentZone"+comment_id);

    Swal.fire({
        icon: 'warning',
        title: "Delete this comment?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const data = {};
            data.comment_id = comment_id;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/admin/comment/delete',
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                traditional: true,
                processData: false,
                type: 'POST',
                beforeSend: function() {

                },
                success: function (response) {
                    if (response.result === 'success'){
                        commentZone.hide();
                        let totalCommentSpan = $(".totalCommentSpan");
                        totalCommentSpan.text(response.data);
                    } else {
                        notify.open({ type: 'error', message: response.message });
                    }
                },
                error: function (response) {
                    notify.open({ type: 'error', message: 'Unexpected error.' });
                }
            });
        }
    });
});

$('body').on( "click", ".deletePost", function() {
    let post_id = $(this).data('id');

    Swal.fire({
        icon: 'warning',
        title: "Delete this post?",
        showCancelButton: true,
        confirmButtonText: "Confirm",
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const data = {};
            data.post_id = post_id;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/admin/post/delete',
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                traditional: true,
                processData: false,
                type: 'POST',
                beforeSend: function() {

                },
                success: function (response) {
                    if (response.result === 'success'){
                        location.reload();
                    } else {
                        notify.open({ type: 'error', message: response.message });
                    }
                },
                error: function (response) {
                    notify.open({ type: 'error', message: 'Unexpected error.' });
                }
            });
        }
    });
});


