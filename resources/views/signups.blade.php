@extends('layouts.master')
@section('title')
    KYC
@endsection

@section('meta')
    <meta name="title" content="KYC">
    <meta name="description" content="KYC">
    <meta name="keywords" content="KYC">
@endsection


@section('page-title')
    KYC
@endsection
@section('body')

    <body>
    @endsection
    @section('content')
        <br>
        <div class="row ">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center mb-3">
                            <h5 class="card-title me-2">Signups List</h5>

                        </div>

                        <div class="mx-n4 simplebar-scrollable-y" data-simplebar="init" style="max-height: 332px;">
                            <div class="simplebar-wrapper" style="margin: 0px;">
                                <div class="simplebar-height-auto-observer-wrapper">
                                    <div class="simplebar-height-auto-observer"></div>
                                </div>
                                <div class="simplebar-mask">
                                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                        <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                            aria-label="scrollable content" style="height: auto; overflow: hidden scroll;">
                                            <div class="simplebar-content" style="padding: 0px;">
                                                <div class="table-responsive">
                                                    <table
                                                        class="table table-striped table-centered align-middle table-nowrap mb-0 table-check">
                                                        <thead>
                                                            <tr>
                                                                <th>ID No</th>
                                                                <th>Name</th>
                                                                <th>DOB</th>
                                                                <th>Gender</th>
                                                                <th>ID</th>
                                                                <th>Detected Face</th>
                                                                <th>Selfie</th>
                                                                <th>Face Matches</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($users as $user)
                                                                <tr>

                                                                    <td class="fw-semibold">{{ $user->id_no }}</td>
                                                                    <td>{{ $user->name }}</td>
                                                                    <td>{{ $user->dob }}</td>
                                                                    <td>{{ $user->gender }}</td>
                                                                    <td style="width: 190px;">
                                                                        @if (!empty($user->id_url))
                                                                            <img class="rounded avatar-lg"
                                                                                src="{{ $user->id_url }}" alt="">
                                                                        @endif

                                                                    </td>
                                                                    <td style="width: 190px;">
                                                                        @if (!empty($user->detected_face))
                                                                            <img class="rounded avatar-lg"
                                                                                src="{{ $user->detected_face }}"
                                                                                alt="">
                                                                        @endif

                                                                    </td>
                                                                    <td style="width: 190px;">
                                                                        @if (!empty($user->selfie))
                                                                            <img class="rounded avatar-lg"
                                                                                src="{{ $user->selfie }}" alt="">
                                                                        @endif

                                                                    </td>

                                                                    <td>
                                                                        @if ($user->face_match == 1)
                                                                            <div
                                                                                class="badge bg-success-subtle text-success font-size-12">
                                                                                Matched</div>
                                                                        @elseif($user->face_match == 2)
                                                                            <div
                                                                                class="badge bg-danger-subtle text-danger font-size-12">
                                                                                Failed</div>
                                                                        @else
                                                                            <div
                                                                                class="badge bg-warning-subtle text-warning font-size-12">
                                                                                Pending</div>
                                                                        @endif

                                                                    </td>
                                                                </tr>
                                                            @endforeach


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="simplebar-placeholder" style="width: 1158px; height: 410px;"></div>
                            </div>
                            <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                            </div>
                            <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                <div class="simplebar-scrollbar"
                                    style="height: 268px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
