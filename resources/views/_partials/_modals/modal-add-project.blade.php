<!-- Add Project Modal -->
<div class="modal addProjectModal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-project">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="project-title mb-2">Add New Project</h3>
          <p class="text-muted">Set project Clients</p>
        </div>
        <!-- Add project form -->
        <form id="addProjectForm" class="row g-3" onsubmit="return false">
          {{--  {{ csrf_field() }}  --}}
          <div class="col-12 mb-4">
            <label class="form-label" for="modalProjectName">Project Name</label>
            <input type="text" id="modalProjectName" name="modalProjectName" class="form-control" placeholder="Enter a project name" tabindex="-1" />
          </div>
          <div class="col-12 mb-4">
            <label class="form-label" for="modalProjectDescription">Project Description</label>
            {{--  <input type="text" id="modalProjectDescription" name="modalProjectDescription" class="form-control" placeholder="Enter a project description" tabindex="-1" />  --}}
            <textarea class="form-control" id="modalProjectDescription" name="modalProjectDescription" rows="3"></textarea>
          </div>
          <div class="col-12 mb-4">
            <label class="form-label" for="modalProjectType">Project Type</label>
            {{--  <input type="text" id="modalProjectType" name="modalProjectType" class="form-control" placeholder="Enter a project type" tabindex="-1" />  --}}

              {{--  <label for="select2Multiple" class="form-label">Multiple</label>  --}}
              <select  id="modalProjectType" name="modalProjectType" class="select2 form-select" multiple>
                @foreach ($projectTypes as $projectType)
                  <option value={{$projectType->id}} >{{$projectType->type_name}}</option>
                  @endforeach
              </select>

          </div>
          <div class="col-12 mb-4">
            <label class="form-label" for="leads">Project Leads</label>
              <select  id="leads" name="leads" class="select2 form-select" multiple>
                @foreach ($leads as $lead)
                  <option value={{$lead->id}} >{{$lead->name}}</option>
                  @endforeach
              </select>

          </div>
          <div class="col-12 mb-4">
            <label class="form-label" for="members">Team Members</label>
              <select  id="members" name="members" class="select2 form-select" multiple>
                @foreach ($members as $member)
                  <option value={{$member->id}} >{{$member->name}}</option>
                  @endforeach
              </select>

          </div>
          <div class="col-12">
            <h5>Project Clients</h5>
            <!-- Client table -->
            <div class="table-responsive">
              <table class="table table-flush-spacing">
                <tbody>
                  {{--  <tr>
                    <td class="text-nowrap fw-semibold">Administrator Access <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Allows a full access to the system"></i></td>
                    <td>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" />
                        <label class="form-check-label" for="selectAll">
                          Select All
                        </label>
                      </div>
                    </td>
                  </tr>  --}}
                  @foreach ($clients as $client)
                  <tr>
                    <td class="text-nowrap  fw-semibold">{{$client->client_name}}</td>
                    <td>
                      <div class="d-flex">
                        <div class="form-check me-3 me-lg-5">
                          <input class="form-check-input client-checkbox" data-id={{$client->id}} value={{$client->id}} type="checkbox" id={{$client->id}} />
                          <label class="form-check-label" for="userManagementRead">

                          </label>
                        </div>


                      </div>
                    </td>
                  </tr>
                  @endforeach





                </tbody>
              </table>
            </div>
            <!-- clients table -->
          </div>
          <div class="col-12 text-center mt-4">
            <button type="submit" id="submit_project" data-id="0" data-type="new" class="btn submit-project btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
        <!--/ Add project form -->
      </div>
    </div>
  </div>
</div>
<!--/ Add Project Modal -->
