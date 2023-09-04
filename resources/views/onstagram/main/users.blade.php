
@extends("onstagram.main.layout")

@push("css")
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.css"/>
    <style>
        img {
            width: 100%;
        }
        .btn-width-custom {
            width: 100px;
        }
    </style>
@endpush

@push("js")
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.js"></script>
    <script src="/onstagram/main/js/user.js?v={{env('VERSION_JS')}}"></script>
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

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table id="userDatatable" class="table table-bordered table-hover" style="width: 100% !important;">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th>ID</th>
                                            <th>Avatar</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Total Posts</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
