// Submit form
document.getElementById('myForm').addEventListener('submit', function(e) {
  e.preventDefault();
  var formData = new FormData(this);
  fetch('https://script.google.com/macros/s/ABC/exec', { // URL Apps Script
    method: 'POST',
    body: formData
  }).then(response => alert('Submitted!')).catch(err => alert('Error'));
});

// Fetch và render data từ Sheets
fetch('https://script.google.com/macros/s/ABC/exec') // doGet URL
  .then(response => response.json())
  .then(data => {
    var contentDiv = document.getElementById('content');
    data.forEach(row => {
      contentDiv.innerHTML += `<p>ID: ${row[0]}, Content: ${row[1]}, Image: ${row[2]}</p>`;
    });
  });