document.addEventListener('DOMContentLoaded', function() {
    // Theme Toggle
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
    }
    
    themeToggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        themeToggleBtn.innerHTML = theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    });
    
    // Copy to Clipboard
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const content = document.getElementById(targetId).textContent;
            
            navigator.clipboard.writeText(content).then(() => {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                }, 2000);
            });
        });
    });
    
    // Drag and Drop
    const dropArea = document.getElementById('drop-area');
    const textarea = document.getElementById('input-text');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        dropArea.classList.add('active');
    }
    
    function unhighlight() {
        dropArea.classList.remove('active');
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length) {
            const file = files[0];
            if (file.type.startsWith('text/') || file.type === 'application/pdf') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    textarea.value = e.target.result;
                };
                reader.readAsText(file);
            } else {
                alert('Please upload a text or PDF file');
            }
        }
    }
    
    // Image Modal
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('modal-image');
    const modalCaption = document.querySelector('.modal-caption');
    const closeBtn = document.querySelector('.close');
    
    document.querySelectorAll('.thumbnail').forEach(img => {
        img.addEventListener('click', function() {
            modal.style.display = 'block';
            modalImg.src = this.src;
            modalCaption.textContent = this.alt || 'Image preview';
        });
    });
    
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Preview All for Images/Videos
    document.querySelectorAll('.preview-all').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            const items = Array.from(document.querySelectorAll(`#${type} .thumbnail`));
            
            if (items.length > 0) {
                let currentIndex = 0;
                
                function showItem(index) {
                    if (index >= 0 && index < items.length) {
                        modalImg.src = items[index].src;
                        modalCaption.textContent = `${type} ${index + 1} of ${items.length}`;
                        currentIndex = index;
                    }
                }
                
                showItem(0);
                modal.style.display = 'block';
                
                // Navigation
                modalImg.onclick = function(e) {
                    e.stopPropagation();
                    showItem((currentIndex + 1) % items.length);
                };
                
                // Keyboard navigation
                document.addEventListener('keydown', function handleKeyNav(e) {
                    if (modal.style.display === 'block') {
                        if (e.key === 'ArrowRight') {
                            showItem((currentIndex + 1) % items.length);
                        } else if (e.key === 'ArrowLeft') {
                            showItem((currentIndex - 1 + items.length) % items.length);
                        } else if (e.key === 'Escape') {
                            modal.style.display = 'none';
                            document.removeEventListener('keydown', handleKeyNav);
                        }
                    }
                });
            }
        });
    });
});