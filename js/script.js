'use strict';

// Header Menu
document.addEventListener('DOMContentLoaded', function () {
  const menu = document.getElementById('menu');
  const navLinks = document.querySelector('.nav-links');
  let timeoutId;

  menu.addEventListener('click', function () {
    navLinks.classList.add('active');

    // Start a 3-second countdown to remove 'active'
    timeoutId = setTimeout(() => {
      if (!navLinks.matches(':hover')) {
        navLinks.classList.remove('active');
      }
    }, 3000);
  });

  // If they hover over the menu, cancel the timeout
  navLinks.addEventListener('mouseenter', () => {
    clearTimeout(timeoutId);
  });

  // If they leave the menu after clicking, start a new 3s countdown
  navLinks.addEventListener('mouseleave', () => {
    timeoutId = setTimeout(() => {
      navLinks.classList.remove('active');
    }, 3000);
  });
});

// Cropper Handler
document.addEventListener('DOMContentLoaded', function () {
  let cropper;

  const fileInput = document.querySelector('input[type="file"]');
  const cropperImage = document.getElementById('cropper-image');
  const cropperModal = document.getElementById('cropper-modal');
  const confirmBtn = document.getElementById('crop-confirm');

  // Detect context (user or product)
  let previewTargetId = '';
  let hiddenInputId = '';

  if (fileInput?.id === 'user-profile-pic') {
    previewTargetId = 'profile-preview';
    hiddenInputId = 'cropped-image';
  } else if (fileInput?.id === 'product-image') {
    previewTargetId = 'product-preview';
    hiddenInputId = 'cropped-product';
  } else {
    console.warn('No recognized file input present.');
    return;
  }

  const previewImage = document.getElementById(previewTargetId);
  const hiddenInput = document.getElementById(hiddenInputId);

  if (!fileInput || !previewImage || !hiddenInput) {
    console.warn('Missing key cropper elements.');
    return;
  }

  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      cropperImage.src = e.target.result;
      cropperModal.style.display = 'flex';

      if (cropper) cropper.destroy();
      cropper = new Cropper(cropperImage, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 1,
      });

      // Preview original before crop
      previewImage.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });

  confirmBtn.addEventListener('click', function () {
    cropper.getCroppedCanvas({ width: 500, height: 500 }).toBlob(function (blob) {
      const reader = new FileReader();
      reader.onloadend = function () {
        hiddenInput.value = reader.result;
        cropperModal.style.display = 'none';

        // Update preview with cropped result
        previewImage.src = reader.result;
      };
      reader.readAsDataURL(blob);
    });
  });
});

// Back to top Button
const backToTopBtn = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
  if (window.pageYOffset > 300) {
    backToTopBtn.classList.add('show');
  } else {
    backToTopBtn.classList.remove('show');
  }
});

backToTopBtn.addEventListener('click', () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  });
});

// Slideshow

document.addEventListener('DOMContentLoaded', () => {
  showSlides(slideIndex);
  addSwipeListeners(); // Ensure swipe functionality is active
});

let slideIndex = 1;

/**
 * Displays the slide at index `n` and updates the active dot.
 *
 * @param {number} n - The index of the slide to display.
 */
function showSlides(n) {
  let slides = document.getElementsByClassName('mySlides');
  let dots = document.getElementsByClassName('dot');

  if (slides.length === 0) return; // Prevents errors if no slides exist

  if (n > slides.length) slideIndex = 1;
  if (n < 1) slideIndex = slides.length;

  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = 'none';
  }
  for (let i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(' active', '');
  }

  slides[slideIndex - 1].style.display = 'block';
  dots[slideIndex - 1].className += ' active';
}

/**
 * Changes the current slide by a given offset.
 *
 * @param {number} n - The number of slides to move (positive or negative).
 */
function plusSlides(n) {
  showSlides((slideIndex += n));
}

/**
 * Enables swipe gestures on the slideshow container for slide navigation.
 */
function addSwipeListeners() {
  const slider = document.querySelector('.slideshow-container'); // Adjust based on your slideshow container
  let touchStartX = 0;
  let touchEndX = 0;

  if (!slider) {
    console.error('Swipe listener failed: .slideshow-container not found.');
    return;
  }

  slider.addEventListener('touchstart', e => {
    touchStartX = e.touches[0].clientX; // Store the starting X position
  });

  slider.addEventListener('touchmove', e => {
    touchEndX = e.touches[0].clientX; // Update the X position as the finger moves
  });

  slider.addEventListener('touchend', () => {
    handleSwipe();
  });

  /**
   * Determines swipe direction and changes the slide accordingly.
   */
  function handleSwipe() {
    const swipeDistance = touchStartX - touchEndX;
    const minSwipeDistance = 50; // Adjust this threshold if needed

    if (swipeDistance > minSwipeDistance) {
      plusSlides(1); // Swipe left → Next Slide
    } else if (swipeDistance < -minSwipeDistance) {
      plusSlides(-1); // Swipe right → Previous Slide
    }
  }
}

// sign-up pop-up
document.addEventListener('DOMContentLoaded', function () {
  let signupBtn = document.getElementById('openSignup');
  let closeBtn = document.getElementById('closeSignup');
  let signupPopup = document.getElementById('signup-popup');

  if (signupBtn && signupPopup) {
    signupBtn.addEventListener('click', function (event) {
      event.preventDefault(); // Prevent any default link behavior
      signupPopup.classList.remove('hidden');
    });
  }

  if (closeBtn && signupPopup) {
    closeBtn.addEventListener('click', function () {
      signupPopup.classList.add('hidden');
    });
  }
});

