@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'CMD Research - Register')

@section('vendor-style')
<!-- Vendor -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-on-scroll/animate-on-scroll.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/animate-on-scroll/animate-on-scroll.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-auth.js')}}"></script>
<script src="{{asset('assets/js/extended-ui-timeline.js')}}"></script>
<script>
$(document).ready(function() {
    $('#discipline, #institution').change(function() {

        var discipline = $('#discipline').val();
        var institution = $('#institution').val();
        console.log('#programme option.' + institution + '-' + discipline);

        // Show only the options in #select3 that match the selected values of #select1 and #select2
        $('#programme option').hide();
        $('#type option').hide(); // Hide all options initially
        $('#programme option.' + institution + '-' + discipline).show(); // Show only matching options
        $('#type option.' + institution).show();
    });

    // Trigger change to set the initial state
    {
        {
            --$('#discipline, #institution').trigger('change');
            --
        }
    }


    var counter = 0; // Initialize a counter to give unique ids and names

    // Function to add a new input field
    $('#add-input').click(function(e) {
        e.preventDefault();
        counter++; // Increment counter to ensure unique id and name

        // Create a new input field with a unique id and name
        {
            {
                --
                var inputField = `
            <div class="input-group" id="input-${counter}">
                <input type="text" id="field-${counter}" name="field-${counter}" placeholder="Input ${counter}" />
                <button class="remove-input" data-id="${counter}">Remove</button>
            </div>
        `;
                --
            }
        }
        var inputField = `
        <div class="mb-3">
              <label for="name" class="form-label">Educational Qualification</label>
              <input type="text" class="form-control" id="field-${counter}" name="field-${counter}" placeholder="Input ${counter}" name="education"  autofocus>
               <button class="remove-input btn mt-2 btn-sm btn-danger" data-id="${counter}">Remove</button>
            </div>
        `;

        // Append the new input field to the container
        $('#input-container').append(inputField);
    });

    // Function to remove a specific input field
    $(document).on('click', '.remove-input', function() {
        var id = $(this).data('id'); // Get the id of the input field to remove
        $('#input-' + id).remove(); // Remove the input field
    });


});
(function() {


})
</script>

