<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onstagram</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="/onstagram/main/asset/plugins/fontawesome-free/css/all.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/onstagram/main/asset/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/onstagram/main/asset/dist/css/adminlte.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <div class="content-wrapper">

        <section class="content">
            <div class="container-fluid">
                @foreach ($posts as $post)
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-6">
                            <!-- Box Comment -->
                            <div class="card card-widget">
                                <div class="card-header">
                                    <div class="user-block">
                                        <img class="img-circle" src="{{$post->user->photo}}" alt="User Image">
                                        <span class="username"><a href="{{route("wall",$post->user->id)}}">{{$post->user->name . ' ' . $post->user->last_name}}</a></span>
                                        <span class="description">{{$post->created_at}}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(isset($post->photo) && strlen($post->photo) > 0)
                                        <a target="_blank" href="{{route("post_detail",$post->id)}}">
                                            <img class="img-fluid pad" src="{{$post->photo}}" alt="Photo">
                                        </a>
                                    @endif

                                    <p>{{$post->description}}</p>

                                    <button type="button" class="btn btn-default btn-sm btnLike" id="btnLike{{$post->id}}" data-id="{{$post->id}}">
                                        @if($post->self_like)
                                            <i class="fas fa-thumbs-up"></i> Liked
                                        @else
                                            <i class="far fa-thumbs-up"></i> Like
                                        @endif
                                    </button>
                                    <a href="{{route("post_detail",$post->id)}}" target="_blank" class="float-right text-muted"><span id="totalLike{{$post->id}}">{{$post->total_like}}</span> likes - <span class="totalCommentSpan" id="totalComment{{$post->id}}">{{$post->total_comment}}</span> comments</a>
                                </div>
                                @if(isset($post->last_comment))
                                    <div class="card-footer card-comments" id="commentSection{{$post->id}}">
                                        <div class="card-comment" id="commentZone{{$post->last_comment->id}}">
                                            <img class="img-circle img-sm" src="{{$post->last_comment->user_comment_last->photo}}" alt="User Image">
                                            <div class="comment-text">
                                            <span class="username">
                                              <a style="color: #dddddd" href="{{route("wall",$post->last_comment->user_comment_last->id)}}">{{$post->last_comment->user_comment_last->name . ' ' . $post->last_comment->user_comment_last->last_name}}</a>
                                              <span class="text-muted float-right">{{$post->last_comment->created_at}}
                                                <i class="fas fa-trash ml-3 deleteComment" data-id="{{$post->last_comment->id}}" id="deleteComment{{$post->last_comment->id}}" style="cursor: pointer"></i>
                                              </span>
                                            </span>
                                                {{$post->last_comment->comment}}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="card-footer">
                                    <img class="img-fluid img-circle img-sm" src="{{$post->myself->photo}}" alt="Alt Text">
                                    <div class="img-push">
                                        <input type="text" class="form-control form-control-sm inputComment" data-id="{{$post->id}}" id="inputComment{{$post->id}}" placeholder="Press enter to post comment">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    </div>
</div>


</body>
</html>
