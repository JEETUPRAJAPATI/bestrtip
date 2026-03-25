$(document).ready(function () {
    $('.testimonials-card').slick({
        centerMode: true,
        centerPadding: '30px',
        slidesToShow: 3,
        autoplay: true,
        autoplaySpeed: 3500,
        dots: true,
        arrows: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    centerPadding: '50px',
                }
            }
        ]
    });
});

// image upload code
const uploadInput = document.getElementById("uploadProfilePic");
const profileImage = document.getElementById("profileImage");
const modalProfileImage = document.getElementById("modalProfileImage");
uploadInput.addEventListener("change", function () {
    const file = this.files[0];
    if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function (e) {
            profileImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
function viewImage() {
    modalProfileImage.src = profileImage.src;
    const modal = new bootstrap.Modal(document.getElementById("viewProfilePicModal"));
    modal.show();
}
function removeProfileImage() {
    profileImage.src = "https://cdn-icons-png.flaticon.com/512/847/847969.png";
    uploadInput.value = "";
}

// car exterior interior image upload code
const imageInput = document.getElementById('imageInput');
    const previewContainer = document.getElementById('imagePreview');
    const modalCarImage = document.getElementById('modalCarImage');
    imageInput.addEventListener('change', handleFiles);
    function handleFiles() {
        const selectedFiles = Array.from(imageInput.files);
        const currentCount = previewContainer.querySelectorAll('img').length;
        const remainingSlots = 5 - currentCount;
        if (remainingSlots <= 0) {
            alert("You can upload a maximum of 5 images.");
            imageInput.value = '';
            return;
        }
        const filesToAdd = selectedFiles.slice(0, remainingSlots);
        filesToAdd.forEach(file => {
            if (file.size > 1024 * 1024) {
                alert(`"${file.name}" exceeds 1MB size limit!`);
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                const imageBox = document.createElement('div');
                imageBox.classList.add('position-relative');
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = "rounded";
                img.style = "width: 80px; height: 80px; object-fit: cover; border: 2px solid #eee; cursor: pointer;";
                img.onclick = () => showLargePreview(e.target.result);
                const delBtn = document.createElement('button');
                delBtn.type = "button";
                delBtn.className = "btn bg-purple-200 position-absolute top-0 end-0";
                delBtn.innerHTML = "&times;";
                delBtn.onclick = () => {
                    imageBox.remove();
                };
                imageBox.appendChild(img);
                imageBox.appendChild(delBtn);
                previewContainer.appendChild(imageBox);
            };
            reader.readAsDataURL(file);
        });
        imageInput.value = '';
    }
    function showLargePreview(url) {
        modalCarImage.src = url;
        const modal = new bootstrap.Modal(document.getElementById('carImageModal'));
        modal.show();
    }

    //license card upload

    
    function handlePreview(inputId, previewId, placeholderId, eyeButtonId, errorId) {
      const input = document.getElementById(inputId);
      const preview = document.getElementById(previewId);
      const placeholder = document.getElementById(placeholderId);
      const eyeButton = document.getElementById(eyeButtonId);
      const error = document.getElementById(errorId);
      input.addEventListener('change', function () {
          const file = this.files[0];
          if (file) {
              if (file.size > 1 * 1024 * 1024) {
                  error.style.display = 'block';
                  preview.style.display = 'none';
                  placeholder.style.display = 'block';
                  eyeButton.style.display = 'none';
                  input.value = '';
              } else {
                  error.style.display = 'none';
                  const imgUrl = URL.createObjectURL(file);
                  preview.src = imgUrl;
                  preview.style.display = 'block';
                  placeholder.style.display = 'none';
                  eyeButton.dataset.imgUrl = imgUrl;
                  eyeButton.style.display = 'flex';
              }
          }
      });
  } 

  handlePreview('licenseFrontInput', 'licenseFrontPreview', 'licenseFrontPlaceholder', 'licenseFrontLink', 'licenseFrontError');
  handlePreview('licenseBackInput', 'licenseBackPreview', 'licenseBackPlaceholder', 'licenseBackLink', 'licenseBackError');
  handlePreview('rcInput', 'rcPreview', 'rcPlaceholder', 'rcLink', 'rcError');

  const steps = document.querySelectorAll('.step');

function showStep(stepNum) {
    steps.forEach(step => {
        step.classList.remove('active');
        if (step.dataset.step === stepNum) {
            step.classList.add('active');
        }
    });
}

document.querySelectorAll('[data-target-step]').forEach(button => {
    button.addEventListener('click', () => {
        const step = button.dataset.targetStep;
        showStep(step);
    });
});