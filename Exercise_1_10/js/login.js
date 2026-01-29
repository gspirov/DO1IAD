const signInForm = document.getElementById("login-form");

if (signInForm) {
    const users = {
        "motivateduser@aston.ac.uk": "Atleast1",
        "fitnessguru@aston.ac.uk": "Takingsupplements"
    }

    signInForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const emailEl = this.querySelector('#login-email');

        if (!emailEl) {
            return false;
        }

        const passwordEl = this.querySelector('#login-password');

        if (!passwordEl) {
            return false;
        }

        const email = emailEl.value.trim();
        const password = passwordEl.value.trim();

        if (!Object.hasOwn(users, email) || users[email] !== password) {
            alert("Invalid email or password.");
            location.reload();
        } else {
            alert("Successfully logged in!");
            location.href = "index.html";
        }
    });
}
