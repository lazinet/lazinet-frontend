// Modern LAZINET App - Optimized for Performance
class LazinetApp {
  constructor() {
    this.init();
  }

  async init() {
    this.setupEventListeners();
    this.setupIntersectionObserver();
    await this.loadContent();
    this.setupAnimatedBackground();
    this.hideLoadingScreen();
  }

  setupEventListeners() {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navMenu = document.getElementById('navMenu');
    
    mobileMenuBtn?.addEventListener('click', () => {
      mobileMenuBtn.classList.toggle('active');
      navMenu.classList.toggle('active');
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const target = document.querySelector(link.getAttribute('href'));
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          
          // Close mobile menu if open
          if (navMenu.classList.contains('active')) {
            mobileMenuBtn.classList.remove('active');
            navMenu.classList.remove('active');
          }
        }
      });
    });

    // Header scroll effect
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
      const header = document.getElementById('header');
      const currentScroll = window.pageYOffset;
      
      if (currentScroll > 100) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }

      // Update active navigation based on scroll position
      this.updateActiveNavigation();
      
      lastScroll = currentScroll;
    });
  }

  setupIntersectionObserver() {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animated');
            // Lazy load images
            const images = entry.target.querySelectorAll('img[data-src]');
            images.forEach(img => {
              img.src = img.dataset.src;
              img.classList.add('loaded');
              img.removeAttribute('data-src');
            });
          }
        });
      },
      { threshold: 0.1 }
    );

    // Observe all sections and animated elements
    document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
    document.querySelectorAll('section').forEach(el => observer.observe(el));
  }

  updateActiveNavigation() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-menu a[href^="#"]');
    
    let current = '';
    sections.forEach(section => {
      const sectionTop = section.offsetTop - 150;
      const sectionHeight = section.offsetHeight;
      if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
        current = section.getAttribute('id');
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${current}`) {
        link.classList.add('active');
      }
    });
  }

  async loadContent() {
    // Load content dynamically for better initial page load performance
    const contentSections = {
      quickServicesContent: this.getQuickServicesHTML(),
      aboutContent: this.getAboutHTML(),
      servicesContent: this.getServicesHTML(),
      productsContent: this.getProductsHTML(),
      portfolioContent: this.getPortfolioHTML(),
      teamContent: this.getTeamHTML(),
      newsContent: this.getNewsHTML(),
      contactContent: this.getContactHTML()
    };

    // Load sections with a small delay for better UX
    Object.entries(contentSections).forEach(([id, content], index) => {
      setTimeout(() => {
        const element = document.getElementById(id);
        if (element) {
          element.innerHTML = content;
          element.classList.add('animate-on-scroll');
        }
      }, index * 100);
    });
  }

  getQuickServicesHTML() {
    return `
      <div class="section-title">
        <h2>Why Choose <span style="color: var(--primary-blue);">LAZINET</span></h2>
      </div>
      <div class="card-grid cols-4">
        <div class="card service-card">
          <div class="service-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="var(--primary-blue)">
              <path d="M12 2L2 7L12 12L22 7L12 2Z"/>
              <path d="M2 17L12 22L22 17"/>
              <path d="M2 12L12 17L22 12"/>
            </svg>
          </div>
          <h3>Customization</h3>
          <p>Tailored solutions designed specifically for your business needs and objectives.</p>
        </div>
        <div class="card service-card">
          <div class="service-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="var(--primary-orange)">
              <circle cx="12" cy="12" r="3"/>
              <path d="M19.4 15A1.65 1.65 0 0022 13.35A1.65 1.65 0 0020.35 12A1.65 1.65 0 0022 10.65A1.65 1.65 0 0019.4 9A1.65 1.65 0 0017.75 7.4A1.65 1.65 0 0016.1 9A1.65 1.65 0 0014.45 7.4A1.65 1.65 0 0012.8 9A1.65 1.65 0 0011.15 7.4A1.65 1.65 0 009.5 9A1.65 1.65 0 007.85 7.4A1.65 1.65 0 006.2 9A1.65 1.65 0 004.55 7.4A1.65 1.65 0 002.9 9A1.65 1.65 0 001.25 7.4"/>
            </svg>
          </div>
          <h3>Innovation</h3>
          <p>Cutting-edge technology solutions that drive efficiency and competitive advantage.</p>
        </div>
        <div class="card service-card">
          <div class="service-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="var(--primary-blue)">
              <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z"/>
            </svg>
          </div>
          <h3>Efficiency</h3>
          <p>Optimized workflows and processes that maximize productivity and reduce costs.</p>
        </div>
        <div class="card service-card">
          <div class="service-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="var(--primary-orange)">
              <path d="M12 2V22M2 12H22M17.5 6.5L6.5 17.5M17.5 17.5L6.5 6.5"/>
            </svg>
          </div>
          <h3>Sustainability</h3>
          <p>Environmentally conscious solutions that support long-term business growth.</p>
        </div>
      </div>
    `;
  }

  getAboutHTML() {
    return `
      <div class="section-title">
        <h2>About <span style="color: var(--primary-orange);">LAZINET</span></h2>
        <p>Driving Innovation and Practical Solutions</p>
      </div>
      <div class="about-content">
        <div class="about-text">
          <h3>Technologies for Efficiency</h3>
          <p>At LAZINET, we harness advanced technologies to drive business efficiency and promote sustainable growth. Our commitment to innovation allows us to stay ahead in the rapidly evolving technological landscape.</p>
          <ul class="about-features">
            <li>
              <div class="icon">🤖</div>
              <div>
                <h4>AI & Analytics</h4>
                <p>Advanced artificial intelligence and machine learning solutions for data-driven insights.</p>
              </div>
            </li>
            <li>
              <div class="icon">🌐</div>
              <div>
                <h4>IoT Integration</h4>
                <p>Connected devices and smart systems for automated operations and monitoring.</p>
              </div>
            </li>
            <li>
              <div class="icon">☁️</div>
              <div>
                <h4>Cloud Solutions</h4>
                <p>Scalable cloud infrastructure and services for modern business needs.</p>
              </div>
            </li>
          </ul>
        </div>
        <div class="about-image">
          <img data-src="./assets/img/background/SMT.webp" alt="LAZINET Technology" class="lazy-load">
        </div>
      </div>
      
      <div class="stats-grid">
        <div class="stat-item">
          <span class="stat-number" data-target="8">0</span>
          <div class="stat-label">Happy Clients</div>
        </div>
        <div class="stat-item">
          <span class="stat-number" data-target="12">0</span>
          <div class="stat-label">Projects Completed</div>
        </div>
        <div class="stat-item">
          <span class="stat-number" data-target="19">0</span>
          <div class="stat-label">Systems Deployed</div>
        </div>
        <div class="stat-item">
          <span class="stat-number" data-target="168">0</span>
          <div class="stat-label">Products Delivered</div>
        </div>
      </div>
    `;
  }

  getServicesHTML() {
    const services = [
      {
        title: "AI & Analytics",
        description: "Comprehensive AI services from data preparation to model deployment, helping businesses unlock insights and enhance decision-making.",
        icon: "🤖"
      },
      {
        title: "Internet of Things",
        description: "End-to-end IoT solutions from hardware development to system integration for intelligent operations.",
        icon: "🌐"
      },
      {
        title: "Software Development",
        description: "Custom software solutions for web, mobile, and desktop platforms with cutting-edge technologies.",
        icon: "💻"
      },
      {
        title: "Cloud & Networking",
        description: "Cloud migration, network management, and infrastructure optimization for seamless connectivity.",
        icon: "☁️"
      },
      {
        title: "Smart Solutions",
        description: "Innovative smart solutions integrating AI, IoT, and advanced technologies for enhanced efficiency.",
        icon: "🏙️"
      },
      {
        title: "Digital Transformation",
        description: "Complete digital transformation services including process automation and technology integration.",
        icon: "🔄"
      }
    ];

    return `
      <div class="section-title">
        <h2>Our <span style="color: var(--primary-blue);">Services</span></h2>
        <p>Comprehensive technology solutions for your business needs</p>
      </div>
      <div class="card-grid cols-3">
        ${services.map(service => `
          <div class="card service-card">
            <div class="service-icon">
              <span style="font-size: 2rem;">${service.icon}</span>
            </div>
            <h3>${service.title}</h3>
            <p>${service.description}</p>
          </div>
        `).join('')}
      </div>
    `;
  }

  getProductsHTML() {
    return `
      <div class="section-title">
        <h2>Our <span style="color: var(--primary-orange);">Products</span></h2>
        <p>Innovative solutions across multiple industries</p>
      </div>
      <div class="card-grid cols-2">
        <div class="card portfolio-card">
          <div class="portfolio-image">
            <img data-src="./assets/img/portfolio/Agritech/LAZINET-Smart-Agriculture.webp" alt="Smart Agriculture" class="lazy-load">
          </div>
          <div class="portfolio-overlay">
            <h4>Smart Agriculture Systems</h4>
            <p>Advanced IoT and AI solutions for modern farming operations</p>
            <div class="portfolio-links">
              <a href="#contact">📧</a>
              <a href="#contact">🔗</a>
            </div>
          </div>
        </div>
        <div class="card portfolio-card">
          <div class="portfolio-image">
            <img data-src="./assets/img/portfolio/WDM/uNMS.webp" alt="Network Management" class="lazy-load">
          </div>
          <div class="portfolio-overlay">
            <h4>Network Management System</h4>
            <p>Comprehensive network monitoring and management platform</p>
            <div class="portfolio-links">
              <a href="#contact">📧</a>
              <a href="#contact">🔗</a>
            </div>
          </div>
        </div>
        <div class="card portfolio-card">
          <div class="portfolio-image">
            <img data-src="./assets/img/portfolio/Software/eCommerce1.webp" alt="E-Commerce Platform" class="lazy-load">
          </div>
          <div class="portfolio-overlay">
            <h4>E-Commerce Solutions</h4>
            <p>Complete e-commerce platform with advanced features</p>
            <div class="portfolio-links">
              <a href="https://hathyo.com" target="_blank">🔗</a>
              <a href="#contact">📧</a>
            </div>
          </div>
        </div>
        <div class="card portfolio-card">
          <div class="portfolio-image">
            <img data-src="./assets/img/portfolio/WDM/5G-Fronthaul-Semi-Active-WDM-Solution.webp" alt="5G Solutions" class="lazy-load">
          </div>
          <div class="portfolio-overlay">
            <h4>5G Fronthaul Solutions</h4>
            <p>Advanced 5G fronthaul semi-active WDM technology</p>
            <div class="portfolio-links">
              <a href="#contact">📧</a>
              <a href="#contact">🔗</a>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  getPortfolioHTML() {
    return `
      <div class="section-title">
        <h2>Our <span style="color: var(--primary-blue);">Portfolio</span></h2>
        <p>Showcasing our latest projects and achievements</p>
      </div>
      <div class="portfolio-filters">
        <button class="filter-btn active" data-filter="all">All Projects</button>
        <button class="filter-btn" data-filter="ai">AI Solutions</button>
        <button class="filter-btn" data-filter="iot">IoT Systems</button>
        <button class="filter-btn" data-filter="software">Software</button>
      </div>
      <div class="card-grid cols-3" id="portfolioGrid">
        <!-- Portfolio items will be loaded here -->
      </div>
    `;
  }

  getTeamHTML() {
    const team = [
      {
        name: "HOÀNG MINH PHỤNG",
        role: "Founder & CEO | Innovation Principal",
        image: "./assets/img/team/Phung-Hoang.webp",
        social: {
          linkedin: "https://linkedin.com/in/phunghm/",
          facebook: "https://www.facebook.com/profile.php?id=61567050494124"
        }
      },
      {
        name: "Dr. Phùng Thế Bảo",
        role: "CTO | Data Science & AI Researcher",
        image: "./assets/img/team/Bao-Phung-RB1.png",
        social: {
          linkedin: "https://linkedin.com/company/lazinet",
          facebook: "https://www.facebook.com/profile.php?id=61567050494124"
        }
      },
      {
        name: "Ánh Văn",
        role: "Operation Manager - HATHYO Project",
        image: "./assets/img/team/Anh-Van-RB2.png",
        social: {
          facebook: "https://www.facebook.com/vanthingocanhs/"
        }
      },
      {
        name: "Trọng Nguyễn",
        role: "Technology Manager - HATHYO Project",
        image: "./assets/img/team/Trong-Nguyen-RB1.png",
        social: {
          linkedin: "https://linkedin.com/company/lazinet"
        }
      }
    ];

    return `
      <div class="section-title">
        <h2>Our <span style="color: var(--primary-orange);">Team</span></h2>
        <p>Meet the experts behind LAZINET's innovative solutions</p>
      </div>
      <div class="card-grid cols-4">
        ${team.map(member => `
          <div class="card team-card">
            <div class="team-avatar">
              <img data-src="${member.image}" alt="${member.name}" class="lazy-load">
              <div class="team-social">
                ${member.social.linkedin ? `<a href="${member.social.linkedin}" target="_blank">🔗</a>` : ''}
                ${member.social.facebook ? `<a href="${member.social.facebook}" target="_blank">📘</a>` : ''}
              </div>
            </div>
            <h4>${member.name}</h4>
            <div class="role">${member.role}</div>
          </div>
        `).join('')}
      </div>
    `;
  }

  getNewsHTML() {
    const news = [
      {
        title: "AI Technological Ecosystem & Future Development",
        date: "March 25, 2025",
        location: "IT Faculty, HUIT",
        image: "./assets/img/Events/processed_HUIT-AI.jpeg",
        excerpt: "LAZINET showcased cutting-edge AI technologies, including NLP-powered chatbots, intelligent AI agents, and advanced AI robotics.",
        link: "https://huit.edu.vn/"
      },
      {
        title: "Transforming Agriculture with AI and IoT Innovations",
        date: "December 05, 2024",
        location: "SECC HCMC Computer Association",
        image: "./assets/img/Events/processed_OK_AIoT.jpeg",
        excerpt: "LAZINET unveiled groundbreaking applications of IoT and AI, revolutionizing modern agriculture with smart, sustainable solutions.",
        link: "https://youtu.be/AR6xtkN9ntY"
      },
      {
        title: "Partnering with HUIT for Research Development",
        date: "November 29, 2024",
        location: "HUIT University",
        image: "./assets/img/Events/processed_HUIT-MOU.jpeg",
        excerpt: "LAZINET presented collaboration framework focusing on research and technology deployment while offering internship opportunities.",
        link: "https://www.facebook.com/story.php?story_fbid=pfbid02JYen9bnFjsw5u4iWynddNHz43u4RjcFLsNJD1U1pcPHGQpdpF8xszsXXESyzBMYal&id=100064505750401"
      }
    ];

    return `
      <div class="container">
        <div class="section-title">
          <h2 style="color: white;">Latest <span style="color: var(--primary-orange);">News & Events</span></h2>
          <p style="color: rgba(255, 255, 255, 0.8);">Stay updated with our latest achievements and innovations</p>
        </div>
        <div class="card-grid cols-3">
          ${news.map(item => `
            <div class="news-card">
              <div class="news-image">
                <img data-src="${item.image}" alt="${item.title}" class="lazy-load">
              </div>
              <div class="news-content">
                <div class="news-date">${item.date} @ ${item.location}</div>
                <h4 class="news-title">${item.title}</h4>
                <p class="news-excerpt">${item.excerpt}</p>
                <a href="${item.link}" target="_blank" class="news-link">Read More →</a>
              </div>
            </div>
          `).join('')}
        </div>
      </div>
    `;
  }

  getContactHTML() {
    return `
      <div class="section-title">
        <h2>Get In <span style="color: var(--primary-blue);">Touch</span></h2>
        <p>Ready to transform your business? Let's discuss your project</p>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start;">
        <div class="contact-form">
          <h3 style="margin-bottom: 2rem; color: var(--dark-gray);">Send us a message</h3>
          <form id="contactForm" action="https://script.google.com/macros/s/AKfycbxvD2O3k7XdP8xKGn_e_s5mGPBMy7bC9bR8Eg/exec" method="POST">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone">
            </div>
            <div class="form-group">
              <label for="subject">Subject *</label>
              <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
              <label for="message">Message *</label>
              <textarea id="message" name="message" required placeholder="Tell us about your project or requirements..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
          </form>
        </div>
        
        <div>
          <div class="card">
            <h3 style="margin-bottom: 2rem; color: var(--dark-gray);">Contact Information</h3>
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
              <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.5rem;">📧</span>
                <div>
                  <strong>Email</strong><br>
                  <a href="mailto:email@lazinet.com" style="color: var(--primary-blue);">email@lazinet.com</a>
                </div>
              </div>
              <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.5rem;">📞</span>
                <div>
                  <strong>Phone</strong><br>
                  <a href="tel:+84908556220" style="color: var(--primary-blue);">+84-908556220</a>
                </div>
              </div>
              <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.5rem;">💬</span>
                <div>
                  <strong>Zalo</strong><br>
                  <a href="http://zalo.me/0908556220" target="_blank" style="color: var(--primary-blue);">+84-908556220</a>
                </div>
              </div>
              <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.5rem;">🌐</span>
                <div>
                  <strong>Social Media</strong><br>
                  <a href="https://www.facebook.com/profile.php?id=61567050494124" target="_blank" style="color: var(--primary-blue); margin-right: 1rem;">Facebook</a>
                  <a href="https://linkedin.com/company/lazinet" target="_blank" style="color: var(--primary-blue);">LinkedIn</a>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card" style="margin-top: 2rem;">
            <h4 style="color: var(--dark-gray); margin-bottom: 1rem;">Why Choose Us?</h4>
            <ul style="list-style: none; padding: 0;">
              <li style="margin-bottom: 0.5rem;">✅ Proven track record with 8+ happy clients</li>
              <li style="margin-bottom: 0.5rem;">✅ 12+ successful projects completed</li>
              <li style="margin-bottom: 0.5rem;">✅ 19+ systems deployed and running</li>
              <li style="margin-bottom: 0.5rem;">✅ Expert team with deep technical knowledge</li>
              <li>✅ Commitment to innovation and quality</li>
            </ul>
          </div>
        </div>
      </div>
    `;
  }

  setupAnimatedBackground() {
    const canvas = document.getElementById('techGrid');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const particles = [];
    const particleCount = 50;

    // Create particles
    for (let i = 0; i < particleCount; i++) {
      particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        size: Math.random() * 3 + 1,
        speedX: (Math.random() - 0.5) * 0.5,
        speedY: (Math.random() - 0.5) * 0.5,
        opacity: Math.random() * 0.5 + 0.2
      });
    }

    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      // Draw connections
      particles.forEach((particle, i) => {
        particles.slice(i + 1).forEach(otherParticle => {
          const dx = particle.x - otherParticle.x;
          const dy = particle.y - otherParticle.y;
          const distance = Math.sqrt(dx * dx + dy * dy);
          
          if (distance < 100) {
            ctx.strokeStyle = `rgba(255, 99, 0, ${0.1 - distance / 1000})`;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(particle.x, particle.y);
            ctx.lineTo(otherParticle.x, otherParticle.y);
            ctx.stroke();
          }
        });
      });

      // Update and draw particles
      particles.forEach(particle => {
        particle.x += particle.speedX;
        particle.y += particle.speedY;

        if (particle.x < 0 || particle.x > canvas.width) particle.speedX *= -1;
        if (particle.y < 0 || particle.y > canvas.height) particle.speedY *= -1;

        ctx.fillStyle = `rgba(0, 0, 255, ${particle.opacity})`;
        ctx.beginPath();
        ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
        ctx.fill();
      });

      requestAnimationFrame(animate);
    }

    animate();

    // Handle resize
    window.addEventListener('resize', () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    });
  }

  hideLoadingScreen() {
    setTimeout(() => {
      const loadingScreen = document.getElementById('loadingScreen');
      if (loadingScreen) {
        loadingScreen.classList.add('hidden');
        setTimeout(() => loadingScreen.remove(), 500);
      }
      
      // Animate stats counters
      this.animateCounters();
    }, 1000);
  }

  animateCounters() {
    const counters = document.querySelectorAll('.stat-number[data-target]');
    counters.forEach(counter => {
      const target = parseInt(counter.dataset.target);
      const increment = target / 100;
      let current = 0;
      
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        counter.textContent = Math.floor(current);
      }, 20);
    });
  }
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new LazinetApp();
});

// Performance monitoring
if ('performance' in window) {
  window.addEventListener('load', () => {
    const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
    console.log(`Page loaded in ${loadTime}ms`);
  });
}