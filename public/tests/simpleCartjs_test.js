// Cấu hình SimpleCart.js
simpleCart({
    checkout: {
        type: "SendForm",
        url: "/api/checkout", // Đường dẫn đến backend FastAPI
        method: "POST"
    },
    currency: "USD",
    cartStyle: "table",
    shippingFlatRate: 5.00,
    taxRate: 0.1
});

// Hàm gửi checkout custom
function customCheckout() {
    let cartData = simpleCart.toJSON();
    fetch('/api/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: cartData
    })
    .then(response => response.json())
    .then(data => {
        alert('Checkout successful!');
        console.log('Success:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
