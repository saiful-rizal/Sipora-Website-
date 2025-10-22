function createWaterDrops() {
  const container = document.getElementById('waterDropsContainer');
  const dropCount = window.innerWidth < 768 ? 5 : 8;
  
  for (let i = 0; i < dropCount; i++) {
    const drop = document.createElement('div');
    drop.className = 'water-drop';
    
    const size = Math.random() * 40 + 20;
    drop.style.width = `${size}px`;
    drop.style.height = `${size}px`;
    drop.style.left = `${Math.random() * 100}%`;
    drop.style.top = `${Math.random() * 100}%`;
    drop.style.animationDelay = `${Math.random() * 8}s`;
    drop.style.animationDuration = `${Math.random() * 3 + 5}s`;
    
    container.appendChild(drop);
  }
}

function createRipple(event, element) {
  const ripple = document.createElement('span');
  ripple.className = 'ripple';
  
  const rect = element.getBoundingClientRect();
  const size = Math.max(rect.width, rect.height);
  const x = event.clientX - rect.left - size / 2;
  const y = event.clientY - rect.top - size / 2;
  
  ripple.style.width = ripple.style.height = size + 'px';
  ripple.style.left = x + 'px';
  ripple.style.top = y + 'px';
  
  element.appendChild(ripple);
  
  setTimeout(() => {
    ripple.remove();
  }, 600);
}

function createParticles() {
  const container = document.getElementById('particlesContainer');
  const particleCount = window.innerWidth < 768 ? 8 : 15;
  
  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    
    const size = Math.random() * 60 + 20;
    particle.style.width = `${size}px`;
    particle.style.height = `${size}px`;
    particle.style.left = `${Math.random() * 100}%`;
    particle.style.top = `${Math.random() * 100}%`;
    particle.style.animation = `float ${Math.random() * 10 + 15}s ease-in-out infinite`;
    particle.style.animationDelay = `${Math.random() * 5}s`;
    particle.style.opacity = Math.random() * 0.3 + 0.1;
    
    container.appendChild(particle);
  }
}

function validateUsername() {
  const usernameInput = document.getElementById('usernameInput');
  const usernameWarning = document.getElementById('usernameWarning');
  const username = usernameInput.value.trim();
  
  if (username === '') {
    usernameWarning.classList.add('hidden');
    usernameInput.style.borderColor = '#e2e8f0';
    return true;
  }
  
  if (username.length < 3) {
    usernameWarning.classList.remove('hidden');
    usernameWarning.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Username minimal 3 karakter</span>';
    usernameInput.style.borderColor = '#ef4444';
    return false;
  }
  
  usernameWarning.classList.add('hidden');
  usernameInput.style.borderColor = '#10b981';
  return true;
}

function validateEmail() {
  const emailInput = document.getElementById('emailInput');
  const emailWarning = document.getElementById('emailWarning');
  const email = emailInput.value.trim();
  
  if (email === '') {
    emailWarning.classList.add('hidden');
    emailInput.style.borderColor = '#e2e8f0';
    return true;
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    emailWarning.classList.remove('hidden');
    emailWarning.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Format email tidak valid</span>';
    emailInput.style.borderColor = '#ef4444';
    return false;
  }
  
  const domain = email.split('@')[1];
  if (!domain.endsWith('.ac.id')) {
    emailWarning.classList.remove('hidden');
    emailWarning.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Hanya email dengan domain .ac.id yang diizinkan</span>';
    emailInput.style.borderColor = '#ef4444';
    return false;
  }
  
  emailWarning.classList.add('hidden');
  emailInput.style.borderColor = '#10b981';
  return true;
}

