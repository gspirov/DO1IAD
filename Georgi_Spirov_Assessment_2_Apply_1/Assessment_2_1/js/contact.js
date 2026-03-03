const contactForm = document.querySelector('#contact-form');

const emailInput = document.getElementById('email');
const confirmEmailInput = document.getElementById('confirmEmail');
const projectDateInput = document.getElementById('projectDate');
const phoneInput = document.getElementById('phone');

function checkEmail() {
    const email = this.value.trim();

    if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
        this.setCustomValidity("Please enter a valid email address.")
    } else {
        this.setCustomValidity("");
    }
}

function checkEmails() {
    if (!emailInput) {
        return;
    }

    const email = this.value.trim();
    const confirmEmail = emailInput.value.trim();

    if (email !== confirmEmail) {
        this.setCustomValidity("Emails mismatch.")
    } else {
        this.setCustomValidity("");
    }
}

function checkDate() {
    const projectDate = new Date(this.value.trim());
    projectDate.setHours(0, 0, 0, 0);

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);

    if (projectDate < tomorrow) {
        this.setCustomValidity('Project date must be at least 1 day in the future.');
    } else {
        this.setCustomValidity('');
    }
}

function checkPhone() {
    const phone = this.value.trim();

    if (phone && !/^(?:\+44|0044|0)7[\d\s-]{9,13}$/.test(phone)) {
        phoneInput.setCustomValidity('Please enter a valid UK mobile number.');
    } else {
        phoneInput.setCustomValidity('');
    }
}

if (emailInput) {
    emailInput.addEventListener('input', checkEmail);
}

if (confirmEmailInput) {
    confirmEmailInput.addEventListener('input', checkEmails);
}

if (phoneInput) {
    phoneInput.addEventListener('input', checkPhone);
}

if (projectDateInput) {
    projectDateInput.addEventListener('input', checkDate);
}

if (contactForm) {
    contactForm.addEventListener('submit', function (event) {
        if (!this.reportValidity()) {
            event.preventDefault();
        }

        this.submit();

        const data = new FormData(this);
        const [year, month, day] = data.get('projectDate').split('-');
        const projectDateFormatted = `${day}.${month}.${year}`;

        const preferredContactMethodEl = this.querySelector('#preferredContactMethod');
        const preferredContactMethodText = preferredContactMethodEl.options[preferredContactMethodEl.selectedIndex].text;

        let message = `
            Thank you for contacting us!
            An email was sent to 250400247@aston.ac.uk with your request:
            Email: ${data.get('email')}
            First name: ${data.get('firstName')}
            Phone: ${data.get('phone')}
            Preferred Contact Method: ${preferredContactMethodText}
            Project Date: ${projectDateFormatted}
            Duration (days): ${data.get('duration')}
        `;

        alert(message);

        location.reload();
    });
}