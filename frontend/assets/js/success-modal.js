document.addEventListener('DOMContentLoaded', function() {
  const successModal = document.getElementById('successModal');
  const closeSuccessBtn = document.getElementById('closeSuccessBtn');
  const viewDocumentBtn = document.getElementById('viewDocumentBtn');
  const uploadForm = document.getElementById('uploadForm');
  const submitUploadBtn = document.getElementById('submitUploadBtn');
  
  closeSuccessBtn.addEventListener('click', function() {
    successModal.classList.remove('show');
    uploadForm.reset();
    document.getElementById('fileList').innerHTML = '';
  });
  
  successModal.addEventListener('click', function(e) {
    if (e.target === successModal) {
      successModal.classList.remove('show');
      uploadForm.reset();
      document.getElementById('fileList').innerHTML = '';
    }
  });
  
  viewDocumentBtn.addEventListener('click', function() {
    alert('Fitur lihat dokumen akan segera tersedia');
    successModal.classList.remove('show');
  });
  
  if (submitUploadBtn) {
    submitUploadBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      if (!uploadForm.checkValidity()) {
        uploadForm.reportValidity();
        return;
      }
      
      const formData = new FormData(uploadForm);
      formData.append('action', 'upload');
      
      const uploadProgress = document.getElementById('uploadProgress');
      const progressFill = document.getElementById('progressFill');
      const progressPercent = document.getElementById('progressPercent');
      const progressStatus = document.getElementById('progressStatus');
      
      uploadProgress.style.display = 'block';
      progressFill.style.width = '0%';
      progressPercent.textContent = '0%';
      progressStatus.textContent = 'Mengunggah...';
      
      const xhr = new XMLHttpRequest();
      
      xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
          const percentComplete = (e.loaded / e.total) * 100;
          progressFill.style.width = percentComplete + '%';
          progressPercent.textContent = Math.round(percentComplete) + '%';
          
          if (percentComplete < 100) {
            progressStatus.textContent = 'Mengunggah...';
          } else {
            progressStatus.textContent = 'Memproses...';
          }
        }
      });
      
      xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
          try {
            const response = JSON.parse(xhr.responseText);
            
            if (response.success) {
              uploadProgress.style.display = 'none';
              showSuccessModal(response);
            } else {
              alert(response.message || 'Terjadi kesalahan saat mengunggah dokumen.');
              uploadProgress.style.display = 'none';
            }
          } catch (e) {
            console.error('Error parsing response:', e);
            alert('Terjadi kesalahan saat memproses respons server.');
            uploadProgress.style.display = 'none';
          }
        } else {
          alert('Terjadi kesalahan server. Status: ' + xhr.status);
          uploadProgress.style.display = 'none';
        }
      });
      
      xhr.addEventListener('error', function() {
        alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
        uploadProgress.style.display = 'none';
      });
      
      xhr.open('POST', '', true);
      xhr.send(formData);
    });
  }
  
  function showSuccessModal(response) {
    document.getElementById('docTitle').textContent = document.getElementById('documentTitle').value;
    
    const docTypeSelect = document.getElementById('documentType');
    const docTypeText = docTypeSelect.options[docTypeSelect.selectedIndex].text;
    document.getElementById('docType').textContent = docTypeText;
    
    const fileSize = response.file_size || 0;
    let formattedSize;
    if (fileSize < 1024) {
      formattedSize = fileSize + ' B';
    } else if (fileSize < 1024 * 1024) {
      formattedSize = (fileSize / 1024).toFixed(2) + ' KB';
    } else {
      formattedSize = (fileSize / (1024 * 1024)).toFixed(2) + ' MB';
    }
    document.getElementById('docSize').textContent = formattedSize;
    
    document.getElementById('docName').textContent = response.original_name || '-';
    
    successModal.classList.add('show');
    
    setTimeout(function() {
      location.reload();
    }, 5000);
  }
});