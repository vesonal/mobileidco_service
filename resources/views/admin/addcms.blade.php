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
            <h1 class="m-0">Manage Cms</h1>
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
        <form action="{{route('cms.store')}}" method="POST">
		    @csrf
		    <div class="mb-3 expiry">
        <label for="exampleFormControlInput1" class="form-label">Pagename</label>
        <input type="text" class="form-control" name="pagename" value=""  id="exampleFormControlInput1"  placeholder="pagename">
        @if($errors->has('name'))
		    <div class="error text-danger">{{ $errors->first('pagename') }}</div>
		@endif
      </div>

      <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Title</label>
        <input type="text" class="form-control" name="title" value=""  placeholder="Enter Page Title"></textarea>
        @if($errors->has('email'))
		    <div class="error text-danger">{{ $errors->first('title') }}</div>
		@endif
      </div>
       <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Keywords</label>
        <input type="text" class="form-control" name="keyword" value=""  placeholder="keyword"></textarea>
        @if($errors->has('country'))
		    <div class="error text-danger">{{ $errors->first('keywords') }}</div>
		@endif
      </div>
       <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Short description</label>
        <input type="text" class="form-control" name="short_description" value=""  placeholder="short description"></textarea>
        @if($errors->has('country'))
		    <div class="error text-danger">{{ $errors->first('short_description') }}</div>
		@endif
      </div>
       <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Description</label>
        <textarea class=" form-control summernote" name="description" placeholder="Description"></textarea>
        @if($errors->has('contact_no'))
		    <div class="error text-danger">{{ $errors->first('description') }}</div>
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
