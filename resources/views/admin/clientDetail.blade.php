@extends('admin.layouts.app')

@section('content')
<div class="wrapper">
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Client Detail</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-12">
                <pre>
                  @php
                    $json_string = json_encode($result, JSON_PRETTY_PRINT);
                    echo $json_string;
                  @endphp
                </pre>
              <div class="mt-5 mb-3">
                <a href="{{route('clientList')}}" class="btn btn-sm btn-warning">Cancel</a>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section>
  </div>
</div>
@endsection
<!-- ./wrapper -->


