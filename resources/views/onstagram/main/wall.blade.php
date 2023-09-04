
@extends("onstagram.main.layout")

@push("css")

@endpush

@push("js")
    <script src="/onstagram/main/js/post.js?v={{env('VERSION_JS')}}"></script>
@endpush

@section("content")
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="py-2">

        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Members</span>
                                <span class="info-box-number">{{$totalUser}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Posts</span>
                                <span class="info-box-number">{{$totalPost}}</span>
                            </div>
                        </div>
                    </div>

                    <!-- fix for small devices only -->
                    <div class="clearfix hidden-md-up"></div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Likes</span>
                                <span class="info-box-number">{{$totalLike}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-comment"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Comments</span>
                                <span class="info-box-number">{{$totalComment}}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if(count($posts) == 0)
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h4>User has no post</h4>
                        </div>
                    </div>
                @else
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
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool deletePost" data-id="{{$post->id}}">
                                            <i class="fas fa-times"></i>
                                        </button>
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
                                                <a style="color: #dddddd" href="{{$post->last_comment->user_comment_last->id}}">{{$post->last_comment->user_comment_last->name . ' ' . $post->last_comment->user_comment_last->last_name}}</a>
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
                @endif
            </div>
        </section>
    </div>
@endsection
