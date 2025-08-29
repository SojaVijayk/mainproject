@extends('layouts/layoutMaster')

@section('title', 'Blank layout - Layouts')
@section('page-script')
<script src="{{asset('assets/js/pages-auth.js')}}"></script>
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>


@endsection

@php
$user = Auth::user();
@endphp
@section('content')

{{-- <lottie-player style="align-items: center; width: 800px; height: 400px;" src='{{config(' variables.login3')}}'
  background="transparent" speed="1" style="width: 800px; height: 400px;" loop autoplay></lottie-player> --}}
<div class="row">


  <div class="col-lg-12 mb-4">

    <!-- NOIFICATN -->
    @can('leave-request-approve')
    <h5 class="pb-1 mb-4">Notification</h5>
    <div class="row mb-5">
      <div class="col-md">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Leave Request</span>
                <div class="card-header-elements">
                  <span
                    class="badge rounded-pill {{$pending_leave > 0 ? 'bg-danger' : 'bg-secondary'}}">{{$pending_leave}}</span>
                </div>

              </div>
              <div class="card-body">
                <a href="/leave/approve-list" class="btn btn-primary">Take Action</a>
                <p class="card-text pt-4"><small class="text-muted">Last updated 1 mins ago</small></p>
              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" " src="https://lottie.host/ba97c031-5f47-4ac8-a78a-30e38da11a45/Fg7GMZaUw0.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>

          </div>
        </div>
        <div class="col-md">
          <div class="card mb-3">
            <div class="row g-0">
              <div class="col-md-8">
                <div class="card-header header-elements">
                  <span class=" me-2">Leave Cencel Request</span>
                  <div class="card-header-elements">
                    <span
                      class="badge  rounded-pill {{$pending_leave_cancel_ro > 0 ? 'bg-danger' : 'bg-secondary'}}">{{$pending_leave_cancel_ro}}</span>

                  </div>

                </div>
                <div class="card-body">
                  <a href="/leave/ro/cancel-requests" class="btn btn-primary">Take Action</a>
                  <p class="card-text pt-4"><small class="text-muted">Last updated 1 mins ago</small></p>
                </div>
              </div>
              <div class="col-md-4">
                <dotlottie-player style=" "
                  src="https://lottie.host/ba97c031-5f47-4ac8-a78a-30e38da11a45/Fg7GMZaUw0.json"
                  background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
                </dotlottie-player>
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="col-md">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Movement Request</span>
                <div class="card-header-elements">
                  <span
                    class="badge  rounded-pill {{$pending_movement > 0 ? 'bg-danger' : 'bg-secondary'}}">{{$pending_movement}}</span>
                </div>
              </div>
              <div class="card-body">
                <a href="/movement/approve-list" class="btn btn-primary">Take Action</a>
                <p class="card-text pt-4"><small class="text-muted">Last updated 1 mins ago</small></p>
              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" " src="https://lottie.host/573302e1-7a3c-4844-b6e8-00e9c20c9367/O43rezaDwA.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Miss Punch Request</span>
                <div class="card-header-elements">
                  <span
                    class="badge  rounded-pill {{$pending_misspunch > 0 ? 'bg-danger' : 'bg-secondary'}}">{{$pending_misspunch}}</span>
                </div>
              </div>
              <div class="card-body">
                <a href="/misspunch/approve-list" class="btn btn-primary">Take Action</a>
                <p class="card-text pt-4"><small class="text-muted">Last updated 1 mins ago</small></p>
              </div>
            </div>
            <div class="col-md-4 p-2">
              <dotlottie-player style=" " src="https://lottie.host/fc6e3aa0-e598-47cb-8686-67d91384f81b/PwYH2kXBIh.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
          </div>
        </div>
      </div>


    </div>
    <div class="row mb-5">
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Movement Status Repot</span>
                <div class="card-header-elements">
                  <span class="badge  rounded-pill {{$report > 0 ? 'bg-danger' : 'bg-secondary'}}">{{$report}}</span>
                </div>
              </div>
              <div class="card-body">
                <a href="/movement/approve-list?report=1" class="btn btn-primary">View</a>
                <p class="card-text pt-4"><small class="text-muted">Last updated 1 mins ago</small></p>
              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" " src="https://lottie.host/573302e1-7a3c-4844-b6e8-00e9c20c9367/O43rezaDwA.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
          </div>
        </div>
      </div>


      <div class="col-md-4">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Project Management BETA</span>
                <div class="card-header-elements">
                  {{-- <span class="badge "></span> --}}
                </div>
              </div>
              <div class="card-body">
                <a href="/pms/dashboard" class="btn btn-primary">Start</a>

              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" "
                src="https://lottie.host/0ff80b37-dd0e-4304-8fb5-6ce520f1547c/VC0cHUYVry.lottie"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
          </div>
        </div>
      </div>
      @if($user->hasRole('director'))
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Project Dashboard BETA</span>
                <div class="card-header-elements">
                  {{-- <span class="badge "></span> --}}
                </div>
              </div>
              <div class="card-body">
                <a href="/pms/reports/project-status-detailed" class="btn btn-primary">View</a>

              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" "
                src="https://lottie.host/0ff80b37-dd0e-4304-8fb5-6ce520f1547c/VC0cHUYVry.lottie"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
          </div>
        </div>
      </div>
      @endif
      @if($user->hasRole('finance'))
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Invoice Management </span>
                <div class="card-header-elements">
                  {{-- <span class="badge "></span> --}}
                </div>
              </div>
              <div class="card-body">
                <a href="/pms/finance/dashboard" class="btn btn-primary">View</a>

              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" "
                src="https://lottie.host/0ff80b37-dd0e-4304-8fb5-6ce520f1547c/VC0cHUYVry.lottie"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
          </div>
        </div>
      </div>
      @endif


    </div>
    <!--/ Notification -->
    @endcan
    <div class="row">
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Hall Availability</span>
                <div class="card-header-elements">
                  {{-- <span class="badge "></span> --}}
                </div>
              </div>
              <div class="card-body">
                <a href="/availability/calendar" class="btn btn-primary">View</a>

              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" "
                src="https://lottie.host/1f4b12ab-1df8-46bb-a19c-b3c705cde0bc/o1fHfqtk7r.lottie"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
          </div>
        </div>
      </div>
      @cannot('leave-request-approve')
      @if($user->hasRole('HR'))
      <div class="col-md">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Leave Cencel Request</span>
                <div class="card-header-elements">
                  <span
                    class="badge  rounded-pill {{$pending_leave_cancel_hr > 0 ? 'bg-danger' : 'bg-secondary'}}">{{$pending_leave_cancel_hr}}</span>
                </div>

              </div>
              <div class="card-body">
                <a href="/leave/hr/cancel-requests" class="btn btn-primary">Take Action</a>
                <p class="card-text pt-4"><small class="text-muted">Last updated 1 mins ago</small></p>
              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" " src="https://lottie.host/ba97c031-5f47-4ac8-a78a-30e38da11a45/Fg7GMZaUw0.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>

          </div>
        </div>
      </div>
      @endif
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-8">
              <div class="card-header header-elements">
                <span class=" me-2">Project Management BETA</span>
                <div class="card-header-elements">
                  {{-- <span class="badge "></span> --}}
                </div>
              </div>
              <div class="card-body">
                <a href="/pms/dashboard" class="btn btn-primary">Start</a>

              </div>
            </div>
            <div class="col-md-4">
              <dotlottie-player style=" "
                src="https://lottie.host/0ff80b37-dd0e-4304-8fb5-6ce520f1547c/VC0cHUYVry.lottie"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
          </div>
        </div>
      </div>

      @endcannot
    </div>


    <!-- NEWS -->
    <h5 class="pb-1 mb-3">News & Updates</h5>
    <div class="row mb-2">
      <div class="col-md">
        <div class="card mb-1">
          <div class="row g-0">
            <div class="col-md-1">
              <dotlottie-player style=" " src="https://lottie.host/7858dc1a-462f-4889-9b54-17cb488ca895/ywSVghJfw8.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
              </dotlottie-player>
            </div>
            <div class="col-md-8">
              <div class="card-body">
                {{-- <h5 class="card-title">Card title</h5> --}}
                <p class="card-text">
                  {{-- No Data Available --}}

                </p>
                <p class="card-text"><small class="text-muted time">Last updated 1 mins ago</small></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--/ NEWS -->

    <dotlottie-player style=" " src="https://lottie.host/560f4f0c-e80e-4e77-8793-40f896753469/Y14w0uZqv9.json"
      background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>

  </div>
</div>


@endsection