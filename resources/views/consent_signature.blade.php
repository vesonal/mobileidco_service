@extends('layouts.app')
@section('content')

<div id="consentRoutePage"></div>
<p class="header-description">MobileID  <label style="color: red; display: inline;"></label></p>
<h1>Web to merchant app integration</h1>
<p> On this page you can perform a signature by means of a consent text string with evidence output. </p>
<h2>Consent signature</h2>
<h3><span>1 - Enter the <code>externalRef</code> of the user you want to authenticate</span></h3>
<p><label>External reference</label></p>
<input id="externalRef" type="medium-text-box" value="" aria-label="External reference"><br>
<h3><span>2 - Click the <strong>Get available devices</strong> button and select an authentication device</span></h3>
<div><button class="button" id="activate_device" onclick='GetAvailableDevice()'>Get available devices</button></div>
<p><label>Available devices</label></p>
<select text="Choose a device to Authenticate" id="authentication-select" aria-label="Available devices" class="authentication-select signicat-select"></select><br><br>
<h3><span>3 - Optionally enter additional information to be passed back to the app</span></h3>
<p>
   <input id="push_payload" type="checkbox" id="pushPayloadCheck"><label for="pushPayloadCheck"> Specify push payload</label>
   <input type="text" id="push_payment_msg" class="medium-text-box" placeholder="Push message payload" style="display:none;">
</p>
<br><br>
<h3><span>4 - Enter the consent text and optionally metadata</span></h3>
<p><label>Consent text</label>
  <input type="medium-text-box" id="consent_text" class="medium-text-box" placeholder="Enter consent sign text" aria-label="Consent text">
</p>
<p>
   <input id="metadata" type="checkbox" id="metaDataCheck"><label for="metaDataCheck"> Specify metadata (optional)</label><!---->
   <input type="text" id="metadata_information" class="medium-text-box" placeholder="Metadata" style="display:none;">
</p>
<br>
<h3><span>5 - Select the desired SDO format and click the <b>Sign</b> button</span></h3>
<p><label>SDO format</label></p>
<select text="Choose a sdo format" aria-label="Sdo format" class="signicat-select">
   <option value="Jwt"> Jwt </option></select>
</select>
<br><br>
<div><button class="button" id="activate_device" onclick='doSign()'>Sign</button></div>
<br><br>
<h3><span>6 - Push notification is displayed on the mobile device. Carry out authentication</span></h3>
<p><label>Signed response</label></p>
<textarea id="consent_response" aria-label="Signed response"></textarea>





@endsection
<!-- ./wrapper -->
@push('scripts')

<script>
var api_key = "{{ getenv('SECRET_API_KEY') }}";
function GetAvailableDevice(){
  var client_id = document.getElementById("externalRef").value;
  var base_url = '{{ getenv('APP_URL') }}';
  //alert(base_url);
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

function doSign(){
  var client_id = document.getElementById("externalRef").value;
  var device = document.getElementById("authentication-select").value;
  var sdo_format = 'Jwt';
  var base_url = '{{ getenv('APP_URL') }}';
  var push_payload = document.getElementById("push_payment_msg").value;
  var metadata_information = document.getElementById("metadata_information").value;
  var preContextTitle = btoa(document.getElementById("consent_text").value);
        $.ajax({
             type:'POST',
             url:base_url+'/consentSign',
             dataType:'json',
             data:{
                 "_token": "{{ csrf_token() }}",
                 "client_id":client_id,
                 "device_id":device,
                 "sdoFormat":sdo_format,
                 "pushPayload":push_payload,
                 "preContextTitle":preContextTitle,
                 "preContextContent":metadata_information,
             },
             success:function(data){
                 $("#authentication-select").attr('disabled', false);
              if (data.status=='success'){
                // $("#authentication-select").empty().append('<option value=' + data.data + ' selected>' + data.data + '</option>');
                // 2nd ajax call function
                checkStatus(client_id);
             // end 2nd ajax
              }
              else if (data.status=='error'){
                 document.getElementById("consent_response").innerHTML = JSON.stringify(data);
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


$("#metadata").click(function(){
    if($('#metadata').is(":checked"))   
      $("#metadata_information").show();
    else
     $("#metadata_information").hide();
});

// function checkStatus(client_id){
//     var base_url = '{{ getenv('APP_URL') }}';
//     $.ajax({
//           type: 'POST',
//           url: base_url + '/api/consentsign/checkStatus',
//           dataType: 'json',
//           headers: {
//                   "MobileIDAuthorization": api_key
//                  },
//           data: {
//             "_token": "{{ csrf_token() }}",
//             "client_id": client_id,
//           },
//           success: function (data) {
//             if(data.status==true) {
//               document.getElementById("consent_response").innerHTML = JSON.stringify(data);
//             }
//             else{
//                setIntervalX(function () {
//                   var base_url = '{{ getenv('APP_URL') }}';
//                   $.ajax({
//                         type: 'POST',
//                         url: base_url + '/api/consentsign/checkStatus',
//                         dataType: 'json',
//                         headers: {
//                             "MobileIDAuthorization": api_key
//                         },
//                         data: {
//                           "_token": "{{ csrf_token() }}",
//                           "client_id": client_id,
//                         },
//                         success: function (data) {
//                           if(data.status==true) {
//                             document.getElementById("consent_response").innerHTML = JSON.stringify(data);
//                             window.clearInterval(setIntervalX);

//                           }
//                           else{
//                             document.getElementById("consent_response").innerHTML = JSON.stringify(data);
//                           }
//                         }
//                       });
//                   }, 10000, 15); // end set interval function
//             }
//           }
//         });
// };

// function setIntervalX(callback, delay, repetitions) {
//     var x = 0;
//     var intervalID = window.setInterval(function () {
//     callback();
//      if (++x === repetitions) {
//          window.clearInterval(intervalID);
//      }
//     }, delay);
// }
function checkStatus(client_id){
  var base_url = '{{ getenv('APP_URL') }}';
  const id = setInterval(function(){
        $.ajax({
                type: 'POST',
                url: base_url + '/api/consentsign/checkStatus',
                dataType: 'json',
                headers: {
                "MobileIDAuthorization": api_key
                },
                data: {_token:"{{csrf_token()}}",client_id: client_id},
                success: function (data) {
                if(data.status==true) {
                document.getElementById("consent_response").innerHTML = JSON.stringify(data);
                clearInterval(id);
                }
                }
          });
    }, 3000);
    setTimeout(function(){
    clearInterval(id);
    }, 120000);
};
</script>
@endpush