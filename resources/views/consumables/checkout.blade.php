@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Checkout Consumable: {{ $consumable->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Current Quantity:</strong> {{ $consumable->quantity }}</p>
                            <p><strong>Purchase Cost:</strong> ${{ number_format($consumable->purchase_cost, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($consumable->quantity <= $consumable->min_quantity)
                                <div class="alert alert-warning">
                                    <strong>Low Stock!</strong> Quantity is at or below minimum level.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <form action="{{ route('consumables.process-checkout', $consumable) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="quantity">Quantity to Checkout *</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" 
                                   name="quantity" value="{{ old('quantity', 1) }}" min="1" max="{{ $consumable->quantity }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="assigned_to_type">Assign To *</label>
                            <select class="form-control @error('assigned_to_type') is-invalid @enderror" id="assigned_to_type" name="assigned_to_type" required>
                                <option value="">Select Assignment Type</option>
                                <option value="user" {{ old('assigned_to_type') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="department" {{ old('assigned_to_type') == 'department' ? 'selected' : '' }}>Department</option>
                            </select>
                            @error('assigned_to_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group" id="user-field" style="display: none;">
                            <label for="user_id">User *</label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group" id="department-field" style="display: none;">
                            <label for="department_id">Department *</label>
                            <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="floor">Floor/Location (Optional)</label>
                            <input type="text" class="form-control @error('floor') is-invalid @enderror" id="floor" 
                                   name="floor" value="{{ old('floor') }}">
                            @error('floor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" 
                                      name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Checkout</button>
                        <a href="{{ route('consumables.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#assigned_to_type').change(function() {
            if ($(this).val() === 'user') {
                $('#user-field').show();
                $('#department-field').hide();
                $('#department_id').val('');
            } else if ($(this).val() === 'department') {
                $('#user-field').hide();
                $('#department-field').show();
                $('#user_id').val('');
            } else {
                $('#user-field').hide();
                $('#department-field').hide();
                $('#user_id').val('');
                $('#department_id').val('');
            }
        });
        
        // Trigger change event on page load if there's a selected value
        if ($('#assigned_to_type').val() === 'user') {
            $('#user-field').show();
        } else if ($('#assigned_to_type').val() === 'department') {
            $('#department-field').show();
        }
    });
</script>
@endpush