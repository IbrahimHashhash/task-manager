<?php 
include "header.php";
?>

<form action="signup.php" method="post" id="signup-form">
    <fieldset>
    <legend>Personal Info</legend>
    <label for="firstName">First Name:</label><br>
    <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required><br>
    <label for="lastName">Last Name:</label><br>
    <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required><br>
    <label for="dob">Date of Birth:</label><br>
    <input type="date" id="dob" name="dob" required><br>
    <label for="idNumber">ID Number:</label><br>
    <input type="text" id="idNumber" name="idNumber" pattern="[0-9]{8}" title="Please enter an 8-digit ID number" placeholder="Enter your 8-digit ID number" required><br>

    </fieldset>
    <fieldset>
    <legend> Address</legend>
    <label for="flat">Flat/House No:</label><br>
    <input type="text" id="flat" name="flat" placeholder="Enter your flat or house number" required><br>

    <label for="street">Street:</label><br>
    <input type="text" id="street" name="street" placeholder="Enter your street" required><br>

    <label for="city">City:</label><br>
    <input type="text" id="city" name="city" placeholder="Enter your city" required><br>

    <label for="country">Country:</label><br>
    <input type="text" id="country" name="country" placeholder="Enter your country" required><br>
    </fieldset>
    <fieldset>
    <legend> Contact Information</legend>
    <label for="email">E-mail Address:</label><br>
    <input type="email" id="email" name="email" placeholder="Enter your email address" required><br>

    <label for="telephone">Enter your phone number:</label><br>
    <input type="tel" id="telephone" name="telephone" pattern="(\+?\d{1,2}\s?)?(\(?\d{1,4}\)?[\s\-]?)?[\d\s\-]{7,15}" title="Phone number format: (123) 456-7890 or +44 20 7946 0958" placeholder="Enter your phone number"><br>
    </fieldset>
    <fieldset>
    <legend> Professional Information</legend>
    <label for="role">Role:</label><br>
    <select id="role" name="role" required>
        <option value="Manager">Manager</option>
        <option value="Project Leader">Project Leader</option>
        <option value="Team Member">Team Member</option>
    </select><br>

    <label for="qualification">Qualification:</label><br>
    <input type="text" id="qualification" name="qualification" placeholder="Enter your qualification" required><br>

    <label for="skills">Skills:</label><br>
    <textarea id="skills" name="skills" placeholder="Enter your skills" required></textarea><br><br>
    </fieldset>
    <input type="submit" value="Proceed">

</form>

<?php include "footer.html";?>
