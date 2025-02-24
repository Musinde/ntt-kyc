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

<style>
    .video-wrapper {
        position: relative;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        overflow: hidden;
        /* Ensures the video is cropped in a circle */
        display: flex;
        align-items: center;
        justify-content: center;
        background: black;
    }

    .video-wrapper video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.5) inset;
        pointer-events: none;
    }
</style>

@section('content')
    @php
        $user = session('user');
        $new_user = \App\Models\User::findOrFail($user->id);
    @endphp
    @if (!empty($new_user->selfie))
        @if (empty($new_user->liveness_passed))
            <div class="col-md-12 d-flex justify-content-center">
                <button class="btn btn-outline-primary btn-sm" id="initiate_liveness">Liveness Check</button>
            </div>

            <!-- Camera Container -->
            <div class="camera-container text-center mt-3"
                style="display: none; position: relative; width: 260px; height: 260px; margin: auto;">
                <!-- Instruction Text -->
                <p id="liveness-prompt" class="text-primary">Align your face within the circle</p>

                <!-- Circular Video Feed -->
                <div class="video-wrapper">
                    <video id="video" autoplay playsinline></video>
                    <div class="overlay"></div> <!-- Circular Frame Overlay -->
                </div>
            </div>

            <canvas id="canvas" style="display: none;"></canvas>
        @else
            @if ($new_user->face_match == 1)
                <!-- Face Match Status Messages -->
                <div class="col-md-12" id="face_failed">
                    <div class="alert alert-success">Face matched!</div>
                </div>
            @else
                <div class="col-md-12 face_matched">
                    <div class="alert alert-danger">Face does not match!</div>
                </div>
            @endif
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="card p-1">
                        <div class="row mb-3">
                            <p><b>Name:</b> {{ $new_user->name }}</p>
                        </div>

                        <div class="row mb-3">
                            <p><b>ID No:</b> {{ $new_user->id_no }}</p>
                        </div>


                        <div class="row mb-3">
                            <p><b>Gender:</b> {{ $new_user->gender }}</p>
                        </div>

                        <div class="row mb-3">
                            <p><b>DOB:</b> {{ $new_user->dob }}</p>
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
                        <img class="rounded" height="200px" src="{{ $new_user->detected_face }}" alt="Image"
                            style="">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-1">
                        <h6 class="text-center">Uploaded Selfie</h6>
                        <img class="rounded" height="200px" src="{{ $new_user->selfie }}" alt="Image" style="">
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="row justify-content-between mb-3 flex-sm-column flex-md-row">

            <!-- Selfie Capture Section -->
            <div class="col-md-5">
                <div class="card p-4 text-center" id="upload_drop_zone">
                    <h6 class="mb-3 text-center">Capture Your Selfie</h6>

                    <div class="file-drop-area" id="fileDropArea" style="height: 400px !important;">
                        <span class="file-message">Take a selfie <a href="#" id="fileInputTrigger"></a></span><br>

                        <div class="video-wrapper">
                            <video id="camera_feed" autoplay></video>
                            <div class="overlay"></div>
                        </div>

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
    @if (empty($new_user->selfie))
        <script>
            const takeSelfieBtn = document.getElementById('take_selfie_btn');
            const cameraFeed = document.getElementById('camera_feed');
            const captureBtn = document.getElementById('capture_btn');
            const selfieCanvas = document.getElementById('selfie_canvas');
            const loadingSpinner = document.getElementById('loading_spinner');
            const fileDropArea = document.getElementById('fileDropArea');
            const faceFailedAlert = document.getElementById('face_failed');
            const faceMatchedAlert = document.querySelector('.face_matched');

            // Trigger file input when "select a file" link is clicked
            document.getElementById('fileInputTrigger').addEventListener('click', function(e) {
                e.preventDefault();
                passportUploadInput.click();
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
                $(".video-wrapper").addClass('d-none');

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
                            toastr.error('Could not upload the selfie!', 'Error');
                        }
                    });
                }, 'image/jpeg');
            });
            $(document).ready(function() {
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(stream => {
                        cameraFeed.srcObject = stream;
                        cameraFeed.hidden = false;
                        captureBtn.hidden = false;
                    })
                    .catch(error => {
                        console.error("Error accessing camera:", error);
                        toastr.error("Could not access the camera.", 'Error');
                    });
            });
        </script>
    @else
        <script>
            $(document).ready(function() {
                let mediaRecorder;
                let recordedChunks = [];
                let videoStream;
                let promptInterval;
                const prompts = ["Move Closer", "Tilt Your Head", "Blink", "Smile"];

                $("#initiate_liveness").click(async function() {
                    $(".camera-container").show();
                    $("#liveness-prompt").text("Initializing camera...");

                    try {
                        videoStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: "user"
                            }
                        });
                        let video = document.getElementById("video");
                        video.srcObject = videoStream;

                        mediaRecorder = new MediaRecorder(videoStream, {
                            mimeType: "video/webm"
                        });

                        mediaRecorder.ondataavailable = (event) => recordedChunks.push(event.data);
                        mediaRecorder.start();

                        // Show random prompts every 1.5 seconds
                        promptInterval = setInterval(() => {
                            let randomPrompt = prompts[Math.floor(Math.random() * prompts.length)];
                            $("#liveness-prompt").text(randomPrompt);
                        }, 1500);

                        // Automatically stop recording and submit after 8 seconds
                        setTimeout(() => {
                            submitLiveness();
                        }, 5000);

                    } catch (error) {
                        toastr.error("Camera access denied!", 'Error');
                        console.error(error);
                    }
                });

                function submitLiveness() {
                    clearInterval(promptInterval); // Stop prompts
                    $("#liveness-prompt").text("Processing...");

                    mediaRecorder.stop();

                    mediaRecorder.onstop = async () => {

                        $.ajax({
                            url: "{{ url('initiate-liveness') }}",
                            type: "POST",
                            data: {},
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                toastr.success("Liveness confirmed", 'Success');
                                window.location.reload();
                            },
                            error: function(xhr, status, error) {
                                toastr.error("Error verifying liveness!", 'Error');
                            }
                        });
                    };
                }
            });
        </script>
    @endif
@endsection
