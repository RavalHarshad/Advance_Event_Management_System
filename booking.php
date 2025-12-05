<div class="container-fluid">
	<form action="" id="manage-book">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
		<input type="hidden" name="venue_id" value="<?php echo isset($_GET['venue_id']) ? $_GET['venue_id'] :'' ?>">
		
		<div class="form-group">
			<label for="" class="control-label">Full Name</label>
			<input type="text" class="form-control" name="name" value="<?php echo isset($name) ? $name :'' ?>" required>
		</div>
		
		<div class="form-group">
			<label for="" class="control-label">Decoration For Event</label>
			<?php echo isset($event) ? $event:'' ?>
			</br>
			<select name="event">
				<option value="birthday">Birthday</option>
				<option value="wedding">Wedding</option> 
				<option value="anniversary">Anniversary</option>
			</select>
		</div>

		<div class="form-group">
			<label for="" class="control-label">Email</label>
			<input type="email" class="form-control" name="email" value="<?php echo isset($email) ? $email :'' ?>" required>
		</div>

		<div class="form-group">
			<label for="" class="control-label">Contact</label>
			<input type="text" class="form-control" name="contact" value="<?php echo isset($contact) ? $contact :'' ?>" required>
		</div>

		<div class="form-group">
			<label for="" class="control-label">Duration</label>
			<?php echo isset($duration) ? $duration :'' ?>
			<br/>
			<select name="duration">
				<option value="morning">Morning </option>
				<option value="evening">Evening</option> 
				<option value="night">Night</option>
			</select>
		</div>

		<div class="form-group">
			<label for="" class="control-label">Desired Event Schedule</label>
			<input type="text" class="form-control datetimepicker schedule" name="schedule" value="<?php echo isset($schedule) ? $schedule :'' ?>" required>
		</div>

		<div class="form-group">
			<label for="" class="control-label">End Duration</label>
			<input type="text" class="form-control datetimepicker end-duration" name="end_duration" value="<?php echo isset($end_duration) ? $end_duration :'' ?>" required>
		</div>
	</form>
</div>
<script>
	$('.datetimepicker').datetimepicker({
    format: 'Y/m/d H:i',
    minDate: 0, // This disables past dates
    startDate: '+3d' // Ensures selection starts from 3 days later
});


	// Ensure End Duration is after Start Date
	$('input[name="schedule"]').on('change', function() {
		var startDateTime = $(this).val();
		if (!startDateTime) return;

		// Destroy previous instance and set new restrictions
		$('input[name="end_duration"]').datetimepicker('destroy');
		$('input[name="end_duration"]').datetimepicker({
			format: 'Y/m/d H:i',
			startDate: startDateTime // Ensures end date cannot be before start date
		});
	});
	
	$('#manage-book').submit(function(e){
		e.preventDefault();
		
		// Get form values
		var name = $('input[name="name"]').val().trim();
		var event = $('select[name="event"]').val();
		var email = $('input[name="email"]').val().trim();
		var contact = $('input[name="contact"]').val().trim();
		var duration = $('select[name="duration"]').val();
		var schedule = $('input[name="schedule"]').val().trim();
		var endDuration = $('input[name="end_duration"]').val().trim();

		var contactPattern = /^[0-9]{10}$/; // Contact should be exactly 10 digits
		var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Valid email pattern

		var errorMessage = "";

		// **Check if any field is empty**
		if (!name || !event || !email || !contact || !duration || !schedule || !endDuration) {
			alert("All fields are required.");
			return false; // Stop form submission
		}

		// **Validate Contact Number**
		if (!contactPattern.test(contact)) {
			errorMessage += "Contact number must be a 10-digit number.\n";
		}

		// **Validate Email Format**
		if (!emailPattern.test(email)) {
			errorMessage += "Invalid email format.\n";
		}

		// **Ensure End Duration is after Start Date**
		var startDate = new Date(schedule.replace(/-/g, '/'));
		var endDate = new Date(endDuration.replace(/-/g, '/'));

		if (endDate <= startDate) {
			errorMessage += "End Duration must be after Desired Event Schedule.\n";
		}

		// **If validation fails, show an error message and prevent submission**
		if (errorMessage) {
			alert(errorMessage.trim());
			return false;
		}

		start_load();
		$('#msg').html('');
		
		// **Check if venue is already booked (AJAX Request)**
		$.ajax({
			url: 'admin/ajax.php?action=save_book',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			dataType: 'json',
			success: function(resp){
				end_load();
				if(resp.status == "error"){
					alert(resp.message); // Show error message if venue is already booked
				} else if(resp.status == "success"){
					alert_toast("Book Request Sent.", 'success');
					uni_modal("", "book_msg.php");
				}
			},
			error: function(){
				end_load();
				alert("Something went wrong. Please try again.");
			}
		});
	});
</script>
