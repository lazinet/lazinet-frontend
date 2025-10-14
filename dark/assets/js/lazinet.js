// function toggleFlip(card) {
//     card.classList.toggle('flipped');
//   }  

// function googleTranslateElementInit() {
      //   new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
      // }
    
      function googleTranslateElementInit() {
        new google.translate.TranslateElement({
          pageLanguage: 'en',
          layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
          "includedLanguages": "vi,zh-CN,hi,es,fr,ar,ru,pt,ja,ko,id,ms,th,my,km,lo,tl,de,it,fa,ur,pl,tr,uk,ro,nl,sv,cs,he,el,hu,da,fi",
          autoDisplay: false
        }, 'google_translate_element');
      }

// BONG BÓNG CHAT
    document.addEventListener('DOMContentLoaded', function() {
    const mainBtn = document.querySelector('.lazinet-contact-main-btn');
    const contactButtons = document.querySelector('.lazinet-contact-buttons');
    const contactBtns = document.querySelectorAll('.lazinet-contact-btn');
    
    // Main button click - Ẩn nút chính, hiện 4 nút con
    if (mainBtn) {
        mainBtn.addEventListener('click', function() {
            // Ẩn nút chính
            this.classList.add('hidden');
            // Hiện 4 nút con
            contactButtons.classList.add('active');
        });
    }
    
    // Xử lý click cho từng nút con
    contactBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.classList[1]; // phone, zalo, messenger, ai-bot
            
            // Thực hiện chức năng tương ứng
            switch(type) {
                case 'phone':
                    window.open('tel:+84-908556220');
                    break;
                case 'zalo':
                    window.open('http://zalo.me/0908556220', '_blank');
                    break;
                case 'messenger':
                    window.open('https://m.me/61567050494124', '_blank');
                    break;
                case 'ai-bot':
                    openAIChat();
                    break;
            }
            
            // Sau khi click: Ẩn 4 nút con, hiện lại nút chính
            contactButtons.classList.remove('active');
            mainBtn.classList.remove('hidden');
        });
    });
    
    function openAIChat() {
        // Thêm logic mở AI chat của bạn ở đây
        console.log('Opening AI Chat...');
        // Ví dụ: window.open('/ai-chat', '_blank');
    }
    
    // Optional: Đóng menu khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.lazinet-contact-floating') && 
            contactButtons.classList.contains('active')) {
            contactButtons.classList.remove('active');
            mainBtn.classList.remove('hidden');
        }
    });
});