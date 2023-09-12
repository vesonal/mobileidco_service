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
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Add Billing</h3>
              </div>

              <form action="{{route('billing.store')}}" method="POST">
                @csrf
                  <div class="card-body">
                    <div class="mb-3">

                    <!-- Radio-1 -->
                      <label for="exampleFormControlInput1" class="form-label">Expiry Time
                      <div class="icheck-primary d-inline">
                        <input type="radio" id="expiry" name="expiration_time" checked="checked">
                        <label for="expiry">
                        </label>
                      </div>

                    <!-- Radio-2 -->
                      <label for="exampleFormControlInput1" class="form-label" style="padding-left:70px;">Usage
                      <div class="icheck-primary d-inline">
                        <input type="radio" id="usage" name="expiration_time">
                        <label for="usage">
                        </label>
                      </div>

                    </div>
                    <!-- Radio-1 content-->
                    <div class="mb-3 expiry">
                        <label for="exampleFormControlInput1" class="form-label">Expiry Time</label>
                        <input type="text" class="form-control" name="expiration_time" value=""  id="exampleFormControlInput1"  placeholder="Expiration Time">
                        @if($errors->has('name'))
                            <div class="error text-danger">{{ $errors->first('expiration_time') }}</div>
                        @endif
                    </div>

                    <!-- Radio-2 content-->
                    <div class="usage-section" style="display: none;">
                      <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Usage Interval</label>
                        <input type="text" class="form-control" name="usage" value=""
                          placeholder="Usage Interval"></textarea>
                        @if($errors->has('email'))
                        <div class="error text-danger">{{ $errors->first('usage') }}</div>
                        @endif
                      </div>
                      <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Authentication</label>
                        <input type="text" class="form-control" name="authorization" value=""
                          placeholder="Authorization"></textarea>
                        @if($errors->has('country'))
                        <div class="error text-danger">{{ $errors->first('authorization') }}</div>
                        @endif
                      </div>
                      <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Payment Authorization</label>
                        <input type="text" class="form-control" name="payment" value="" placeholder="Payment"></textarea>
                        @if($errors->has('contact_no'))
                        <div class="error text-danger">{{ $errors->first('payment') }}</div>
                        @endif
                      </div>
                      <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Consent Sign</label>
                        <input type="text" class="form-control" name="consent" value="" placeholder="Consent"></textarea>
                        @if($errors->has('contact_no'))
                        <div class="error text-danger">{{ $errors->first('contact_no') }}</div>
                        @endif
                      </div>
                    </div>

                  </div>
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
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
