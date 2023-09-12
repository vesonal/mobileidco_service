@extends('admin.layouts.app')

@section('content')
<div class="wrapper">
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
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
                <h3 class="card-title">Client List</h3>
              </div>
              <div class="card-body table-responsive">
                <table id="clientlist" class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Client Id</th>
                      <th>Device Name</th>
                      <th style="width: 142px;">Action</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Client Id</th>
                      <th>Device Name</th>
                      <th style="width: 142px;">Action</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <!-- ./col -->
        </div>
       
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  
  <!-- /.control-sidebar -->
</div>
@endsection

@section('pagesscripts')
  <script>
   $('#clientlist').DataTable({
      "processing": true,
      "serverSide": true,
      "pageLength":25,
      "columnDefs": [
        { "orderable": false, "targets": '_all' }
      ],
      "ajax":{
        "dataType": "json",
        "type": "POST",
        "url": "{{route('clients.getAllClient')}}",
        "data":{ _token: $('meta[name="csrf-token"]').attr('content')}
       },
      "columns": [
        { "data": "id" },
        { "data": "device_id" },
        { "data": "device_name" },
        { "data": "action" }
      ],
      order:[0,'desc'],
    });
  </script>
@stop