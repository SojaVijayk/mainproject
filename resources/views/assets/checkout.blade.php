@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Checkout Asset: {{ $asset->asset_tag }} - {{ $asset->name }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('assets.process-checkout', $asset) }}">
                        @csrf
                        
                        <div class="form-group row">
                            <label for="assigned_type" class="col-md-4 col-form-label text-md-right">Assign To</label>
                            <div class="col-md-6">
                                <select id="assigned_type" class="form-control @error('assigned_type') is-invalid @enderror" name="assigned_type" required>
                                    <option value="">Select Assignment Type</option>
                                    <option value="user">User</option>
                                    <option value="department">Department</option>
                                </select>
                                
                                @error('assigned_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row" id="user-field" style="display: none;">
                            <label for="user_id" class="col-md-4 col-form-label text-md-right">User</label>
                            <div class="col-md-6">
                                <select id="user_id" class="form-control @error('user_id') is-invalid @enderror" name="user_id">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                
                                @error('user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row" id="department-field" style="display: none;">
                            <label for="department_id" class="col-md-4 col-form-label text-md-right">Department</label>
                            <div class="col-md-6">
                                <select id="department_id" class="form-control @error('department_id') is-invalid @enderror" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                
                                @error('department_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row" id="floor-field" style="display: none;">
                            <label for="floor" class="col-md-4 col-form-label text-md-right">Floor (Optional)</label>
                            <div class="col-md-6">
                                <input id="floor" type="text" class="form-control @error('floor') is-invalid @enderror" name="floor" value="{{ old('floor') }}">
                                
                                @error('floor')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="notes" class="col-md-4 col-form-label text-md-right">Notes (Optional)</label>
                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes">{{ old('notes') }}</textarea>
                                
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Checkout Asset
                                </button>
                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const assignedType = document.getElementById('assigned_type');
    const userField = document.getElementById('user-field');
    const departmentField = document.getElementById('department-field');
    const floorField = document.getElementById('floor-field');
    
    assignedType.addEventListener('change', function() {
        if (this.value === 'user') {
            userField.style.display = 'flex';
            departmentField.style.display = 'none';
            floorField.style.display = 'none';
        } else if (this.value === 'department') {
            userField.style.display = 'none';
            departmentField.style.display = 'flex';
            floorField.style.display = 'flex';
        } else {
            userField.style.display = 'none';
            departmentField.style.display = 'none';
            floorField.style.display = 'none';
        }
    });
});
</script>
@endsection