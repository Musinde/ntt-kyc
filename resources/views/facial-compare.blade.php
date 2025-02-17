@extends('layouts.master')
@section('title')
    FACIAL RECOGNITION
@endsection

@section('meta')
    <meta name="title" content="KYC">
    <meta name="description" content="KYC">
    <meta name="keywords" content="KYC">
@endsection

@section('page-title')
    FACIAL RECOGNITION
@endsection

@section('content')
    @php
        $user = session('user');
        $new_user = \App\Models\User::findOrFail($user->id);
    @endphp
    @if (!empty($new_user->selfie))
        @if (empty($new_user->face_match))
            <!-- Face Match Status Messages -->
            <div class="col-md-12" id="face_failed">
                <div class="alert alert-danger">Face does not match!</div>
            </div>
        @else
            <div class="col-md-12 face_matched">
                <div class="alert alert-success">Face matched!</div>
            </div>
        @endif
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card p-1">
                    <div class="row mb-3">
                        <p><b>Name:</b> {{$new_user->name}}</p>
                    </div>

                    <div class="row mb-3">
                        <p><b>ID No:</b> {{$new_user->id_no}}</p>
                    </div>


                    <div class="row mb-3">
                        <p><b>Gender:</b> {{$new_user->gender}}</p>
                    </div>

                    <div class="row mb-3">
                        <p><b>DOB:</b> {{$new_user->dob}}</p>
                    </div>


                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-1">
                    <h6 class="text-center">Uploaded ID</h6>
                    <img class="rounded" height="200px" src="{{ $new_user->id_url }}" alt="Image" style="">
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-1">
                    <h6 class="text-center">Face Detected</h6>
                    <img class="rounded" height="200px" src="{{ $new_user->detected_face }}" alt="Image" style="">
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-1">
                    <h6 class="text-center">Uploaded Selfie</h6>
                    <img class="rounded" height="200px" src="{{ $new_user->selfie }}" alt="Image" style="">
                </div>
            </div>
        </div>
    @else
        <div class="row justify-content-between mb-3 flex-sm-column flex-md-row">

            <!-- Selfie Capture Section -->
            <div class="col-md-5">
                <div class="card p-4" id="upload_drop_zone">
                    <h6 class="mb-3 text-center">Capture Your Selfie</h6>

                    <div class="file-drop-area" id="fileDropArea" style="height: 400px !important;">
                        <span class="file-message">Take a selfie <a href="#" id="fileInputTrigger"></a></span><br>
                        <button class="btn btn-primary mt-3" id="take_selfie_btn">Take Selfie</button>
                        <video id="camera_feed" autoplay hidden style="width: 100%; margin-top: 15px;"></video>
                        <button id="capture_btn" class="btn btn-success mt-2" hidden>Capture</button>
                        <img src="{{ asset('loading.gif') }}" id="loading_spinner" hidden style="margin: auto;"
                            height="50px" width="50px">
                        <canvas id="selfie_canvas" hidden></canvas>
                        <div id="preview_upload_post_image"></div>
                    </div>
                </div>
            </div>

            <!-- Detected Face Display Section -->
            <div class="col-md-5">
                <div class="card p-4" style="height: 480px !important;">
                    <h6 class="mb-3 text-center">Detected Face</h6>
                    <img class="image-preview-img" src="{{ urldecode(request()->face) }}" alt="Image"
                        style="width: 100%">
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        const takeSelfieBtn = document.getElementById('take_selfie_btn');
        const cameraFeed = document.getElementById('camera_feed');
        const captureBtn = document.getElementById('capture_btn');
        const selfieCanvas = document.getElementById('selfie_canvas');
        const loadingSpinner = document.getElementById('loading_spinner');
        const fileDropArea = document.getElementById('fileDropArea');
        const passportUploadInput = document.getElementById('passport_upload_input');
        const faceFailedAlert = document.getElementById('face_failed');
        const faceMatchedAlert = document.querySelector('.face_matched');

        // Trigger file input when "select a file" link is clicked
        document.getElementById('fileInputTrigger').addEventListener('click', function(e) {
            e.preventDefault();
            passportUploadInput.click();
        });

        // Event listener to open the camera
        takeSelfieBtn.addEventListener('click', () => {
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    cameraFeed.srcObject = stream;
                    cameraFeed.hidden = false;
                    captureBtn.hidden = false;
                    takeSelfieBtn.hidden = true;
                })
                .catch(error => {
                    console.error("Error accessing camera:", error);
                    alert("Could not access the camera.");
                });
        });

        // Capture selfie and send to the server
        captureBtn.addEventListener('click', () => {
            selfieCanvas.width = cameraFeed.videoWidth;
            selfieCanvas.height = cameraFeed.videoHeight;
            const context = selfieCanvas.getContext('2d');
            context.drawImage(cameraFeed, 0, 0, cameraFeed.videoWidth, cameraFeed.videoHeight);

            cameraFeed.srcObject.getTracks().forEach(track => track.stop()); // Stop the camera
            cameraFeed.hidden = true;
            captureBtn.hidden = true;
            loadingSpinner.hidden = false;

            // Convert the captured image to Blob and send it to the server
            selfieCanvas.toBlob(blob => {
                var formData = new FormData();
                formData.append('file', blob, 'selfie.jpg');
                formData.append('detected_face', '{{ request()->path }}');
                formData.append('detected_face_path', '{{ request()->face }}');

                $.ajax({
                    url: "{{ url('upload-selfie') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#preview_upload_post_image').append(response.preview);
                        loadingSpinner.hidden = true;                        
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        loadingSpinner.hidden = true;
                        alert('Could not upload the selfie!');
                    }
                });
            }, 'image/jpeg');
        });
    </script>
@endsection
