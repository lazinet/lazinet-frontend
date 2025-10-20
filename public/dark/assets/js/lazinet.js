// function toggleFlip(card) {
//     card.classList.toggle('flipped');
//   }  

// function googleTranslateElementInit() {
      //   new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
      // }
    
      function googleTranslateElementInit() {
        new google.translate.TranslateElement({
          pageLanguage: 'vi',
          layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
          "includedLanguages": "en,zh-CN,hi,es,fr,ar,ru,pt,ja,ko,id,ms,th,my,km,lo,tl,de,it,fa,ur,pl,tr,uk,ro,nl,sv,cs,he,el,hu,da,fi",
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

// Random Content

    // Mảng chứa tất cả các phiên bản content
    const heroContents = [
        {
            subtitle: '<span>Mới!</span> Kiến trúc sư Chuyển đổi Số',
            title: 'Giải pháp công nghệ <span>mạnh mẽ, mở rộng & thông minh</span> thích ứng <span>với mọi thách thức kinh doanh</span>',
            description: 'Đội ngũ chuyên gia LAZINET kết hợp công nghệ tiên tiến với kinh nghiệm thực chiến để mang đến giải pháp toàn trình, đảm bảo doanh nghiệp bạn vận hành tối ưu và không gián đoạn.'
        },
        {
            subtitle: '<span>Đột phá!</span> Kiến tạo Doanh nghiệp Thông minh',
            title: 'Hệ sinh thái công nghệ <span>linh hoạt, thích ứng & tự động hóa</span> chuyển đổi <span>toàn diện vận hành</span>',
            description: 'Chuyên gia LAZINET tích hợp AI, IoT và nền tảng số tiên tiến vào giải pháp toàn diện, giúp doanh nghiệp bạn vận hành thông minh, hiệu quả vượt trội và tăng trưởng bền vững.'
        },
        {
            subtitle: '<span>Tiên phong!</span> Đối tác Công nghệ Chiến lược',
            title: 'Nền tảng số <span>toàn diện, thông minh & tùy biến</span> mang lại <span>lợi thế cạnh tranh đột phá</span>',
            description: 'Đội ngũ chuyên gia LAZINET phối hợp công nghệ đỉnh cao với hiểu biết sâu sắc ngành nghề để triển khai giải pháp toàn diện, biến thách thức số thành cơ hội phát triển vượt bậc.'
        },
        {
            subtitle: '<span>Mới!</span> Kiến trúc sư Doanh nghiệp Số',
            title: 'Giải pháp chuyển đổi số <span>mạnh mẽ, mở rộng & thông minh</span> thích ứng <span>hoàn hảo với tương lai</span>',
            description: 'Chuyên gia LAZINET kết hợp công nghệ tiên phong với kinh nghiệm đa ngành để cung cấp giải pháp toàn diện, đảm bảo doanh nghiệp bạn chuyển đổi thành công và dẫn đầu thị trường.'
        },
        {
            subtitle: '<span>Đột phá!</span> Kiến tạo Tương lai Số',
            title: 'Công nghệ <span>thông minh, linh hoạt & mạnh mẽ</span> chinh phục <span>mọi thách thức kinh doanh</span>',
            description: 'Đội ngũ chuyên gia LAZINET tích hợp giải pháp công nghệ tiên tiến với chiến lược kinh doanh, mang đến hệ sinh thái số toàn diện giúp doanh nghiệp vận hành hiệu quả và phát triển bền vững.'
        }
    ];

    // Hàm random content
    function randomizeHeroContent() {
        const randomIndex = Math.floor(Math.random() * heroContents.length);
        const content = heroContents[randomIndex];
        
        document.getElementById('hero-subtitle').innerHTML = content.subtitle;
        document.getElementById('hero-title').innerHTML = content.title;
        document.getElementById('hero-description').textContent = content.description;
        
        // Ghi log để debug (có thể xóa sau)
        console.log('Đã load hero content version:', randomIndex + 1);
    }

    // Chạy khi trang load
    document.addEventListener('DOMContentLoaded', randomizeHeroContent);

    // Hàm random Description Only
    function randomizeHeroDescriptionOnly() {
        const randomIndex = Math.floor(Math.random() * heroContents.length);
        const content = heroContents[randomIndex];

        document.getElementById('hero-description').textContent = content.description;
        
        // Ghi log để debug (có thể xóa sau)
        console.log('Đã load hero content version:', randomIndex + 1);
    }

    // Chạy khi trang load
    document.addEventListener('DOMContentLoaded', randomizeHeroDescriptionOnly);

    // Hoặc có thể random mỗi lần refresh
    // randomizeHeroContent();



//  FORM LIÊN HỆ
    
    document.getElementById('contact-form').addEventListener('submit', async (event) => {
      event.preventDefault(); // Ngăn hành vi mặc định của form
      button = document.getElementById('submit-btn');
      button.textContent = 'Sending your message ...';
      button.style.color = '#ffff00'; // Màu vàng

      // Lấy dữ liệu từ form
      const formData = {
        name: document.getElementById('name').value,
        userType: document.getElementById('user-type').value,
        otherType: document.getElementById('other-type').value,
        phone: document.getElementById('phone').value,
        email: document.getElementById('email').value,
        subject: document.getElementById('subject').value,
        message: document.getElementById('message').value,

      };

      try {
        // Gửi yêu cầu POST với no-cors
        await fetch('https://script.google.com/macros/s/AKfycbz4t2LtIb1In7obXC_EKujEH3ZJGbvrz3uevuqVw4d-zErIj3Tf10QWwxlWH2-5IJ7KqA/exec', {
          method: 'POST',
          mode: 'no-cors', // Bỏ qua kiểm tra CORS
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData),
        });

        // Hiển thị thông báo thành công
        button.textContent = 'Sent successfully, thank you for contacting LAZINET!';
        button.style.color = '#00FF00'; // Màu xanh lá
        button.disabled = true; // Khóa nút để tránh gửi lại
      } catch (error) {
        // Hiển thị thông báo lỗi
        button.textContent = `Error: ${error.message}`;
        button.style.backgroundColor = '#FF3333'; // Đổi màu nền (màu đỏ)
        // button.style.color = '#FF6347'; // Màu đỏ
      }
    });

    
    window.addEventListener('DOMContentLoaded', function () {
      const userTypeSelect = document.getElementById('user-type');

      // Kiểm tra nếu giá trị mặc định là ""
      function updateSelectColor() {
        if (userTypeSelect.value === "") {
          userTypeSelect.style.color = "#595959"; // Màu của placeholder
        } else {
          userTypeSelect.style.color = ""; // Đặt lại màu khi chọn giá trị khác
        }
      }

      // Cập nhật màu khi load trang
      updateSelectColor();

      // Thêm sự kiện khi người dùng chọn một giá trị khác
      userTypeSelect.addEventListener('change', function () {
        updateSelectColor(); // Cập nhật màu khi chọn thay đổi
      });
    });

    document.getElementById('user-type').addEventListener('change', function () {
      const otherInputContainer = document.getElementById('other-type');
      const userType = document.getElementById('user-type');

      if (this.value === 'Other') {
        userType.style.width = '30%'; // Thu hẹp dropdown userType
        otherInputContainer.style.display = 'inline-block'; // Hiển thị input
        otherInputContainer.setAttribute('required', ''); // Thêm thuộc tính required
      } else {
        userType.style.width = '100%'; // Trả lại kích thước ban đầu
        otherInputContainer.style.display = 'none'; // Ẩn trường nhập liệu
        otherInputContainer.removeAttribute('required'); // Xóa thuộc tính required
      }
    });
    

    // FORM NEWSLETTER
    async function submitNewsletter() {
      const email = document.getElementById('email-newsletter').value;
      const button = document.getElementById('newsletter-btn');

      // Kiểm tra email hợp lệ
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!emailRegex.test(email)) {
        button.textContent = 'Lỗi Email!';
        button.style.color = 'red';
        button.disabled = false;
        return;
      } else {
        button.textContent = 'Đang đăng ký';
        button.style.color = '#ff5000'; // Màu cam
        try {
          // Gửi yêu cầu POST
          const response = await fetch(
            'https://script.google.com/macros/s/AKfycbz38pxrAZj7NprlijMQszw3k1tRL1YC8Phzlcl7v7mAZfd8_JGqOgAP6rusC2ZkrK2E/exec',
            {
              method: 'POST',
              mode: 'no-cors', // Bỏ qua kiểm tra CORS
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email }),
            }
          );

          button.textContent = 'Đã đăng ký!';
          button.style.color = '#0000FF';
          button.disabled = true;
          document.getElementById('email-newsletter').value = "";

        } catch (error) {
          button.textContent = `Error: ${error.message}`;
          button.style.color = 'red';
          button.disabled = false;
        }
      }
    }