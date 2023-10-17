<!-- Add Designation Modal -->
<div class="modal fade" id="DesignationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="mb-2 designation-title">Request Movement</h3>

        </div>

        <form id="designationForm" class="row" onsubmit="return false">
          <div class="mb-3">
            <label class="form-label" for="eventTitle">Title</label>
            <input type="text" class="form-control" id="eventTitle" name="eventTitle" placeholder="Event Title" />
          </div>
          <div class="mb-3">
            <label class="form-label" for="eventLabel">Label</label>
            <select class="select2 select-event-label form-select" id="eventLabel" name="eventLabel">
              <option data-label="primary" value="Field" selected>Field</option>
              <option data-label="danger" value="Personal">Personal</option>
              <option data-label="warning" value="Meeting">Meeting</option>
              <option data-label="success" value="Training">Training</option>
              <option data-label="info" value="Official">Official</option>
            </select>
          </div>
          <div class="row">
          <div class="mb-3 col-6">
            <label for="fromDate" class="form-label">From</label>
            <input type="text" class="form-control datepicker" id="fromDate" name="fromDate" placeholder="MM/DD/YYYY" class="form-control" />

          </div>
          <div class="mb-3 col-6">
            <label for="timepicker-step" class="form-label">Time</label>
            <input type="text" class="form-control timepicker" id="fromTime" placeholder="HH:MMam" class="form-control" />
          </div>
        </div>

        <div class="row">
          <div class="mb-3 col-6">
            <label for="toDate" class="form-label">To</label>
            <input type="text" class="form-control datepicker" id="toDate" name="toDate" placeholder="MM/DD/YYYY" class="form-control" />

          </div>
          <div class="mb-3 col-6">
            <label for="timepicker-step" class="form-label">Time</label>
            <input type="text" class="form-control timepicker" id="toTime" placeholder="HH:MMam" class="form-control" />
          </div>
        </div>


          <div class="mb-3">
            <label class="form-label" for="eventLocation">Location</label>
            <input type="text" class="form-control" id="eventLocation" name="eventLocation" placeholder="Enter Location" />
          </div>
          <div class="mb-3">
            <label class="form-label" for="eventDescription">Description</label>
            <textarea class="form-control" name="eventDescription" id="eventDescription"></textarea>
          </div>

          <div class="col-12 text-center demo-vertical-spacing">
            <button type="submit" id="submit_designation" data-id="0" data-type="new" class="btn btn-primary me-sm-3 me-1 submit-designation">Save</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Discard</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Add Permission Modal -->