// favorite button
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.favorite-btn').forEach(button => {
    button.addEventListener('click', function () {
      let vendorId = this.dataset.vendorId;
      let buttonElement = this; // Store reference to button

      fetch('favorite_vendor.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `vendor_id=${vendorId}`,
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification(getMessageText(data.message)); // Convert message key to readable text

            // Toggle button style based on action
            if (data.message === 'favorite_removed') {
              buttonElement.textContent = '♡'; // Unfavorite icon
            } else if (data.message === 'favorite_added') {
              buttonElement.textContent = '❤️'; // Favorited icon
            }
          } else {
            showNotification(getMessageText(data.message), true);
          }
        })
        .catch(error => console.error('Error:', error));
    });
  });
});

/**
 * Returns a human-readable message based on a given key.
 *
 * @param {string} messageKey - The key identifying the message.
 * @returns {string} The corresponding message text or a default error message.
 */
function getMessageText(messageKey) {
  const messages = {
    favorite_added: '✅ Vendor added to favorites!',
    favorite_removed: '❌ Vendor removed from favorites!',
    error_invalid_vendor: '❌ Error: Invalid vendor selected.',
    error_not_logged_in: '❌ Error: You must be logged in to favorite a vendor.',
    error_add_failed: '❌ Error: Failed to add vendor to favorites.',
    error_remove_failed: '❌ Error: Failed to remove vendor from favorites.',
  };
  return messages[messageKey] || '❌ Unknown error occurred.';
}

/**
 * Displays a notification message with optional error styling, then fades out.
 *
 * @param {string} message - The message text to display.
 * @param {boolean} [isError=false] - Whether the message should be styled as an error.
 */
function showNotification(message, isError = false) {
  let notification = document.getElementById('notification');
  if (!notification) {
    console.error('❌ Notification element NOT found!');
    return;
  }

  notification.textContent = message;
  notification.classList.remove('hidden');
  notification.classList.add(isError ? 'error' : 'success');
  notification.style.display = 'block'; // Ensure it appears

  setTimeout(() => {
    notification.style.opacity = '0';
    setTimeout(() => {
      notification.style.display = 'none'; // Hide after fade out
      notification.style.opacity = '1';
    }, 500);
  }, 2000);
}

// Function to fade out the removed vendor message after 3 seconds
window.onload = function () {
  let message = document.querySelector('.message');
  if (message) {
    setTimeout(function () {
      message.classList.add('fade-out'); // Add fade-out class to the message
    }, 1000); // Delay for 1 second
  }
};

/**
 * Previews an image file selected in a file input.
 *
 * @param {Event} event - The file input change event.
 */
function previewImage(event) {
  const input = event.target;
  const previewClass = input.getAttribute('data-preview') || 'image-preview';
  const preview = input.closest('fieldset')?.querySelector(`.${previewClass}`);

  if (input.files?.[0] && preview) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.setAttribute('data-state', 'updated');
    };
    reader.readAsDataURL(input.files[0]);
  }
}

/**
 * Toggles visibility of a section in the admin/ superadmin dash and saves its state in localStorage.
 *
 * @param {HTMLElement} header - The section header element that was clicked.
 */
function toggleSection(header) {
  let content = header.nextElementSibling;
  let sectionId = header.dataset.section; // Unique ID for each section

  // Toggle display
  if (content.style.display === 'none' || content.style.display === '') {
    content.style.display = 'block';
    localStorage.setItem(sectionId, 'open'); // Store state
  } else {
    content.style.display = 'none';
    localStorage.setItem(sectionId, 'closed'); // Store state
  }
}

// Restore saved state on page load
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.section-header').forEach(header => {
    let content = header.nextElementSibling;
    let sectionId = header.dataset.section; // Unique section identifier
    let savedState = localStorage.getItem(sectionId);

    if (savedState === 'open') {
      content.style.display = 'block';
    } else {
      content.style.display = 'none'; // Default to collapsed
    }
  });
});

// Popup Message Fade
// document.addEventListener('DOMContentLoaded', function () {
//   const popup = document.querySelector('.popup-message');
//   if (popup) {
//     popup.classList.add('show');
//     setTimeout(() => {
//       popup.classList.remove('show');
//       setTimeout(() => {
//         popup.style.display = 'none';
//       }, 500);
//     }, 3000);
//   }
// });

// Smart Scroll Header
document.addEventListener('DOMContentLoaded', function () {
  const header = document.querySelector('header');
  let lastScrollTop = 0;
  const scrollThreshold = 5; // Minimum scroll amount to trigger header show/hide

  /**
   * Hides or shows the header based on scroll direction and distance.
   */
  function handleSmartScroll() {
    const currentScrollTop = window.scrollY || document.documentElement.scrollTop;

    // Make sure enough scrolling has occurred
    if (Math.abs(lastScrollTop - currentScrollTop) <= scrollThreshold) {
      return;
    }

    // Scrolling down AND not at the very top of the page
    if (currentScrollTop > lastScrollTop && currentScrollTop > 100) {
      header.classList.add('header-hidden');
    }
    // Scrolling up
    else {
      header.classList.remove('header-hidden');
    }

    lastScrollTop = currentScrollTop;
  }

  // Listen for scroll events with throttling for better performance
  let scrollTimeout;
  window.addEventListener('scroll', function () {
    if (!scrollTimeout) {
      scrollTimeout = setTimeout(function () {
        handleSmartScroll();
        scrollTimeout = null;
      }, 10); // Small timeout for performance
    }
  });
});
