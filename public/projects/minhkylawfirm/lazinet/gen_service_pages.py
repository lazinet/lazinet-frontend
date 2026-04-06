# -*- coding: utf-8 -*-
"""Generate remaining service sub-pages for Minh Ky Lawfirm"""

import os

BASE = "d:/Dev_SW/projects/minhkylawfirm/services"

SERVICES = [
    {
        "id": "invest-ma-ip",
        "num": "04",
        "title": "M&A, Đầu tư & Sở hữu Trí tuệ",
        "short": "M&A & SHTT",
        "desc": "Tư vấn chiến lược pháp lý trong thương vụ M&A, cơ cấu đầu tư và bảo hộ tài sản trí tuệ của doanh nghiệp.",
        "thumb": "../assets/services/invest-ma-ip.jpg",
        "items": [
            ("🤝", "Tư vấn & soạn thảo hợp đồng M&A", "SPA, SHA, điều khoản chuyển nhượng, điều khoản bảo vệ"),
            ("🔍", "Due Diligence pháp lý", "Rà soát pháp lý toàn diện trước khi giao dịch"),
            ("💼", "Cấu trúc thương vụ đầu tư", "Tối ưu thuế, rủi ro pháp lý cho bên mua và bên bán"),
            ("™️", "Đăng ký nhãn hiệu & bản quyền", "Bảo hộ thương hiệu trong nước và quốc tế"),
            ("💡", "Đăng ký sáng chế & kiểu dáng CN", "Bảo vệ tài sản trí tuệ kỹ thuật và công nghệ"),
            ("⚖️", "Xử lý tranh chấp SHTT", "Xâm phạm nhãn hiệu, vi phạm bản quyền, bí mật thương mại"),
        ],
        "why": [
            "Hiểu biết sâu về thị trường M&A Việt Nam và khu vực",
            "Kết hợp kinh nghiệm pháp lý với tư duy kinh doanh thực tiễn",
            "Mạng lưới đối tác tư vấn tài chính, kiểm toán hỗ trợ toàn diện",
            "Bảo mật tuyệt đối thông tin giao dịch và chiến lược",
        ],
        "faqs": [
            ("Due Diligence pháp lý là gì và tại sao quan trọng?",
             "Due Diligence (thẩm định pháp lý) là quá trình rà soát toàn bộ hồ sơ pháp lý của doanh nghiệp mục tiêu trước khi M&A: giấy phép, hợp đồng, tranh chấp tiềm ẩn, nghĩa vụ thuế... Đây là bước không thể bỏ qua để tránh rủi ro hậu giao dịch."),
            ("Đăng ký nhãn hiệu tại Việt Nam mất bao lâu?",
             "Thời gian xét nghiệm hình thức: 1 tháng. Thẩm định nội dung: 9–12 tháng. Tổng thời gian đến khi có Giấy chứng nhận đăng ký nhãn hiệu thường là 12–18 tháng. Minh Kỳ hỗ trợ theo dõi và phản hồi các yêu cầu của Cục SHTT."),
            ("Chuyển nhượng cổ phần cần những thủ tục gì?",
             "Tùy loại hình doanh nghiệp: với CTCP cần thông báo cho cổ đông hiện hữu, ký hợp đồng chuyển nhượng, công bố thông tin (nếu đại chúng). Với TNHH cần thông báo Sở KH&ĐT trong 10 ngày. Minh Kỳ chuẩn bị đầy đủ hồ sơ và đại diện thực hiện."),
        ]
    },
    {
        "id": "labor-export",
        "num": "05",
        "title": "Pháp luật Lao động & Xuất khẩu Lao động",
        "short": "Lao động & XKLĐ",
        "desc": "Tư vấn toàn diện về quan hệ lao động, giải quyết tranh chấp và hỗ trợ pháp lý cho chương trình xuất khẩu lao động.",
        "thumb": "../assets/services/labor-export.webp",
        "items": [
            ("📄", "Soạn thảo & rà soát hợp đồng lao động", "HĐLĐ xác định thời hạn, không thời hạn, hợp đồng thử việc"),
            ("⚖️", "Giải quyết tranh chấp lao động", "Hòa giải, tố tụng tại Hội đồng trọng tài hoặc Tòa án"),
            ("💰", "Tư vấn BHXH, BHYT, BHTN", "Tuân thủ, điều chỉnh, giải quyết chế độ cho người lao động"),
            ("✈️", "Hỗ trợ pháp lý xuất khẩu lao động", "Hợp đồng đưa người đi làm việc nước ngoài, quyền lợi XKLĐ"),
            ("🎓", "Chương trình tu nghiệp sinh", "Nhật Bản, Hàn Quốc, Đài Loan: hợp đồng, quyền lợi, tranh chấp"),
            ("🌏", "Bảo vệ người lao động ở nước ngoài", "Xử lý vi phạm hợp đồng, hỗ trợ hồi hương, đòi bồi thường"),
        ],
        "why": [
            "Am hiểu luật lao động Việt Nam và các hiệp định lao động quốc tế",
            "Kinh nghiệm thực tiễn trong tranh chấp sa thải, chấm dứt hợp đồng trái luật",
            "Tư vấn cho cả người sử dụng lao động lẫn người lao động",
            "Hỗ trợ doanh nghiệp xây dựng nội quy lao động chuẩn pháp luật",
        ],
        "faqs": [
            ("Người lao động bị sa thải trái luật có thể làm gì?",
             "NLĐ bị sa thải trái luật có quyền khiếu nại đến Hội đồng hòa giải lao động, khởi kiện tại Tòa án để yêu cầu: nhận lại việc làm, bồi thường lương, và các khoản phụ cấp trong thời gian nghỉ việc. Minh Kỳ đại diện bảo vệ quyền lợi NLĐ."),
            ("Doanh nghiệp cần lưu ý gì khi chấm dứt hợp đồng lao động đúng luật?",
             "Cần tuân thủ: lý do chấm dứt hợp pháp, thông báo trước (30–45 ngày tùy loại HĐ), thanh toán đầy đủ trợ cấp thôi việc/mất việc, hoàn trả sổ BHXH và giấy tờ. Sai sót dễ dẫn đến tranh chấp tốn kém."),
            ("Đi xuất khẩu lao động cần ký những hợp đồng nào?",
             "Cần có: (1) Hợp đồng đưa người đi làm việc nước ngoài với doanh nghiệp được cấp phép, (2) Hợp đồng lao động với chủ sử dụng nước ngoài. Minh Kỳ rà soát để đảm bảo quyền lợi NLĐ được bảo vệ đầy đủ."),
        ]
    },
    {
        "id": "tax-lifecycle",
        "num": "06",
        "title": "Pháp luật về Thuế & Dịch vụ Pháp lý Trọn vòng đời DN",
        "short": "Thuế & Trọn vòng đời DN",
        "desc": "Tư vấn tuân thủ thuế, đại lý thuế, hỗ trợ thanh kiểm tra và đồng hành pháp lý với doanh nghiệp trong toàn bộ vòng đời.",
        "thumb": "../assets/services/tax-lifecycle.png",
        "items": [
            ("💰", "Tư vấn tuân thủ nghĩa vụ thuế", "Thuế TNDN, GTGT, TNCN, nhà thầu nước ngoài, chuyển nhượng vốn"),
            ("📊", "Đại lý thuế (Tax Agent)", "Kê khai, nộp thuế, quyết toán thuế thay mặt doanh nghiệp"),
            ("🔍", "Hỗ trợ thanh tra & kiểm tra thuế", "Chuẩn bị hồ sơ, đại diện làm việc với cơ quan thuế"),
            ("🔄", "Hoàn thuế & xử lý nợ thuế", "Hỗ trợ hoàn thuế GTGT, giải quyết nợ thuế, miễn giảm"),
            ("🏗️", "Thành lập & cơ cấu lại doanh nghiệp", "Các thủ tục pháp lý từ khi thành lập đến tái cơ cấu"),
            ("🔚", "Giải thể tự nguyện theo quy trình", "Quyết toán thuế, xử lý công nợ, đóng mã số thuế"),
        ],
        "why": [
            "Kết hợp chuyên môn pháp lý và am hiểu thực tiễn thuế doanh nghiệp",
            "Đại diện hiệu quả trong các buổi thanh tra, kiểm tra thuế",
            "Tư vấn lập kế hoạch thuế hợp pháp, tối ưu chi phí thuế",
            "Đồng hành suốt vòng đời doanh nghiệp – từ thành lập đến giải thể",
        ],
        "faqs": [
            ("Doanh nghiệp bị thanh tra thuế cần làm gì?",
             "Bước 1: Không tự ý cung cấp thông tin vượt yêu cầu. Bước 2: Liên hệ luật sư/đại lý thuế ngay. Bước 3: Rà soát hồ sơ trước buổi làm việc. Minh Kỳ sẽ đại diện làm việc với cơ quan thuế, bảo vệ quyền lợi hợp pháp của doanh nghiệp."),
            ("Khi nào doanh nghiệp được hoàn thuế GTGT?",
             "Doanh nghiệp được hoàn thuế GTGT khi: xuất khẩu hàng hóa, dịch vụ; dự án đầu tư giai đoạn trước khi phát sinh doanh thu; số thuế khấu trừ lớn hơn số thuế phát sinh liên tiếp 3 tháng. Thủ tục và thời hạn hoàn thuế phụ thuộc trường hợp cụ thể."),
            ("Quy trình giải thể doanh nghiệp tự nguyện như thế nào?",
             "Gồm 5 bước chính: (1) Nghị quyết giải thể, (2) Thông báo giải thể, (3) Quyết toán thuế và đóng mã số thuế, (4) Thanh toán công nợ, (5) Thông báo Sở KH&ĐT. Minh Kỳ đảm nhiệm toàn bộ quy trình, thường hoàn tất trong 2–4 tháng."),
        ]
    },
    {
        "id": "non-litigation-representation",
        "num": "07",
        "title": "Đại diện ngoài Tố tụng",
        "short": "Đại diện ngoài Tố tụng",
        "desc": "Đại diện ủy quyền toàn diện trong các giao dịch, thương lượng và làm việc với cơ quan nhà nước mà không qua con đường tòa án.",
        "thumb": "../assets/services/non-litigation-representation.png",
        "items": [
            ("📝", "Đại diện ủy quyền giao dịch dân sự", "Ký kết hợp đồng, thực hiện giao dịch thay mặt ủy quyền"),
            ("🤝", "Thương lượng & hòa giải tranh chấp", "Giải quyết tranh chấp ngoài tòa, tiết kiệm thời gian và chi phí"),
            ("🏛️", "Làm việc với cơ quan nhà nước", "Đại diện tại UBND, Sở ngành, cơ quan thuế, đăng ký..."),
            ("⚖️", "Đại diện công chứng, ký kết", "Thủ tục công chứng, chứng thực, đăng ký giao dịch đảm bảo"),
            ("📋", "Thủ tục hành chính & cấp phép", "Xin giấy phép, đăng ký, gia hạn các loại giấy tờ"),
            ("🏦", "Đại diện tại ngân hàng & tổ chức tín dụng", "Thế chấp, bảo lãnh, giải quyết tranh chấp tín dụng"),
        ],
        "why": [
            "Tiết kiệm thời gian và công sức của khách hàng trong các thủ tục hành chính phức tạp",
            "Đảm bảo đúng quy trình pháp lý, tránh sai sót dẫn đến tranh chấp sau này",
            "Kinh nghiệm làm việc với nhiều cơ quan nhà nước, hiểu rõ quy trình thực tế",
            "Bảo mật thông tin ủy quyền tuyệt đối",
        ],
        "faqs": [
            ("Hợp đồng ủy quyền cần những nội dung gì?",
             "Hợp đồng ủy quyền cần ghi rõ: phạm vi ủy quyền (việc gì, bao nhiêu), thời hạn, quyền và nghĩa vụ mỗi bên, có thể ủy quyền lại không. Nên công chứng để đảm bảo giá trị pháp lý với bên thứ ba."),
            ("Khi nào nên chọn hòa giải thay vì kiện tụng?",
             "Hòa giải phù hợp khi: tranh chấp về tiền bạc dưới mức đáng để kiện tụng, hai bên muốn duy trì quan hệ kinh doanh, cần giải quyết nhanh và bảo mật, hoặc khi hợp đồng có điều khoản hòa giải bắt buộc. Minh Kỳ tư vấn phương án tối ưu cho từng trường hợp."),
            ("Đại diện làm việc với cơ quan nhà nước có hợp pháp không?",
             "Hoàn toàn hợp pháp. Theo pháp luật Việt Nam, cá nhân và tổ chức có quyền ủy quyền cho luật sư hoặc người đại diện hợp pháp thực hiện các thủ tục hành chính, giao dịch với cơ quan nhà nước theo quy định."),
        ]
    },
    {
        "id": "dissolution-bankruptcy",
        "num": "08",
        "title": "Pháp luật về Giải thể & Phá sản Doanh nghiệp",
        "short": "Giải thể & Phá sản DN",
        "desc": "Tư vấn và hỗ trợ toàn bộ quy trình pháp lý giải thể tự nguyện, giải thể bắt buộc, phá sản và bảo vệ quyền lợi các bên.",
        "thumb": "../assets/services/dissolution-bankruptcy.jpg",
        "items": [
            ("📋", "Tư vấn giải thể tự nguyện", "Quy trình, thủ tục, thời hạn và điều kiện giải thể hợp lệ"),
            ("⚖️", "Xử lý giải thể bắt buộc", "Giải thể theo lệnh Tòa án, thu hồi giấy phép, xử lý tài sản"),
            ("🏛️", "Thủ tục nộp đơn yêu cầu phá sản", "Đại diện chủ nợ hoặc DN trong thủ tục phá sản tại Tòa"),
            ("🔄", "Tái cơ cấu nợ & đàm phán chủ nợ", "Phương án phục hồi doanh nghiệp, giãn nợ, xóa nợ"),
            ("💼", "Thanh lý tài sản doanh nghiệp", "Định giá, bán đấu giá, phân chia tài sản theo thứ tự ưu tiên"),
            ("🛡️", "Bảo vệ quyền lợi cổ đông & chủ nợ", "Đảm bảo quyền lợi hợp pháp trong quá trình thanh lý"),
        ],
        "why": [
            "Kinh nghiệm xử lý các vụ giải thể, phá sản phức tạp nhiều chủ nợ",
            "Am hiểu Luật Phá sản 2014 và thực tiễn thi hành tại Tòa án",
            "Bảo vệ tối đa quyền lợi của thân chủ trong quá trình thanh lý",
            "Tư vấn phương án tái cơ cấu trước khi quyết định phá sản",
        ],
        "faqs": [
            ("Khác nhau giữa giải thể và phá sản doanh nghiệp?",
             "Giải thể là chấm dứt hoạt động tự nguyện (hoặc theo quyết định nhà nước) khi doanh nghiệp vẫn có khả năng thanh toán đủ nợ. Phá sản là thủ tục tư pháp khi doanh nghiệp mất khả năng thanh toán. Phá sản phức tạp hơn, cần Tòa án can thiệp."),
            ("Thủ tục phá sản bắt đầu như thế nào?",
             "Có thể do chủ nợ hoặc chính doanh nghiệp nộp đơn tại Tòa án có thẩm quyền. Tòa sẽ xem xét điều kiện, chỉ định Thẩm phán và Quản tài viên, tổ chức Hội nghị chủ nợ và ra quyết định mở thủ tục phá sản nếu đủ điều kiện."),
            ("Giám đốc có thể bị truy cứu trách nhiệm cá nhân khi công ty phá sản không?",
             "Có thể, trong trường hợp: điều hành công ty cố ý trốn tránh trách nhiệm, chuyển tài sản trái phép, làm giả sổ sách. Pháp luật VN có cơ chế 'xuyên qua màn công ty' khi có hành vi gian lận. Minh Kỳ tư vấn để bảo vệ quyền lợi hợp pháp cho nhà quản lý."),
        ]
    },
]

