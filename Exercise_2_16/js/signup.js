const signupForm = document.getElementById("signup-form");

if (signupForm) {
    signupForm.addEventListener("submit", function(event) {
        event.preventDefault();

        if (!this.reportValidity()) {
            return;
        }

        const isValidEmail = validateEmail(this);
        const isValidPassword = validatePassword(this);

        if (!isValidEmail || !isValidPassword) {
            return false;
        }

        this.submit();

        alert('Successfully signed up, now you can log in!');

        location.href = "index.html";
    });

    function validateEmail(form) {
        const emailEl = form.querySelector('#email');

        if (!emailEl) {
            return false;
        }

        const email = emailEl.value.trim();

        const isValidEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);

        const emailErrorEl = form.querySelector('#email_error');

        if (emailErrorEl) {
            emailErrorEl.innerText = isValidEmail ? "" : "Please enter a valid email address.";
        }

        return isValidEmail;
    }

    function validatePassword(form) {
        const passwordEl = form.querySelector('#password');

        if (!passwordEl) {
            return false;
        }

        const password = passwordEl.value.trim();

        const isValidPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/.test(password);

        const passwordErrorEl = form.querySelector('#password_error');

        if (passwordErrorEl) {
            passwordErrorEl.innerText = isValidPassword ? "" : "Password must be at 8 characters long, including alphanumerics.";
        }

        return isValidPassword;
    }
}