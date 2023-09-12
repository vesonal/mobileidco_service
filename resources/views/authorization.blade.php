@extends('layouts.app')
@section('content')



<div id="paymentRoutePage"></div>
<p class="header-description">MobileID <label style="color: red; display: inline;"></label></p>
<h1>Web to MobileID App</h1>
<p> On this page you can perform the authorization of a payment transaction by means of a consent text string without evidence output. </p>
<h2>Payment authorization</h2>
<h3><span>1 - Enter the <code>externalRef</code> of the user you want to authenticate</span></h3>
<p><label>External reference</label></p>
<input id="externalRef" type="medium-text-box" value="" aria-label="External reference"><br>
<h3><span>2 - Click the <b>Get available devices</b> button and select an authentication device</span></h3>
<div><button class="button" id="activate_device" onclick='GetAvailableDevice()'>Get available devices</button></div>
<p><label>Available devices</label></p>
<select text="Choose a device to Authenticate" aria-label="Available devices" class="signicat-select authentication-select"  id="authentication-select"></select><br><br>
<h3><span>3 - Optionally enter additional information to be passed back to the app</span></h3>
<p>
   <input type="checkbox" id="push_payload"><label for="push_payload"> Specify push payload</label><!---->
</p>
<input type="text" id="push_payment_msg" class="medium-text-box" placeholder="Push message payload" style="display:none;">
<br><br>
<h3><span>4 - Enter payment information and click the <b>Authorize</b> button</span></h3>
<p>
  <label>
   <input id="authorize_payload" value="" aria-label="Activation code" type="checkbox" style="padding-right:3px;margin-right:7px;">Payment information</label>
  <input type="medium-text-box" id="payment_information" class="medium-text-box" placeholder="Payment Information" style="display:none;">
</p>

<div><button class="button" id="activate_device" onclick='doAuthenticate()'>Authorize</button></div>
<br><br>
<h3><span>5 - Push notification is displayed on the mobile device. Carry out authorization</span></h3>
<p><label>Payment authorization response</label></p>
<textarea id="payment_response" disabled="disabled" aria-label="Payment authorization response"></textarea>

@endsection
<!-- ./wrapper -->
@push('scripts')

<script>
var api_key = "{{ getenv('SECRET_API_KEY') }}";
function GetAvailableDevice(){
  var client_id = document.getElementById("externalRef").value;
  var base_url = '{{ getenv('APP_URL') }}';
        $.ajax({
             type:'GET',
             url:base_url+'/client/'+client_id+'',
             dataType:'json',
             success:function(data){
                  $("#authentication-select").attr('disabled', false);
              if (data.status=='success') {
                $("#authentication-select").empty().append('<option value=' + data.data + ' selected>' + data.data + '</option>');
              }
              else{
                 $("#authentication-select").empty();
              }
             }
        });
  }

function doAuthenticate(){
  var client_id = document.getElementById("externalRef").value;
  var device = document.getElementById("authentication-select").value;
  var push_payload = document.getElementById("push_payment_msg").value;
  var payment_information = btoa(document.getElementById("payment_information").value);

  var base_url = '{{ getenv('APP_URL') }}';
        $.ajax({
             type:'POST',
            // url:base_url+'/api/oauth2/auth',
             url:base_url+'/authorizepayment',
             dataType:'json',
             data:{
                 "_token": "{{ csrf_token() }}",
                 "client_id":client_id,
                 "device_id":device,
                 "pushPayload":push_payload,
                 "preContextTitle":payment_information
             },
             success:function(data){
                  $("#authentication-select").attr('disabled', false);
              if (data.status=='success') {
                $("#authentication-select").empty().append('<option value=' + data.data + ' selected>' + data.data + '</option>');
                // 2nd ajax call function
                  var setIntervalX = setInterval(checkStatus(client_id), 10000);
                  window.clearInterval(setIntervalX);
             // end 2nd ajax
              }
              else if (data.status=='error'){
                 document.getElementById("payment_response").innerHTML = JSON.stringify(data);
              }
              else{
                 $("#authentication-select").empty();
              }

             }
        });
  }

$("#push_payload").click(function(){
    if($('#push_payload').is(":checked"))   
      $("#push_payment_msg").show();
    else
     $("#push_payment_msg").hide();
});

$("#authorize_payload").click(function(){
    if($('#authorize_payload').is(":checked"))   
      $("#payment_information").show();
    else
     $("#payment_information").hide();
});
function checkStatus(client_id){
    var base_url = '{{ getenv('APP_URL') }}';
    $.ajax({
          type: 'POST',
          url: base_url + '/api/authorizepayment/checkStatus',
          dataType: 'json',
          headers: {
                  "MobileIDAuthorization": api_key
                 },
          data: {
            "_token": "{{ csrf_token() }}",
            "client_id": client_id,
          },
          success: function (data) {
            if(data.status==true) {
              document.getElementById("payment_response").innerHTML = JSON.stringify(data);
            }
            else{
               setIntervalX(function () {
                  var base_url = '{{ getenv('APP_URL') }}';
                  $.ajax({
                        type: 'POST',
                        url: base_url + '/api/authorizepayment/checkStatus',
                        dataType: 'json',
                        headers: {
                            "MobileIDAuthorization": api_key
                        },
                        data: {
                          "_token": "{{ csrf_token() }}",
                          "client_id": client_id,
                        },
                        success: function (data) {
                          if(data.status==true) {
                            document.getElementById("payment_response").innerHTML = JSON.stringify(data);
                            window.clearInterval(setIntervalX);
                          }
                          else{
                            document.getElementById("payment_response").innerHTML = JSON.stringify(data);
                            // var setIntervalX = setInterval(checkStatus(client_id), 10000);
                            // window.clearInterval(setIntervalX);
                          }
                        }
                      });
                  }, 10000, 25); // end set interval function
            }
              // clearInterval(timer(),1000);

          }
        });
  };

function setIntervalX(callback, delay, repetitions) {
    var x = 0;
    var intervalID = window.setInterval(function () {
       callback();
console.log(x);
       if (++x === repetitions) {
           window.clearInterval(intervalID);
       }
    }, delay);
}
</script>
@endpush