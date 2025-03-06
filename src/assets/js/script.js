document.addEventListener('DOMContentLoaded', function() {
    const uploadButton = document.getElementById('upload-button');
    const fileInput = document.getElementById('file-input');
    const progressBar = document.getElementById('progress-bar');
    const messageBox = document.getElementById('message-box');
    const dropArea = document.getElementById('drop-area');
    const form = document.getElementById('file-upload-form');

    uploadButton.addEventListener('click', function() {
        const files = fileInput.files;
        if (files.length === 0) {
            messageBox.textContent = 'Please select a file to upload.';
            return;
        }

        const formData = new FormData();
        formData.append('file', files[0]);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
            }
        });

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    messageBox.textContent = 'File uploaded successfully!';
                } else {
                    messageBox.textContent = 'Error: ' + response.data;
                }
            } else {
                messageBox.textContent = 'Upload failed. Please try again.';
            }
        };

        xhr.send(formData);
    });

    // Prevent default drag behaviors
    ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false)
        document.body.addEventListener(eventName, preventDefaults, false)
    })

    // Highlight drop area when item is dragged over it
    ;['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('highlight'), false)
    })
    ;['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('highlight'), false)
    })

    // Handle dropped files
    dropArea.addEventListener('drop', handleDrop, false)

    function preventDefaults (e) {
        e.preventDefault()
        e.stopPropagation()
    }

    function handleDrop(e) {
        const dt = e.dataTransfer
        const files = dt.files
        handleFiles(files)
    }

    function handleFiles(files) {
        fileInput.files = files
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('File uploaded successfully!');
            } else {
                alert('Upload failed. Please try again.');
            }
        };
        xhr.send(formData);
    });
});