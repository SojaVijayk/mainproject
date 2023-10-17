<!-- Add Designation Modal -->
<div class="modal fade" id="DesignationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="mb-2 designation-title">Request Leave</h3>

        </div>

        <form id="designationForm" class="row" onsubmit="return false">
          <div class="mb-3">
            <label class="form-label" for="leaveType">Label</label>
            <select class="select2 select-event-label form-select" id="leaveType" name="leaveType">
              @foreach ($leave_types as $leave)
              <option value={{$leave->id}} >{{$leave->leave_type}}</option>
              @endforeach

            </select>
          </div>


          <div class="mb-3 col-6">
            <label for="fromDate" class="form-label">From</label>
            <input type="text" class="form-control datepicker" id="fromDate" name="fromDate" placeholder="MM/DD/YYYY" class="form-control" />

          </div>




          <div class="mb-3 col-6">
            <label for="toDate" class="form-label">To</label>
            <input type="text" class="form-control datepicker" id="toDate" name="toDate" placeholder="MM/DD/YYYY" class="form-control" />

          </div>
          <div class="mb-3 col-12">
            <ul id="dateList"></ul>
          </div>





          <div class="mb-3">
            <label class="form-label" for="eventDescription">Remark</label>
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
