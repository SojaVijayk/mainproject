<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="asset_tag">Asset Tag*</label>
      <input type="text" class="form-control @error('asset_tag') is-invalid @enderror" id="asset_tag" name="asset_tag"
        value="{{ old('asset_tag', $asset->asset_tag ?? '') }}" required>
      @error('asset_tag')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label for="name">Name*</label>
      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
        value="{{ old('name', $asset->name ?? '') }}" required>
      @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="model_id">Model*</label>
      <select class="form-control @error('model_id') is-invalid @enderror" id="model_id" name="model_id" required>
        <option value="">Select Model</option>
        @foreach($models as $model)
        <option value="{{ $model->id }}" {{ old('model_id', $asset->model_id ?? '') == $model->id ? 'selected' : '' }}>
          {{ $model->name }}
        </option>
        @endforeach
      </select>
      @error('model_id')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label for="status_id">Status*</label>
      <select class="form-control @error('status_id') is-invalid @enderror" id="status_id" name="status_id" required>
        <option value="">Select Status</option>
        @foreach($statuses as $status)
        <option value="{{ $status->id }}" {{ old('status_id', $asset->status_id ?? '') == $status->id ? 'selected' : ''
          }}>
          {{ $status->name }}
        </option>
        @endforeach
      </select>
      @error('status_id')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>
</div>

<!-- More form fields... -->

<div class="form-group text-right">
  <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
  <a href="{{ route('asset.index') }}" class="btn btn-secondary">Cancel</a>
</div>