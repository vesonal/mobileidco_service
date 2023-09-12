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
                <h3 class="card-title">Add User</h3>
              </div>
              <form action="{{route('userStore') }}" method="POST">
                @csrf
                  <div class="card-body">

                    <div class="mb-3">
                      <label for="exampleFormControlInput1" class="form-label">Name</label>
                      <input type="text" class="form-control" name="name" value="" id="exampleFormControlInput1"
                        placeholder="Name">
                      @if($errors->has('name'))
                      <div class="error text-danger">{{ $errors->first('name') }}</div>
                      @endif
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Email</label>
                      <input type="text" class="form-control" name="email" value="" placeholder="email"></textarea>
                      @if($errors->has('email'))
                      <div class="error text-danger">{{ $errors->first('email') }}</div>
                      @endif
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Password</label>
                      <input type="password" class="form-control" name="password" value=""
                        autocomplete="current-password">
                      @if($errors->has('password'))
                      <div class="error text-danger">{{ $errors->first('password') }}</div>
                      @endif
                    </div>

                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Confirm Password</label>
                      <input type="password" class="form-control" name="password_confirmation" required value=""
                        autocomplete="current-password">
                      @if($errors->has('password'))
                      <div class="error text-danger">{{ $errors->first('password') }}</div>
                      @endif
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
