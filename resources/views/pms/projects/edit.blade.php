@extends('layouts/layoutMaster')

@section('title', 'Edit Project')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />

@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>

@endsection

@section('page-script')
<script>
  let teamMembers = @json($teamMembersData);
renderTeamList();

 const budgetInput = document.getElementById('budget');
    const expenseInput = document.getElementById('estimated_expense');
    const revenueInput = document.getElementById('revenue');
    function calculateRevenue() {
      const budget = parseFloat(budgetInput.value) || 0;
      const expense = parseFloat(expenseInput.value) || 0;
      const revenue = budget - expense;
      revenueInput.value = revenue >= 0 ? revenue.toFixed(2) : 0;
    }

    budgetInput.addEventListener('input', calculateRevenue);
    expenseInput.addEventListener('input', calculateRevenue);

$('#user_selector').on('change', function () {
    const userId = $(this).val();
    const userName = $(this).find('option:selected').text();

    if (!userId || teamMembers.some(m => m.user_id == userId)) return;

    const newMember = {
        user_id: userId,
        name: userName,
        role: 'member',
        expected_time: 0
    };

    teamMembers.push(newMember);
    renderTeamList();
    $(this).val('');
});

function renderTeamList() {
    let html = '';
    teamMembers.forEach((member, index) => {
        html += `
            <div class="row align-items-center mb-2" data-index="${index}">
                <div class="col-md-4">${member.name}</div>
                <div class="col-md-3">
                    <select class="form-select role-select">
                        <option value="member" ${member.role == 'member' ? 'selected' : ''}>Member</option>
                        <option value="lead" ${member.role == 'lead' ? 'selected' : ''}>Lead</option>
                    </select>
                </div>
                <div class="col-md-3">
                  <label for="project_investigator_id" class="form-label">Time Investment (Hours)</label>
                    <input type="number" step="0.1" min="0" class="form-control time-input" value="${member.expected_time}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-member">X</button>
                </div>
            </div>
        `;
    });

    $('#selected_users_list').html(html);
    updateTeamJson();
}

$(document).on('change', '.role-select, .time-input', function () {
    const row = $(this).closest('.row');
    const index = row.data('index');
    const newRole = row.find('.role-select').val();
    const newTime = parseFloat(row.find('.time-input').val()) || 0;

    teamMembers[index].role = newRole;
    teamMembers[index].expected_time = newTime;
    updateTeamJson();
});

$(document).on('click', '.remove-member', function () {
    const index = $(this).closest('.row').data('index');
    teamMembers.splice(index, 1);
    renderTeamList();
});