TEMPLATE = '''<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{title} – Minh Kỳ Lawfirm</title>
  <meta name="description" content="{desc}">
  <link rel="icon" type="image/x-icon" href="../assets/favicon/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Cormorant+Garamond:ital,wght@0,400;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div id="preloader"><img src="../assets/logos/badge-enhanced_W256.webp" alt="" class="preloader-badge" width="120" height="120"><div class="preloader-bar"><div class="preloader-progress"></div></div><span class="preloader-text">Minh Kỳ Lawfirm</span></div>
<nav id="navbar" class="solid">
  <div class="container nav-inner">
    <a href="../index.html" class="nav-logo"><img src="../assets/logos/logo-enhanced_W512.webp" alt="Logo" width="48" height="48"><div class="nav-logo-text"><span class="brand">MINH KỲ LAWFIRM</span><span class="tagline">Chính trực · Chuẩn mực · Bền vững</span></div></a>
    <ul class="nav-menu">
      <li><a href="../index.html" class="nav-link">Trang chủ</a></li>
      <li><a href="../introduction.html" class="nav-link">Giới thiệu</a></li>
      <li><a href="../services.html" class="nav-link active">Dịch vụ</a></li>
      <li><a href="../lawyers.html" class="nav-link">Luật sư</a></li>
      <li><a href="../activities.html" class="nav-link">Hoạt động</a></li>
      <li><a href="../videos.html" class="nav-link">Video</a></li>
      <li><a href="../news.html" class="nav-link">Tin tức</a></li>
      <li><a href="../consulting.html" class="nav-link">Tư vấn</a></li>
    </ul>
    <div class="nav-cta"><a href="tel:0964037746" class="nav-phone">0964 037 746</a><a href="../contacts.html" class="btn-nav">Liên hệ ngay</a></div>
    <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false" aria-controls="mobile-menu"><span></span><span></span><span></span></button>
  </div>
</nav>
<div class="mobile-menu" id="mobile-menu">
  <ul><li><a href="../index.html" class="nav-link">Trang chủ</a></li><li><a href="../introduction.html" class="nav-link">Giới thiệu</a></li><li><a href="../services.html" class="nav-link">Dịch vụ</a></li><li><a href="../lawyers.html" class="nav-link">Luật sư</a></li><li><a href="../activities.html" class="nav-link">Hoạt động</a></li><li><a href="../videos.html" class="nav-link">Video</a></li><li><a href="../news.html" class="nav-link">Tin tức</a></li><li><a href="../consulting.html" class="nav-link">Tư vấn</a></li></ul>
  <div class="mobile-menu-bottom"><a href="tel:0964037746" class="btn btn-outline" style="justify-content:center;">📞 0964 037 746</a><a href="../contacts.html" class="btn btn-primary" style="justify-content:center;">Liên hệ ngay</a></div>
</div>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb"><a href="../index.html">Trang chủ</a><span class="breadcrumb-sep">›</span><a href="../services.html">Dịch vụ</a><span class="breadcrumb-sep">›</span><span>{short}</span></nav>
    <span class="section-label">Dịch vụ {num}</span>
    <h1>{title}</h1>
    <p>{desc}</p>
  </div>
</section>

<section class="section section--gray" style="padding-block:var(--space-2xl);">
  <div class="container">
    <div class="slider-wrapper" style="border-radius:var(--radius-md);overflow:hidden;">
      <div class="slider-track">
        <div class="slider-slide">
          <img src="{thumb}" alt="{title}" style="width:100%;height:auto;max-height:520px;object-fit:cover;">
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:900px;">
    <div class="animate-on-scroll">
      <span class="section-label">Tổng quan dịch vụ</span>
      <h2>Dịch vụ bao gồm</h2>
      <div class="gold-line" style="margin:0 0 var(--space-xl) 0;"></div>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:var(--space-md);margin-top:var(--space-xl);">
{items_html}
      </div>
      <style>@media(max-width:640px){{section [style*="grid-template-columns:repeat(2"]{{grid-template-columns:1fr!important;}}}}</style>
    </div>
    <div class="animate-on-scroll" style="margin-top:var(--space-3xl);">
      <h3>Tại sao chọn Minh Kỳ?</h3>
      <ul class="intro-list" style="list-style:disc;">
{why_html}
      </ul>
    </div>
    <div class="animate-on-scroll" style="margin-top:var(--space-2xl);">
      <h3>Quy trình tư vấn</h3>
      <ol class="intro-list" style="list-style:decimal;">
        <li>Tiếp nhận thông tin và phân tích vụ việc cụ thể</li>
        <li>Ký kết hợp đồng dịch vụ pháp lý – minh bạch chi phí</li>
        <li>Phân công chuyên gia phù hợp, triển khai thực hiện</li>
        <li>Cập nhật tiến độ định kỳ và giải đáp thắc mắc</li>
        <li>Bàn giao kết quả và hỗ trợ hậu kỳ nếu cần</li>
      </ol>
    </div>
  </div>
</section>

<section class="section section--offwhite">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <span class="section-label">Câu hỏi thường gặp</span>
      <h2>FAQ</h2>
      <div class="gold-line"></div>
    </div>
    <div class="faq-list animate-on-scroll">
{faqs_html}
    </div>
  </div>
</section>

<section class="section" style="background:var(--color-navy);text-align:center;">
  <div class="container animate-on-scroll">
    <span class="section-label">Bắt đầu ngay</span>
    <h2 style="color:var(--color-white);">Cần tư vấn về {short}?</h2>
    <div class="gold-line"></div>
    <p style="color:rgba(255,255,255,.7);max-width:500px;margin:var(--space-lg) auto;">Liên hệ trực tiếp với ThS LS Lê Viết Kỳ để được tư vấn chi tiết và miễn phí.</p>
    <div style="display:flex;gap:var(--space-md);justify-content:center;flex-wrap:wrap;margin-top:var(--space-xl);">
      <a href="../contacts.html" class="btn btn-primary">Liên hệ tư vấn</a>
      <a href="tel:0964037746" class="btn btn-outline" style="border-color:rgba(200,168,75,.5);color:var(--color-gold);">📞 0964 037 746</a>
    </div>
  </div>
</section>

<footer id="footer"><div class="container"><div class="footer-grid"><div><div class="footer-logo"><img src="../assets/logos/logo-enhanced_W512.webp" alt="Minh Kỳ Lawfirm" width="56" height="56"></div><p class="footer-brand">CÔNG TY LUẬT TNHH MINH KỲ</p><p class="footer-slogan">SHARING · COMMITMENT · RESPONSIBILITY</p><p class="footer-desc">Đối tác pháp lý chiến lược tại TP. Hồ Chí Minh.</p></div><div><p class="footer-heading">Dịch vụ</p><ul class="footer-links"><li><a href="legal-services.html" class="footer-link">Pháp lý Cá nhân & DN</a></li><li><a href="litigation-services.html" class="footer-link">Tố tụng đa lĩnh vực</a></li><li><a href="enterprise-fdi.html" class="footer-link">Pháp lý DN & FDI</a></li><li><a href="invest-ma-ip.html" class="footer-link">M&A & SHTT</a></li><li><a href="labor-export.html" class="footer-link">Lao động & XKLĐ</a></li><li><a href="tax-lifecycle.html" class="footer-link">Thuế & Trọn vòng đời</a></li><li><a href="non-litigation-representation.html" class="footer-link">Đại diện ngoài tố tụng</a></li><li><a href="dissolution-bankruptcy.html" class="footer-link">Giải thể & Phá sản</a></li></ul></div><div><p class="footer-heading">Công ty</p><ul class="footer-links"><li><a href="../introduction.html" class="footer-link">Giới thiệu</a></li><li><a href="../lawyers.html" class="footer-link">Đội ngũ luật sư</a></li><li><a href="../activities.html" class="footer-link">Hoạt động</a></li><li><a href="../news.html" class="footer-link">Tin tức</a></li><li><a href="../consulting.html" class="footer-link">Tư vấn trực tuyến</a></li></ul></div><div><p class="footer-heading">Liên hệ</p><div class="footer-contact-item"><span class="icon">📍</span><span>38 Cộng Hòa, Tân Sơn Nhất, TP.HCM</span></div><div class="footer-contact-item"><span class="icon">📞</span><a href="tel:0964037746">0964 037 746</a></div><div class="footer-contact-item"><span class="icon">📧</span><a href="mailto:levietkylaw@gmail.com">levietkylaw@gmail.com</a></div></div></div><div class="footer-bottom"><p>© 2026 Công ty Luật TNHH Minh Kỳ. MST: 0319365453.</p><p>Thiết kế bởi <a href="#">Lazinet</a></p></div></div></footer>
<div class="floating-contact"><a href="tel:0964037746" class="float-btn phone" title="Gọi điện"><svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg></a><a href="https://zalo.me/0964037746" class="float-btn zalo" target="_blank" rel="noopener">Z</a></div>
<script src="../assets/js/main.js" defer></script>
</body>
</html>'''

def make_items(items):
    rows = []
    for icon, name, detail in items:
        rows.append(f'        <div class="charter-item"><span class="charter-icon">{icon}</span><div><strong>{name}</strong><p style="font-size:0.85rem;color:var(--color-gray-600);margin:4px 0 0;">{detail}</p></div></div>')
    return '\n'.join(rows)

def make_why(why):
    return '\n'.join(f'        <li>{w}</li>' for w in why)

def make_faqs(faqs):
    rows = []
    for q, a in faqs:
        rows.append(f'''      <div class="faq-item">
        <button class="faq-question">{q}<span class="faq-icon">+</span></button>
        <div class="faq-answer">{a}</div>
      </div>''')
    return '\n'.join(rows)

for svc in SERVICES:
    html = TEMPLATE.format(
        title=svc['title'],
        short=svc['short'],
        num=svc['num'],
        desc=svc['desc'],
        thumb=svc['thumb'],
        items_html=make_items(svc['items']),
        why_html=make_why(svc['why']),
        faqs_html=make_faqs(svc['faqs']),
    )
    out = os.path.join(BASE, svc['id'] + '.html')
    with open(out, 'w', encoding='utf-8') as f:
        f.write(html)
    print(f'Created: {out}')

print('Done.')
