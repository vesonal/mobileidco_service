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
                <h3 class="card-title">App CMS List</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Page</th>
                      <th style="width: 80px;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(!empty($pages) && $pages->count())
                      @php $i=1; @endphp

                      @foreach ($pages as $key => $pages)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{$pages->pagename}}</td>
                            <td>
                                <a class="btn btn-primary btn-xs" href="{{ route('cms.edit', [$pages->id]) }}">
                                    Edit
                                </a>
                            </td>
                          </tr>
                          @php $i++; @endphp
                      @endforeach

                    @endif
                  </tbody>
                </table>
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
