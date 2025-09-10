// validate.js
// function submitForm(event) {
//     event.preventDefault(); // Ngăn chặn hành động gửi form mặc định

//     const form = document.getElementById('lazinet-contact-form');
//     const formData = {
//         name: form.name.value,
//         email: form.email.value,
//         subject: form.subject.value,
//         message: form.message.value
//     };

//     fetch('http://localhost:8000/api/contact', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify(formData)
//     })
//     .then(response => {
//         if (!response.ok) {
//             throw new Error('Network response was not ok');
//         }
//         return response.json();
//     })
//     .then(data => {
//         console.log("Success:", data);
//         alert("Your message has been sent. Thank you!");
//         form.reset(); // Reset form sau khi gửi thành công
//     })
//     .catch((error) => {
//         console.error("Error:", error);
//         alert("There was a problem sending the email.");
//     });
// }

// validate.js
function submitForm(event) {
    event.preventDefault(); // Ngăn chặn hành động gửi form mặc định

    const form = document.getElementById('lazinet-contact-form');
    const formData = {
        name: form.name.value,
        email: form.email.value,
        subject: form.subject.value,
        message: form.message.value
    };

    fetch('http://localhost:8000/api/contact', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log("Success:", data);
        alert("Your message has been sent. Thank you!");
        form.reset(); // Reset form sau khi gửi thành công
    })
    .catch((error) => {
        console.error("Error:", error);
        alert("There was a problem sending the email.");
    });
}

// function submitNewsletter(event) {
//     event.preventDefault(); // Ngăn chặn hành động gửi form mặc định

//     const form = document.getElementById('newsletter-form');
//     const formData = {
//         email: form.email.value
//     };

//     fetch('http://localhost:8000/api/newsletter', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify(formData)
//     })
//     .then(response => {
//         if (!response.ok) {
//             throw new Error('Network response was not ok');
//         }
//         return response.json();
//     })
//     .then(data => {
//         console.log("Success:", data);
//         alert("Your subscription request has been sent. Thank you!");
//         form.reset(); // Reset form sau khi gửi thành công
//     })
//     .catch((error) => {
//         console.error("Error:", error);
//         alert("There was a problem sending the subscription.");
//     });
// }

// // Đảm bảo rằng bạn đã thêm listener cho form newsletter
// document.getElementById('newsletter-form').addEventListener('submit', submitNewsletter);

async function submitNewsletter(event) {
    event.preventDefault(); // Ngăn chặn hành động gửi form mặc định

    const form = document.getElementById('newsletter-form');
    const formData = {
        email: form.email.value
    };

    try {
        const response = await fetch('http://localhost:8000/api/newsletter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            const err = await response.json();
            throw new Error(err.detail || 'An error occurred');
        }

        const data = await response.json();
        console.log("Success:", data);
        alert("Your subscription request has been sent. Thank you!");
        form.reset(); // Reset form sau khi gửi thành công
    } catch (error) {
        console.error("Error:", error);
        // Hiển thị thông báo nhẹ nhàng hơn cho người dùng
        alert("There was a problem sending your subscription. Please check your email.");
    }
}

// Đảm bảo rằng bạn đã thêm listener cho form newsletter
document.getElementById('newsletter-form').addEventListener('submit', submitNewsletter);
