@extends('layouts.app')

@section('content')
<div class="wrapper">
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Configuration</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
       <div class="card">
        <div class="card-header">
          <h3 class="card-title">Configuration</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button> -->
          </div>
        </div>
        <div class="card-body">
           <p><strong>Configuration set to: </strong>
          <br>
           <strong> Environment:</strong>  preprod<br>
           <strong> Service: </strong> mobileid-sample<br>
           <strong> OIDC Client:</strong>  preprod.mobileid-sample.sampleapp<br>
           <strong> Application ID:</strong>  demo_default<br>
          </p>
        </div>
      </div>
    </section>
  </div>
  
</div>
@endsection
<!-- ./wrapper -->


