@extends('admin.layouts.app')
@section('content')
<div class="wrapper">
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
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
                <h3 class="card-title">Update User</h3>
              </div>
              <form action="{{route('userUpdate', [$adminData->id]) }}" method="POST">
                @csrf
                  <div class="card-body">

                    <div class="mb-3">
                      <label for="exampleFormControlInput1" class="form-label">name</label>
                      <input type="text" class="form-control" name="name" value="{{$adminData->name}}"  id="exampleFormControlInput1" placeholder="Client Id">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">email</label>
                        <input type="text" class="form-control" name="email" value="{{$adminData->email}}" readonly id="exampleFormControlTextarea1" />
                    </div>

                  </div>
                  <div class="card-footer">
                    <a href="{{route('userList')}}" class="btn btn-warning btn-sm">Cancel</a>
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
