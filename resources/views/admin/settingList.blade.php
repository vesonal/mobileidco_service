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
            <h1 class="m-0">APi Setting List</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Admin </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
         
        <div class="card-body p-0">
         <!-- <a class="btn btn-success btn-sm pull-right" href="{{ route('setting.create') }}">
                              <i class="fas fa-folder">
                              </i>
                              Add New Setting 
            </a> -->
          <table class="table table-striped projects">
              <thead>
                  <tr>
                      <th style="width: 1%">
                          S.no
                      </th>
                      <th style="width: 20%">
                          Api End Url
                      </th>
                      <th style="width: 30%">
                          Api Key
                      </th>
                     
                      <th style="width: 20%">
                      </th>
                  </tr>
              </thead>
              <tbody>
                  @if(Session::has('message'))
    <div class="alert {{ Session::get('alert-class') }} fade-in" id="alert">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    {{ Session::get('message') }}
    </div>
    @endif
              @if(!empty($setting) && $setting->count())
              <?php $i=1;
              foreach ($setting as $key => $setting): ?>
                  <tr>
                      <td>
                  {{ $i }}
                      </td>
                      <td>
                          <a>
                              {{$setting->api_end_url}}
                          </a>
                          
                      </td>
                      <td>
                          <ul class="list-inline">
                              <li class="list-inline-item">
                                  {{$setting->api_key}}
                              </li>
                          </ul>
                      </td>
                    
                      <td class="project-actions text-right" style='white-space: nowrap'>
                          <a class="btn btn-info btn-sm" href="{{ route('setting.edit', [$setting->id]) }}">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" href="{{ route('setting.delete', [$setting->id]) }}">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a>
                      </td>
                  </tr>
              <?php $i++ ; endforeach ?>
                 @else
            <tr>
                <td colspan="10">There are no data.</td>
            </tr>
        @endif
              </tbody>
          </table>
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
<!-- ./wrapper -->
<script>
  window.onload = function() {
    if (window.jQuery) {  
        // jQuery is loaded  
        setTimeout(function(){ $('.alert').fadeOut() }, 5000);
    } else {
        // jQuery is not loaded
        alert("Doesn't Work");
    }
}
</script>