function updateTeamJson() {
    $('#team_members_json').val(JSON.stringify(teamMembers));
}
</script>
@endsection

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Edit Project: {{ $project->title }}</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('pms.projects.update', $project->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="title" class="form-label">Project Title</label>
              <input type="text" name="title" id="title" class="form-control"
                value="{{ old('title', $project->title) }}" required>
              @error('title')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="project_investigator_id" class="form-label">Project Investigator</label>
              <select name="project_investigator_id" id="project_investigator_id" class="form-select"
                style="pointer-events: none; background-color: #e9ecef;" tabindex="-1" required>
                <option value="">Select Investigator</option>
                @foreach($faculty as $member)
                <option value="{{ $member->id }}" {{ old('project_investigator_id', $project->project_investigator_id)
                  == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
              </select>
              @error('project_investigator_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              {{-- <div class="mb-3">
                <label class="form-label">Project Investigator Expected Time (in hours)</label>
                <input type="number" step="0.1" min="0" name="pi_expected_time" class="form-control" required>
              </div> --}}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="date" name="start_date" id="start_date" class="form-control"
                value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" required>
              @error('start_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="end_date" class="form-label">End Date</label>
              <input type="date" name="end_date" id="end_date" class="form-control"
                value="{{ old('end_date', $project->end_date->format('Y-m-d')) }}" required>
              @error('end_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label for="budget" class="form-label">Budget (₹ Without Tax)</label>
              <input type="number" step="0.01" min="0" name="budget" id="budget" class="form-control"
                value="{{ old('budget', $project->budget) }}" required>
              @error('budget')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="estimated_expense" class="form-label">Estimated Expense (₹ Without Tax)</label>
              <input type="number" step="0.01" min="0" name="estimated_expense" id="estimated_expense"
                class="form-control" value="{{ old('estimated_expense', $project->estimated_expense) }}" required>
              @error('estimated_expense')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="revenue" class="form-label">Expected Revenue (₹ Without Tax)</label>
              <input type="number" step="0.01" min="0" name="revenue" id="revenue" class="form-control"
                value="{{ old('revenue', $project->revenue) }}" required>
              @error('revenue')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" id="description" class="form-control"
                rows="3">{{ old('description', $project->description) }}</textarea>
              @error('description')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Team Members</label>
              <select name="team_members[]" id="team_members" class="form-select" multiple>
                @foreach($staff as $member)
                <option value="{{ $member->id }}" {{ in_array($member->id, $teamMemberIds) ? 'selected' : '' }}>{{
                  $member->name }}</option>
                @endforeach
              </select>
              @error('team_members')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <small class="text-muted">Hold Ctrl/Cmd to select multiple members</small>
            </div>
          </div> --}}

          @php
          use App\Models\User;
          @endphp
          <div class="mb-3">
            <label class="form-label">Add Team Member</label>
            <select id="user_selector" class="form-select">
              <option value="">Select a user</option>
              @foreach(User::where('id', '!=', auth()->id())->where('active',1)->orderBy('name', 'asc')->get() as $user)
              <option value="{{ $user->id }}" {{ in_array($user->id, $teamMemberIds) ? 'selected' : '' }}>{{
                $user->name }}</option>
              @endforeach
            </select>
          </div>

          <div id="selected_users_list">
            <!-- Dynamic list will be appended here -->
          </div>
          <input type="hidden" name="team_members_json" id="team_members_json">

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Update Project</button>
            <a href="{{ route('pms.projects.show', $project->id) }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Project Details</h5>
      </div>
      <div class="card-body">
        <p><strong>Project Code:</strong> {{ $project->project_code }}</p>
        <p><strong>Status:</strong>
          <span class="badge bg-{{ $project->status_badge_color }}">
            {{ $project->status_name }}
          </span>
        </p>
        <p><strong>Completion:</strong> {{ $project->completion_percentage }}%</p>
        <p><strong>Created At:</strong> {{ $project->created_at->format('d M Y') }}</p>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-header">
        <h5 class="card-title">Linked Requirement</h5>
      </div>
      <div class="card-body">
        <p><strong>Client:</strong> {{ $project->requirement->client->client_name }}</p>
        <p><strong>Category:</strong> {{ $project->requirement->category->name }}</p>
        <p><strong>Contact Person:</strong> {{ $project->requirement->contactPerson->name }}</p>
        <a href="{{ route('pms.requirements.show', $project->requirement->id) }}"
          class="btn btn-sm btn-info w-100 mt-2">
          <i class="fas fa-eye"></i> View Requirement
        </a>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-header">
        <h5 class="card-title">Linked Proposal</h5>
      </div>
      <div class="card-body">
        <p><strong>Budget:</strong> ₹{{ number_format($project->proposal->budget, 2) }}</p>
        <p><strong>Tenure:</strong> {{ $project->proposal->tenure }}</p>
        <p><strong>Status:</strong> {{ $project->proposal->status_name }}</p>
        <a href="{{ route('pms.proposals.show', $project->proposal->id) }}" class="btn btn-sm btn-info w-100 mt-2">
          <i class="fas fa-eye"></i> View Proposal
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
  /* Style for select2-like multiple select */
  select[multiple] {
    height: auto;
    min-height: 100px;
  }
</style>
@endsection