@extends('admin.layouts.app')

@section('content')
<div class="wrapper">
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Api Setting Form</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Admin</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    @if(Session::has('message'))
    <div class="alert {{ Session::get('alert-class') }} fade-in" id="alert">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    {{ Session::get('message') }}
    </div>
    @endif
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
         <div class="col-md-6">
        <div class="card-body p-0">
        <form action="{{route('setting.store')}}" method="POST">
		    @csrf
		    <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Api End Url</label>
        <input type="text" class="form-control" name="api_end_url" value=""  id="exampleFormControlInput1" placeholder="Api End Url">
        @if($errors->has('api_end_url'))
		    <div class="error text-danger">{{ $errors->first('api_end_url') }}</div>
		    @endif
        </div>
      <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Api Key</label>
        <input type="text" class="form-control" name="api_key" value=""  placeholder="Api Key"></textarea>
        @if($errors->has('api_key'))
		    <div class="error text-danger">{{ $errors->first('api_key') }}</div>
		@endif
      </div>
      <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Fcm Key</label>
        <input type="text" class="form-control" name="fcm_key" value=""  placeholder="Fcm Key"></textarea>
        @if($errors->has('fcm_key'))
		    <div class="error text-danger">{{ $errors->first('fcm_key') }}</div>
		@endif
      </div>
		    <button class="btn btn-danger" type="submit">ADD</button>
		</form>
        </div>
          <!-- ./col -->
        </div>
       </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  
  
  <!-- /.control-sidebar -->
</div>
@endsection
<!-- ./wrapper -->
