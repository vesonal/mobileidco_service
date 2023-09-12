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
                <h3 class="card-title">Update App CMS</h3>
              </div>
              <form action="{{route('cms.update',$pages->id)}}" method="POST">
                @csrf
                  <div class="card-body">

                    <div class="mb-3 expiry">
                      <label for="exampleFormControlInput1" class="form-label">Pagename</label>
                      <input type="text" class="form-control" name="pagename" value="{{$pages->pagename}}" readonly
                        placeholder="pagename">
                      @if($errors->has('name'))
                      <div class="error text-danger">{{ $errors->first('pagename') }}</div>
                      @endif
                    </div>

                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Title</label>
                      <input type="text" class="form-control" name="title" value="{{$pages->title}}"
                        placeholder="Enter Page Title"></textarea>
                      @if($errors->has('email'))
                      <div class="error text-danger">{{ $errors->first('usage') }}</div>
                      @endif
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Keywords</label>
                      <input type="text" class="form-control" name="keyword" value="{{$pages->keyword}}"
                        placeholder="keyword"></textarea>
                      @if($errors->has('country'))
                      <div class="error text-danger">{{ $errors->first('authorization') }}</div>
                      @endif
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Short description</label>
                      <input type="text" class="form-control" name="short_description"
                        value="{{$pages->short_description}}" placeholder="short description"></textarea>
                      @if($errors->has('country'))
                      <div class="error text-danger">{{ $errors->first('authorization') }}</div>
                      @endif
                    </div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                      <textarea class=" form-control summernote" name="description" values=""
                        placeholder="Description">{{$pages->description}}</textarea>
                      @if($errors->has('contact_no'))
                      <div class="error text-danger">{{ $errors->first('payment') }}</div>
                      @endif
                    </div>
                  </div>
                  <div class="card-footer">
                    <a href="{{route('cms.list')}}" class="btn btn-warning btn-sm">Cancel</a>
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
