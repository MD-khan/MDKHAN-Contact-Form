// ES6
document.addEventListener('DOMContentLoaded', function (e) {

    let contactForm = document.getElementById('mk-contact-form');

    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        // reset From Messages
        resetMessage();
        //collect form data
        let data = {
            name: contactForm.querySelector('[name="name"]').value,
            email: contactForm.querySelector('[name="email"]').value,
            message: contactForm.querySelector('[name="message"]').value,
            nonce: contactForm.querySelector('[name="nonce"]').value
        }
        if (!data.name) {
            contactForm.querySelector('[data-error="invalidName"]').classList.add('show');
            return;
        }
        if (!validateEmail(data.email)) {
            contactForm.querySelector('[data-error="invalidEmail"]').classList.add('show');
            return;
        }
        if (!data.message) {
            contactForm.querySelector('[data-error="invalidMessage"]').classList.add('show');
            return;
        }

        // ajax post request
        let url = contactForm.dataset.url;

        let params = new URLSearchParams(new FormData(contactForm));
        // shop the data prosessing message
        contactForm.querySelector('.js-form-submission').classList.add('show');

        fetch(url, {
            method: "POST",
            body: params,
        }).then(res => res.json())
            .catch(error => {
                resetMessage();
                contactForm.querySelector('.js-form-error').classList.add('show');
            })
            .then(response => {
                resetMessage();
                // deal with responses coming from controller
                if (response === 0 || response.status === 'error') {
                    contactForm.querySelector('.js-form-error').classList.add('show');
                    return;
                }

                contactForm.querySelector('.js-form-success').classList.add('show');
                contactForm.reset();
            });


    });

});

function resetMessage() {
    document.querySelectorAll('.field-msg').forEach(field => field.classList.remove('show'));
}

function validateEmail(email) {
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}








