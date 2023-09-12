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
                <h3 class="card-title">Organization Detail</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>API Name</th>
                      <th>30D</th>
                      <th>90D</th>
                      <th style="width: 80px;">Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(!empty($finalApiDetails) && count($finalApiDetails)>0)
                      @php $i=1; @endphp

                      @foreach ($finalApiDetails as $apidetailSingle)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{$apidetailSingle['api_url']}}</td>
                            <td>{{$apidetailSingle['count30Days']}}</td>
                            <td>{{$apidetailSingle['count90Days']}}</td>
                            <td>{{$apidetailSingle['countTotalDays']}}</td>
                          </tr>
                          @php $i++; @endphp
                      @endforeach

                    @else
                      <tr>
                        <td colspan="3">No record found.</td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                <a href="{{route('org.list')}}" class="btn btn-warning btn-sm">Cancel</a>
              </div>
              <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                </ul>
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
