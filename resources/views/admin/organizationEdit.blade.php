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
                <h3 class="card-title">Update Organization</h3>
              </div>
              <form action="{{route('org.update',[$org->id])}}" method="POST">
                @csrf
                  <div class="card-body">

                    <div class="mb-3">
                      <label for="exampleFormControlInput1" class="form-label">Organization Name</label>
                      <input type="text" class="form-control" name="name" value="{{$org->name}}"
                        id="exampleFormControlInput1" placeholder="Name">
                      @if($errors->has('name'))
                      <div class="error text-danger">{{ $errors->first('name') }}</div>
                      @endif
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Email</label>
                      <input type="text" class="form-control" name="email" value="{{$org->email}}" placeholder="email"
                        readonly></textarea>
                      @if($errors->has('email'))
                      <div class="error text-danger">{{ $errors->first('email') }}</div>
                      @endif
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Country</label>
                      <input type="text" class="form-control" name="country" value="{{$org->country}}"
                        placeholder="country"></textarea>
                      @if($errors->has('country'))
                      <div class="error text-danger">{{ $errors->first('country') }}</div>
                      @endif
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Contact Number</label>
                      <input type="text" class="form-control" name="contact_no" value="{{$org->contact_no}}"
                        placeholder="contact"></textarea>
                      @if($errors->has('contact_no'))
                      <div class="error text-danger">{{ $errors->first('contact_no') }}</div>
                      @endif
                    </div>
                    <div class=""><input type="checkbox" value="registration" name="selected_option[]" <?php str_contains($org->selected_option,"registration")!== false ?print"checked=true":print "";?> > &nbsp;&nbsp;<label>Registration </label>&nbsp;&nbsp;<input type="checkbox" value="authentication" name="selected_option[]" <?php str_contains($org->selected_option,"authentication")!== false ?print"checked=true":print "";?> > &nbsp;&nbsp;<label>Authentication </label>&nbsp;&nbsp;<input type="checkbox" value="payment_authorization" name="selected_option[]" <?php  str_contains($org->selected_option,"payment_authorization")!==false?print "checked":print "" ?> >&nbsp;&nbsp;<label>Payment Authorization &nbsp;&nbsp;</label><input type="checkbox" value="consent_signature" name="selected_option[]" <?php  str_contains($org->selected_option,"consent_signature")!==false?print"checked":print "" ?>>&nbsp;&nbsp;<label>Consent Signature </label></div>

                  </div>
                  <div class="card-footer">
                    <a href="{{route('org.list')}}" class="btn btn-warning btn-sm">Cancel</a>
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