function validateRegisterForm() {
  const usernameInput = document.getElementById('usernameInput');
  const emailInput = document.getElementById('emailInput');
  const passwordInput = document.getElementById('passwordInput');
  const confirmPasswordInput = document.getElementById('confirmPasswordInput');
  const agreeTerms = document.getElementById('agreeTerms');
  
  if (!validateUsername()) {
    usernameInput.focus();
    return false;
  }
  
  if (!validateEmail()) {
    emailInput.focus();
    return false;
  }
  
  if (passwordInput.value.length < 8) {
    alert('Password minimal harus 8 karakter');
    passwordInput.focus();
    return false;
  }
  
  if (passwordInput.value !== confirmPasswordInput.value) {
    alert('Password tidak cocok');
    confirmPasswordInput.focus();
    return false;
  }
  
  if (!agreeTerms.checked) {
    alert('Anda harus menyetujui syarat dan ketentuan');
    agreeTerms.focus();
    return false;
  }
  
  return true;
}

function switchTab(tab) {
  const loginTab = document.getElementById('loginTab');
  const registerTab = document.getElementById('registerTab');
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');

  if (tab === 'login') {
    loginTab.classList.add('tab-active');
    loginTab.classList.remove('text-gray-500');
    registerTab.classList.remove('tab-active');
    registerTab.classList.add('text-gray-500');
    
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
    
    animateFormElements(loginForm);
  } else {
    registerTab.classList.add('tab-active');
    registerTab.classList.remove('text-gray-500');
    loginTab.classList.remove('tab-active');
    loginTab.classList.add('text-gray-500');
    
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
    
    animateFormElements(registerForm);
  }
}

function animateFormElements(form) {
  const elements = form.querySelectorAll('.form-group, button, .divider');
  elements.forEach((el, index) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    setTimeout(() => {
      el.style.transition = 'all 0.3s ease';
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }, index * 50);
  });
}

function animateInput(input) {
  input.parentElement.style.transform = 'scale(1.02)';
  setTimeout(() => {
    input.parentElement.style.transform = 'scale(1)';
  }, 200);
}

function togglePassword(button) {
  const input = button.previousElementSibling;
  const icon = button.querySelector('i');
  
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
    icon.classList.add('animate-pulse');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
    icon.classList.remove('animate-pulse');
  }
}

function socialLogin(provider) {
  const button = event.currentTarget;
  button.classList.add('animate-pulse');
  showSuccessModal(`Mengalihkan ke login ${provider}...`);
  setTimeout(() => {
    button.classList.remove('animate-pulse');
  }, 1000);
}

function showSuccessModal(message) {
  const modal = document.getElementById('successModal');
  const messageEl = document.getElementById('successMessage');
  messageEl.textContent = message;
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeModal() {
  const modal = document.getElementById('successModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}

document.addEventListener('DOMContentLoaded', function() {
  createWaterDrops();
  createParticles();
  
  const card = document.querySelector('.auth-card');
  card.style.opacity = '0';
  card.style.transform = 'scale(0.9) translateY(20px)';
  setTimeout(() => {
    card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
    card.style.opacity = '1';
    card.style.transform = 'scale(1) translateY(0)';
  }, 100);
  
  const socialButtons = document.querySelectorAll('.social-button');
  socialButtons.forEach(btn => {
    btn.addEventListener('mouseenter', function() {
      this.querySelector('i').classList.add('animate-pulse');
    });
    btn.addEventListener('mouseleave', function() {
      this.querySelector('i').classList.remove('animate-pulse');
    });
  });
  
  const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
  inputs.forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement.classList.add('animate-pulse');
    });
    input.addEventListener('blur', function() {
      this.parentElement.classList.remove('animate-pulse');
    });
  });
  
  if (window.innerWidth < 1024) {
    const logo = document.querySelector('.lg:hidden img');
    if (logo) {
      logo.classList.add('animate-float');
    }
  }
  
  window.addEventListener('resize', function() {
    const waterDropsContainer = document.getElementById('waterDropsContainer');
    const particlesContainer = document.getElementById('particlesContainer');
    waterDropsContainer.innerHTML = '';
    particlesContainer.innerHTML = '';
    createWaterDrops();
    createParticles();
  });
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Enter') {
    const activeElement = document.activeElement;
    if (activeElement.tagName === 'INPUT') {
      activeElement.classList.add('animate-pulse');
      setTimeout(() => {
        activeElement.classList.remove('animate-pulse');
      }, 300);
    }
  }
});