@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">

        <!-- /Left Text -->
        <div class="d-flex d-lg-flex col-lg-6 p-0">
            <div class=" auth-cover-bg-color d-flex justify-content-center align-items-center">

                <div class="col-xl-6">
                    <div class="card">
                        <h5 class="card-header">Instructions</h5>
                        <div class="card-body pb-0">
                            <ul class="timeline mt-3 mb-0">
                                <li class="timeline-item timeline-item-primary pb-4 border-left-dashed">
                                    <span class="timeline-indicator timeline-indicator-primary">
                                        <i class="ti ti-send"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header border-bottom mb-3">
                                            <h6 class="mb-0">Step 1</h6>
                                            <span class="text-muted">01</span>
                                        </div>
                                        <div class="d-flex justify-content-between flex-wrap mb-2">
                                            <div>
                                                <span>Applicant has to submit their application form through CMD’s
                                                    official Portal.</span>

                                            </div>
                                            <div>
                                                <span></span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0)">
                                            <i class="ti ti-link"></i>
                                            hrms.cmd.kerala.gov.in/research
                                        </a>
                                    </div>
                                </li>
                                <li class="timeline-item timeline-item-success pb-4 border-left-dashed">
                                    <span class="timeline-indicator timeline-indicator-success">
                                        <i class="ti ti-brush"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header border-bottom mb-3">
                                            <h6 class="mb-0">Step 02</h6>
                                            <span class="text-muted">02</span>
                                        </div>
                                        <div class="d-flex justify-content-between flex-wrap mb-2">
                                            <div>
                                                <span>Complete “CMD Candidate Registration” on the CMD website.</span>

                                            </div>
                                            <div>
                                                <span></span>
                                            </div>
                                        </div>

                                    </div>
                                </li>
                                <li class="timeline-item timeline-item-danger pb-4 border-left-dashed">
                                    <span class="timeline-indicator timeline-indicator-danger">
                                        <i class="ti ti-basket"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header border-bottom mb-3">
                                            <h6 class="mb-0">Step 03</h6>
                                            <span class="text-muted">03</span>
                                        </div>
                                        <div class="d-flex justify-content-between flex-wrap mb-2">
                                            <div>
                                                <span>Applicant has to submit their application on Centre Portal (CHRIST
                                                    (Deemed to be University), Bangalore/Amrita Vishwa Vidyapeetham,
                                                    Amritapuri) -</span>

                                            </div>
                                            <div>
                                                <span></span>
                                            </div>
                                        </div>

                                    </div>
                                </li>
                                <li class="timeline-item timeline-item-info pb-4 border-left-dashed">
                                    <span class="timeline-indicator timeline-indicator-info">
                                        <i class="ti ti-user-circle"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header border-bottom mb-3">
                                            <h6 class="mb-0">Step 04</h6>
                                            <span class="text-muted">04</span>
                                        </div>
                                        <div class="d-flex justify-content-between flex-wrap mb-2">
                                            <div>
                                                <span>Complete Registration and apply for the programme</span>

                                            </div>
                                            <div>
                                                <span></span>
                                            </div>
                                        </div>

                                    </div>
                                </li>
                                <li class="timeline-item timeline-item-secondary pb-3 border-0">
                                    <span class="timeline-indicator timeline-indicator-secondary">
                                        <i class="ti ti-bell"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header border-bottom mb-3">
                                            <h6 class="mb-0">Step 05</h6>
                                            <span class="text-muted">05</span>
                                        </div>
                                        <div class="d-flex justify-content-between flex-wrap mb-2">
                                            <div>
                                                <span>Send the soft copy of the application to the email id:
                                                    research@cmd.kerala.gov.in</span>

                                            </div>
                                            <div>
                                                <span></span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0)">
                                            <i class="ti ti-mail"></i>
                                            research@cmd.kerala.gov.in
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{--  <img src="{{ asset('assets/img/illustrations/auth-register-illustration-'.$configData['style'].'.png') }}"
                alt="auth-register-cover" class="img-fluid my-5 auth-illustration"
                data-app-light-img="illustrations/auth-register-illustration-light.png"
                data-app-dark-img="illustrations/auth-register-illustration-dark.png">

                <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}"
                    alt="auth-register-cover" class="platform-bg"
                    data-app-light-img="illustrations/bg-shape-image-light.png"
                    data-app-dark-img="illustrations/bg-shape-image-dark.png"> --}}
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Register -->
        <div class="d-flex col-12 col-lg-6 align-items-center p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
                <!-- Logo -->
                <div class="app-brand mb-4">
                    <a href="{{url('/')}}" class="app-brand-link gap-2">
                        <img height="100" width="100" src="{{ asset('assets/img/branding/cmdlogo.png') }}"></img>
                    </a>
                    <h3 class=" mb-1 fw-bold">Welcome to CMD Research Centre


                </div>
                <!-- /Logo -->
                {{--  <h3 class="mb-1 fw-bold">Empowering Scholars. Advancing Knowledge.🚀</h3>  --}}
                <p class="mb-4">Recognized Research Centre in Management and Social Sciences</p>

                <form class="mb-3">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name of Scholar</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name"
                            autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email"
                            autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="mobile" name="mobile"
                            placeholder="Enter your Mobile Number" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="centre">Institution</label>
                        <select class="select2 select-event-label form-select" id="institution" name="institution">
                            <option disabled selected>Select</option>

                            <option data-label="danger" value="Amrita">Amrita Vishwa Vidyapeetham, Amritapuri</option>
                            <option data-label="info" value="CHRIST">CHRIST (Deemed to be University), Bangalore
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="discipline">Discipline Applied</label>
                        <select class="select2 select-event-label form-select" id="discipline" name="discipline">
                            <option disabled selected>Select</option>

                            <option data-label="danger" value="Management">Management</option>
                            <option data-label="info" value="Social-Sciences">Social Sciences</option>
                        </select>
                    </div>
                    <div class="mb-3 programme">
                        <label class="form-label" for="programme">Programmes Selected</label>
                        <select class="select2 select-event-label form-select" id="programme" name="v">
                            <option disabled selected>Select</option>

                            <option class="CHRIST-Management" data-label="danger" value="Business & Management">Business
                                & Management</option>

                            <option class="CHRIST-Social-Sciences" data-label="info" value="Sociology & Social Work">
                                Sociology & Social Work</option>

                            <option class="Amrita-Management" data-label="info" value="Social">Marketing</option>
                            <option class="Amrita-Management" data-label="info" value="Finance">Finance</option>
                            <option class="Amrita-Management" data-label="info" value="Operations">Operations</option>
                            <option class="Amrita-Management" data-label="info"
                                value="Organisational Behaviour & Human Resources">Organisational Behaviour & Human
                                Resources</option>
                            <option class="Amrita-Management" data-label="info" value="General Management">General
                                Management</option>

                            <option class="Amrita-Social-Sciences" data-label="info"
                                value="Social & Behavoural Sciences">Social & Behavoural Sciences</option>
                            <option class="Amrita-Social-Sciences" data-label="info"
                                value="Social Sciences & Technology">Social Sciences & Technology</option>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="type">Full-Time/Part-Time</label>
                        <select class="select2 select-event-label form-select" id="type" name="type">
                            <option disabled selected>Select</option>

                            <option class="Amrita CHRIST" data-label="danger" value="Part-Time">Part-Time</option>
                            {{--  <option class="CHRIST" data-label="danger" value="Full-Time">Full-Time</option>  --}}
                        </select>
                    </div>

                    <div id="input-container">

                        <div class="mb-3">
                            <label for="name" class="form-label">Educational Qualification</label>
                            <input type="text" class="form-control" id="education[]" name="education"
                                placeholder="Enter your educational qualification" autofocus>
                            <button id="add-input" class=" btn btn-success btn-sm mt-2">Add New Education </button>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="qualification">Are you Qualified for</label>
                        <select class="select2 select-event-label form-select" id="qualification" name="qualification">
                            <option disabled selected>Select</option>

                            <option data-label="danger" value="UGC NET">UGC NET</option>
                            <option data-label="danger" value="UGC NET/JRF">UGC NET/JRF</option>
                            <option data-label="danger" value="UGC CSIR NET">UGC CSIR NET</option>
                            <option data-label="danger" value="UGC CSIR NET/JRF">UGC CSIR NET/JRF</option>
                            <option data-label="danger" value="SLET">SLET</option>
                            <option data-label="danger" value="GATE">GATE</option>
                            <option data-label="danger" value="CEED">CEED</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="addl_qualification" class="form-label">Other National Level Test, Please
                            Specify</label>
                        <input type="text" class="form-control" id="addl_qualification" name="addl_qualification"
                            placeholder="Other National Level Test, Please Specify" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block"> Do you belong to SC/ST/OBC (Non Creamy layer)/Differentially
                            Abled/Economically Weaker Section
                            (EWS): Yes/No?</label>
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" checked name="viewTypeOptinon"
                                id="viewTypeOptinon" value="html" />
                            <label class="form-check-label" for="inlineRadio1">
                                Yes</label>
                        </div>

                        <div class="form-check form-check-inline mt-3 detailed-radio">
                            <input class="form-check-input" type="radio" name="viewTypeOptinon" id="viewTypeOptinon"
                                value="monitor" />
                            <label class="form-check-label" for="inlineRadio1">
                                No</label>
                        </div>

                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">
                            Category</label>
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" checked name="category" id="category"
                                value="html" />
                            <label class="form-check-label" for="inlineRadio1">
                                SC</label>
                        </div>

                        <div class="form-check form-check-inline mt-3 detailed-radio">
                            <input class="form-check-input" type="radio" name="category" id="category"
                                value="monitor" />
                            <label class="form-check-label" for="inlineRadio1">
                                ST</label>
                        </div>
                        <div class="form-check form-check-inline mt-3 detailed-radio">
                            <input class="form-check-input" type="radio" name="category" id="category"
                                value="monitor" />
                            <label class="form-check-label" for="inlineRadio1">
                                OBC (Non Creamy layer)</label>
                        </div>
                        <div class="form-check form-check-inline mt-3 detailed-radio">
                            <input class="form-check-input" type="radio" name="category" id="category"
                                value="monitor" />
                            <label class="form-check-label" for="inlineRadio1">
                                Economically Weaker Section
                                (EWS)</label>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block"> Do you belong to Differentially Abled Yes/No?</label>
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" checked name="viewTypeOptinon"
                                id="viewTypeOptinon" value="html" />
                            <label class="form-check-label" for="inlineRadio1">
                                Yes</label>
                        </div>

                        <div class="form-check form-check-inline mt-3 detailed-radio">
                            <input class="form-check-input" type="radio" name="viewTypeOptinon" id="viewTypeOptinon"
                                value="monitor" />
                            <label class="form-check-label" for="inlineRadio1">
                                No</label>
                        </div>

                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="qualification">Professional Qualification</label>
                        <select class="select2 select-event-label form-select" id="qualification" name="qualification">
                            <option disabled selected>Select</option>

                            <option data-label="danger" value="Chartered Accountancy">Chartered Accountancy</option>
                            <option data-label="danger" value="Cost Accountancy">Cost Accountancy</option>
                            <option data-label="danger" value="Company Secretary ">Company Secretary </option>

                        </select>
                    </div>


                    <button class="btn btn-primary d-grid w-100">
                        Register
                    </button>
                </form>
                <p class="text-center">
                    <span>Centre for Management Development</span>
                    {{--  <a href="{{url('auth/register-cover')}}">
                    <span>Create an account</span>
                    </a> --}}
                </p>

                <div class="divider my-4">
                    <div class="divider-text">Follow us</div>
                </div>


                <div class="d-flex justify-content-center">
                    <a href="https://www.facebook.com/cmdkerala/" class="btn btn-icon btn-label-facebook me-3">
                        <i class="tf-icons fa-brands fa-facebook-f fs-5"></i>
                    </a>

                    <a href="https://www.youtube.com/@cmdkerala/" class="btn btn-icon btn-label-google-plus me-3">
                        <i class="tf-icons fa-brands fa-youtube fs-5"></i>
                    </a>

                    {{--  <a href="javascript:;" class="btn btn-icon btn-label-twitter">
            <i class="tf-icons fa-brands fa-twitter fs-5"></i>
          </a>  --}}
                    <a href="https://www.linkedin.com/company/cmdkerala/" class="btn btn-icon btn-label-linkedin me-3">
                        <i class="tf-icons fa-brands fa-linkedin fs-5"></i>
                    </a>
                    <a href="https://www.instagram.com/cmdkerala/" class="btn btn-icon btn-label-instagram">
                        <i class="tf-icons fa-brands fa-instagram fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Register -->
    </div>
</div>
@endsection