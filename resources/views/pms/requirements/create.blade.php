@extends('layouts/layoutMaster')

@section('title', 'Create Requirements')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />

<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/highlight/highlight.css')}}" />

@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>

<script src="{{asset('assets/vendor/libs/quill/katex.js')}}" />
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}" />
<script src="{{asset('assets/vendor/libs/highlight/highlight.js')}}" />

@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Dynamic subcategory loading
    const categorySelect = document.getElementById('project_category_id');
    const subcategorySelect = document.getElementById('project_subcategory_id');

    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;

        // Clear existing options
        subcategorySelect.innerHTML = '<option value="">Loading...</option>';

        if (categoryId) {
            fetch(`/project-categories/${categoryId}/subcategories`)
                .then(response => response.json())
                .then(data => {
                    subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                    data.forEach(subcategory => {
                        const option = document.createElement('option');
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        subcategorySelect.appendChild(option);
                    });
                });
        } else {
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        }
    });

    // Dynamic contact person loading
    const clientSelect = document.getElementById('client_id');
    const contactSelect = document.getElementById('client_contact_person_id');

    clientSelect.addEventListener('change', function() {
        const clientId = this.value;

        // Clear existing options
        contactSelect.innerHTML = '<option value="">Loading...</option>';

        if (clientId) {
            fetch(`/clients/${clientId}/contacts`)
                .then(response => response.json())
                .then(data => {
                    contactSelect.innerHTML = '<option value="">Select Contact Person</option>';
                    data.forEach(contact => {
                        const option = document.createElement('option');
                        option.value = contact.id;
                        option.textContent = contact.name;
                        contactSelect.appendChild(option);
                    });
                });
        } else {
            contactSelect.innerHTML = '<option value="">Select Contact Person</option>';
        }
    });
});
</script>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('pms.requirements.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="type_id" class="form-label">Type *</label>
              <select name="type_id" id="type_id" class="form-select" required>
                <option value="">Select Type</option>
                @foreach($types as $key => $value)
                <option value="{{ $key }}" {{ old('type_id')==$key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
              </select>
              @error('type_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="project_category_id" class="form-label">Category *</label>
              <select name="project_category_id" id="project_category_id" class="form-select" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('project_category_id')==$category->id ? 'selected' : '' }}>{{
                  $category->name }}</option>
                @endforeach
              </select>
              @error('project_category_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="project_subcategory_id" class="form-label">Sub Category</label>
              <select name="project_subcategory_id" id="project_subcategory_id" class="form-select">
                <option value="">Select Subcategory</option>
                {{-- @if(old('project_category_id'))
                @foreach(ProjectSubcategory::where('category_id', old('project_category_id'))->get() as $subcategory)
                <option value="{{ $subcategory->id }}" {{ old('project_subcategory_id')==$subcategory->id ? 'selected' :
                  '' }}>{{ $subcategory->name }}</option>
                @endforeach
                @endif --}}
              </select>
              @error('project_subcategory_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="ref_no" class="form-label">Project Title *</label>
              <input type="text" name="project_title" id="project_title" class="form-control"
                value="{{ old('project_title') }}">
              @error('project_title')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <label for="ref_no" class="form-label">Project Description *</label>
              <textarea name="project_description" id="project_description"
                class="form-control"> {{ old('project_description') }} </textarea>
              @error('project_description')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="client_id" class="form-label">Client *</label>
              <select name="client_id" id="client_id" class="form-select" required>
                <option value="">Select Client</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ old('client_id')==$client->id ? 'selected' : '' }}>{{
                  $client->client_name }}</option>
                @endforeach
              </select>
              @error('client_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="client_contact_person_id" class="form-label">Contact Person *</label>
              <select name="client_contact_person_id" id="client_contact_person_id" class="form-select" required>
                <option value="">Select Contact Person</option>
                {{-- @if(old('client_id'))
                @foreach(ClientContact::where('client_id', old('client_id'))->get() as $contact)
                <option value="{{ $contact->id }}" {{ old('client_contact_person_id')==$contact->id ? 'selected' : ''
                  }}>{{ $contact->name }}</option>
                @endforeach
                @endif --}}
              </select>
              @error('client_contact_person_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="ref_no" class="form-label">Reference No (Optional)</label>
              <input type="text" name="ref_no" id="ref_no" class="form-control" value="{{ old('ref_no') }}">
              @error('ref_no')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label for="documents" class="form-label">Documents (Optional)</label>
              <input type="file" name="documents[]" id="documents" class="form-control" multiple>
              @error('documents')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <small class="text-muted">You can upload multiple files (Max 10MB each)</small>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Create Requirement</button>
            <a href="{{ route('pms.requirements.index') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection