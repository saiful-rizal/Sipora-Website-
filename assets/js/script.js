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

window.addEventListener('load', () => {
  setTimeout(() => {
    document.getElementById('loadingScreen').classList.add('hidden');
  }, 2000);
});

document.addEventListener('DOMContentLoaded', () => {
  createWaterDrops();
  
  const menuBtn = document.getElementById('menuBtn');
  const sidebar = document.getElementById('sidebar');
  const sidebarClose = document.getElementById('sidebarClose');
  const sidebarOverlay = document.getElementById('sidebarOverlay');

  menuBtn.addEventListener('click', () => {
    sidebar.classList.add('active');
    sidebarOverlay.classList.add('active');
  });

  sidebarClose.addEventListener('click', () => {
    sidebar.classList.remove('active');
    sidebarOverlay.classList.remove('active');
  });

  sidebarOverlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    sidebarOverlay.classList.remove('active');
  });

  const searchBtn = document.getElementById('searchBtn');
  const searchInput = document.getElementById('searchInput');
  const searchOverlay = document.getElementById('searchOverlay');
  const searchCloseBtn = document.getElementById('searchCloseBtn');
  const searchModalInput = document.getElementById('searchModalInput');

  function openSearch() {
    searchOverlay.style.display = 'flex';
    setTimeout(() => {
      searchOverlay.classList.add('active');
      searchModalInput.focus();
    }, 10);
  }

  function closeSearch() {
    searchOverlay.classList.remove('active');
    setTimeout(() => {
      searchOverlay.style.display = 'none';
    }, 300);
  }

  searchBtn.addEventListener('click', openSearch);
  searchInput.addEventListener('focus', openSearch);
  searchCloseBtn.addEventListener('click', closeSearch);

  searchOverlay.addEventListener('click', (e) => {
    if (e.target === searchOverlay) {
      closeSearch();
    }
  });

  const notificationBtn = document.getElementById('notificationBtn');
  const notificationPanel = document.getElementById('notificationPanel');

  notificationBtn.addEventListener('click', () => {
    notificationPanel.classList.toggle('active');
    const badge = notificationBtn.querySelector('.notification-badge');
    if (badge) {
      badge.style.display = 'none';
    }
  });

  document.addEventListener('click', (e) => {
    if (!notificationPanel.contains(e.target) && !notificationBtn.contains(e.target)) {
      notificationPanel.classList.remove('active');
    }
  });

  const profileBtn = document.getElementById('profileBtn');
  const profileDropdown = document.getElementById('profileDropdown');

  profileBtn.addEventListener('click', () => {
    profileDropdown.classList.toggle('active');
  });

  document.addEventListener('click', (e) => {
    if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
      profileDropdown.classList.remove('active');
    }
  });

  const slides = document.querySelectorAll('.banner-slide');
  const dots = document.querySelectorAll('.banner-dot');
  let currentSlide = 0;

  function showSlide(index) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    slides[index].classList.add('active');
    dots[index].classList.add('active');
  }

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      currentSlide = index;
      showSlide(currentSlide);
    });
  });

  setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }, 5000);

  const viewBtns = document.querySelectorAll('.view-btn');
  viewBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      viewBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  const navItems = document.querySelectorAll('.nav-item');
  navItems.forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      navItems.forEach(nav => nav.classList.remove('active'));
      item.classList.add('active');
    });
  });

  const menuItems = document.querySelectorAll('.menu-item');
  menuItems.forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      menuItems.forEach(menu => menu.classList.remove('active'));
      item.classList.add('active');
    });
  });

  const fab = document.getElementById('fab');
  const quickAddMenu = document.getElementById('quickAddMenu');

  fab.addEventListener('click', () => {
    quickAddMenu.classList.toggle('active');
    fab.classList.toggle('active');
  });

  const quickAddItems = document.querySelectorAll('.quick-add-item');
  quickAddItems.forEach(item => {
    item.addEventListener('click', () => {
      const text = item.querySelector('.quick-add-text');
      console.log(`Quick add: ${text.textContent}`);
      quickAddMenu.classList.remove('active');
      fab.classList.remove('active');
    });
  });

  document.addEventListener('click', (e) => {
    if (!quickAddMenu.contains(e.target) && !fab.contains(e.target)) {
      quickAddMenu.classList.remove('active');
      fab.classList.remove('active');
    }
  });

  const cards = document.querySelectorAll('.card');
  cards.forEach((card, index) => {
    card.addEventListener('click', (e) => {
      console.log('Card clicked');
    });
  });

  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach(card => {
    card.addEventListener('mouseenter', () => {
      card.style.transform = 'translateY(-5px) scale(1.05)';
    });
    
    card.addEventListener('mouseleave', () => {
      card.style.transform = 'translateY(0) scale(1)';
    });
  });

  const listItems = document.querySelectorAll('.list-item');
  listItems.forEach(item => {
    item.addEventListener('mouseenter', () => {
      const icon = item.querySelector('.list-icon');
      icon.style.transform = 'scale(1.1) rotate(5deg)';
    });
    
    item.addEventListener('mouseleave', () => {
      const icon = item.querySelector('.list-icon');
      icon.style.transform = 'scale(1) rotate(0deg)';
    });
  });

  window.addEventListener('resize', function() {
    const container = document.getElementById('waterDropsContainer');
    container.innerHTML = '';
    createWaterDrops();
  });

  const uploadBtn1 = document.getElementById('uploadBtn1');
  const uploadBtn2 = document.getElementById('uploadBtn2');
  const uploadBtn3 = document.getElementById('uploadBtn3');
  const uploadBtn4 = document.getElementById('uploadBtn4');
  const quickUploadBtn = document.getElementById('quickUploadBtn');
  const quickUploadItem = document.getElementById('quickUploadItem');
  const uploadModal = document.getElementById('uploadModal');
  const uploadModalClose = document.getElementById('uploadModalClose');
  const cancelUploadBtn = document.getElementById('cancelUploadBtn');
  const submitUploadBtn = document.getElementById('submitUploadBtn');
  const uploadArea = document.getElementById('uploadArea');
  const fileInput = document.getElementById('fileInput');
  const fileList = document.getElementById('fileList');
  const uploadProgress = document.getElementById('uploadProgress');
  const progressFill = document.getElementById('progressFill');
  const progressPercent = document.getElementById('progressPercent');
  const progressStatus = document.getElementById('progressStatus');
  
  let uploadedFiles = [];

  function openUploadModal() {
    uploadModal.classList.add('active');
  }

  function closeUploadModal() {
    uploadModal.classList.remove('active');
    document.getElementById('uploadForm').reset();
    fileList.innerHTML = '';
    uploadedFiles = [];
    uploadProgress.style.display = 'none';
    progressFill.style.width = '0%';
    progressPercent.textContent = '0%';
  }

  uploadBtn1.addEventListener('click', openUploadModal);
  uploadBtn2.addEventListener('click', openUploadModal);
  uploadBtn3.addEventListener('click', openUploadModal);
  uploadBtn4.addEventListener('click', openUploadModal);
  quickUploadBtn.addEventListener('click', openUploadModal);
  quickUploadItem.addEventListener('click', openUploadModal);
  uploadModalClose.addEventListener('click', closeUploadModal);
  cancelUploadBtn.addEventListener('click', closeUploadModal);

  uploadArea.addEventListener('click', () => {
    fileInput.click();
  });

  uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
  });

  uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
  });

  uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const files = Array.from(e.dataTransfer.files);
    handleFiles(files);
  });

  fileInput.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    handleFiles(files);
  });

  function handleFiles(files) {
    files.forEach(file => {
      if (file.size > 10 * 1024 * 1024) {
        alert(`File "${file.name}" terlalu besar. Maksimal ukuran file adalah 10MB.`);
        return;
      }
      
      const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
      if (!allowedTypes.includes(file.type)) {
        alert(`File "${file.name}" tidak didukung. Silakan unggah file PDF, DOC, DOCX, JPG, atau PNG.`);
        return;
      }
      
      if (uploadedFiles.some(f => f.name === file.name)) {
        alert(`File "${file.name}" sudah ditambahkan.`);
        return;
      }
      
      uploadedFiles.push(file);
      displayFile(file);
    });
  }

  function displayFile(file) {
    const fileItem = document.createElement('div');
    fileItem.className = 'file-item';
    
    let fileIcon = 'bi-file-earmark';
    if (file.type === 'application/pdf') {
      fileIcon = 'bi-file-earmark-pdf';
    } else if (file.type.includes('word')) {
      fileIcon = 'bi-file-earmark-word';
    } else if (file.type.includes('image')) {
      fileIcon = 'bi-file-earmark-image';
    }
    
    fileItem.innerHTML = `
      <div class="file-icon">
        <i class="bi ${fileIcon}"></i>
      </div>
      <div class="file-info">
        <div class="file-name">${file.name}</div>
        <div class="file-size">${formatFileSize(file.size)}</div>
      </div>
      <button class="file-remove" data-filename="${file.name}">
        <i class="bi bi-x"></i>
      </button>
    `;
    
    fileList.appendChild(fileItem);
    
    const removeBtn = fileItem.querySelector('.file-remove');
    removeBtn.addEventListener('click', () => {
      const filename = removeBtn.dataset.filename;
      uploadedFiles = uploadedFiles.filter(f => f.name !== filename);
      fileItem.remove();
    });
  }

  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  submitUploadBtn.addEventListener('click', () => {
    const form = document.getElementById('uploadForm');
    const formData = new FormData(form);
    formData.append('action', 'upload');
    
    uploadedFiles.forEach(file => {
      formData.append('files[]', file);
    });

    uploadProgress.style.display = 'block';
    
    fetch(window.location.href, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        closeUploadModal();
        location.reload();
      } else {
        alert(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat mengunggah file.');
    })
    .finally(() => {
      uploadProgress.style.display = 'none';
    });
  });
});