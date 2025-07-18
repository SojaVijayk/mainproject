document.addEventListener('DOMContentLoaded', function() {
    // Handle assignment type change in checkout forms
    const assignedType = document.getElementById('assigned_type');
    if (assignedType) {
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
        
        // Trigger change event on page load if there's a value
        if (assignedType.value) {
            assignedType.dispatchEvent(new Event('change'));
        }
    }
    
    // Initialize date pickers
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });
});