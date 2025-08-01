@extends('layouts/layoutMaster')

@section('title', 'Blank layout - Layouts')
@section('page-script')
<script src="{{asset('assets/js/pages-auth.js')}}"></script>
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>


@endsection


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
    </div>
    <!--/ Notification -->
    @endcan


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
                <div
                  style="font-family: 'Arial', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 300px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);">
                  <!-- Sparkles -->
                  <div
                    style="position: absolute; color: #ffd700; font-size: 20px; animation: sparkle 2s infinite; top: 10%; left: 10%;">
                    ✨</div>
                  <div
                    style="position: absolute; color: #ffd700; font-size: 20px; animation: sparkle 2s infinite 0.5s; top: 15%; right: 15%;">
                    ⭐</div>
                  <div
                    style="position: absolute; color: #ffd700; font-size: 20px; animation: sparkle 2s infinite 1s; bottom: 20%; left: 20%;">
                    ✨</div>
                  <div
                    style="position: absolute; color: #ffd700; font-size: 20px; animation: sparkle 2s infinite 1.5s; bottom: 15%; right: 10%;">
                    ⭐</div>

                  <!-- Main Content -->
                  <div
                    style="text-align: center; padding: 30px; background: rgba(255, 255, 255, 0.95); border-radius: 15px; backdrop-filter: blur(10px); position: relative; max-width: 600px; animation: slideUp 1s ease-out;">
                    <div
                      style="background: linear-gradient(45deg, #ff6b6b, #feca57); color: white; padding: 12px 24px; border-radius: 50px; font-size: 18px; font-weight: bold; margin-bottom: 30px; display: inline-block; animation: pulse 2s infinite; box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);">
                      August 1, 2025
                    </div>

                    <h1
                      style="font-size: 3.5rem; font-weight: bold; background: linear-gradient(45deg, #667eea, #764ba2, #ff6b6b); background-size: 300% 300%; -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; animation: gradientShift 3s ease-in-out infinite; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);">
                      CMD 47th Foundation Day
                    </h1>

                    <p style="font-size: 1.3rem; color: #666; margin-bottom: 30px; animation: fadeInUp 2s ease-out;">
                      Celebrating <strong>47 Years</strong> of Excellence
                    </p>

                    {{-- <div
                      style="background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 15px; margin-top: 30px; font-size: 1.2rem; font-weight: 500; animation: glow 2s ease-in-out infinite alternate; box-shadow: 0 10px 20px rgba(240, 147, 251, 0.3);">
                      Join us in commemorating four decades of innovation, growth, and success!
                    </div> --}}
                  </div>

                  <!-- Confetti -->
                  <div
                    style="position: absolute; width: 10px; height: 10px; background: #ff6b6b; animation: confettiFall 3s infinite linear; top: -10px; left: 10%;">
                  </div>
                  <div
                    style="position: absolute; width: 10px; height: 10px; background: #feca57; animation: confettiFall 3s infinite linear 0.5s; top: -10px; left: 30%;">
                  </div>
                  <div
                    style="position: absolute; width: 10px; height: 10px; background: #48dbfb; animation: confettiFall 3s infinite linear 1s; top: -10px; left: 50%;">
                  </div>
                  <div
                    style="position: absolute; width: 10px; height: 10px; background: #ff9ff3; animation: confettiFall 3s infinite linear 1.5s; top: -10px; left: 70%;">
                  </div>
                  <div
                    style="position: absolute; width: 10px; height: 10px; background: #54a0ff; animation: confettiFall 3s infinite linear 2s; top: -10px; left: 90%;">
                  </div>

                  <style>
                    @keyframes slideUp {
                      from {
                        transform: translateY(50px);
                        opacity: 0;
                      }

                      to {
                        transform: translateY(0);
                        opacity: 1;
                      }
                    }

                    @keyframes pulse {

                      0%,
                      100% {
                        transform: scale(1);
                      }

                      50% {
                        transform: scale(1.05);
                      }
                    }

                    @keyframes gradientShift {

                      0%,
                      100% {
                        background-position: 0% 50%;
                      }

                      50% {
                        background-position: 100% 50%;
                      }
                    }

                    @keyframes fadeInUp {
                      from {
                        transform: translateY(20px);
                        opacity: 0;
                      }

                      to {
                        transform: translateY(0);
                        opacity: 1;
                      }
                    }

                    @keyframes sparkle {

                      0%,
                      100% {
                        transform: scale(1) rotate(0deg);
                        opacity: 1;
                      }

                      50% {
                        transform: scale(1.3) rotate(180deg);
                        opacity: 0.7;
                      }
                    }

                    @keyframes confettiFall {
                      0% {
                        transform: translateY(-100px) rotate(0deg);
                        opacity: 1;
                      }

                      100% {
                        transform: translateY(400px) rotate(360deg);
                        opacity: 0;
                      }
                    }

                    @keyframes glow {
                      from {
                        box-shadow: 0 10px 20px rgba(240, 147, 251, 0.3);
                      }

                      to {
                        box-shadow: 0 15px 30px rgba(240, 147, 251, 0.6);
                      }
                    }
                  </style>
                </div>
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