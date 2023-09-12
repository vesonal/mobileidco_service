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
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('clientList')}}">Home</a></li>
              <li class="breadcrumb-item active">Update Client</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            @if(Session::has('message'))
                <div class="alert {{ Session::get('alert-class') }} fade-in" id="alert">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                  {{ Session::get('message') }}
                </div>
              @endif
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Update Client</h3>
              </div>
              <form action="{{route('clientUpdate', [$result->client_id]) }}" method="POST">
                @csrf
                  <div class="card-body">

                    <div class="mb-3">
                      <label for="exampleFormControlInput1" class="form-label">Client Id</label>
                      <input type="text" class="form-control" name="client_id" value="{{$result->client_id}}" readonly id="exampleFormControlInput1" placeholder="Client Id">
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Device Name</label>
                      <input type="text" class="form-control" name="device_name" value="{{$result->client_name}}" id="exampleFormControlTextarea1"></textarea>
                    </div>

                  </div>
                  <div class="card-footer">
                    <a href="{{route('clientList')}}" class="btn btn-warning btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                  </div>
                  
              </form>


            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- /.control-sidebar -->
</div>

@endsection
<!-- ./wrapper -->
