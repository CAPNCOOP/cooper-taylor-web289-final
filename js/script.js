'use strict';

// Hamburger Menu
document.addEventListener('DOMContentLoaded', function () {
  const hamburger = document.querySelector('.hamburger');
  const navLinks = document.querySelector('.nav-links');

  hamburger.addEventListener('click', function () {
    navLinks.classList.toggle('active');
  });
});

// Hero Text Animation
document.addEventListener('DOMContentLoaded', () => {
  const words = ['Fresh.', 'Local.', 'Sustainable.'];
  let currentWordIndex = 0;
  let currentCharIndex = 0;
  let isDeleting = false;
  const typingSpeed = 75;
  const deletingSpeed = 100;
  const delayBetweenWords = 1250;
  const textElement = document.getElementById('type-text');

  function type() {
    if (!textElement) return; // Prevents error if the element doesn't exist

    const currentWord = words[currentWordIndex];

    if (isDeleting) {
      currentCharIndex--;
      if (currentCharIndex < 0) {
        isDeleting = false;
        currentWordIndex = (currentWordIndex + 1) % words.length;
        setTimeout(type, typingSpeed);
        return;
      }
    } else {
      currentCharIndex++;
      if (currentCharIndex > currentWord.length) {
        isDeleting = true;
        setTimeout(type, delayBetweenWords);
        return;
      }
    }

    textElement.innerHTML = currentWord.substring(0, currentCharIndex);
    setTimeout(type, isDeleting ? deletingSpeed : typingSpeed);
  }

  if (textElement) setTimeout(type, typingSpeed);
});

// Slideshow
let slideIndex = 1;

document.addEventListener('DOMContentLoaded', () => {
  showSlides(slideIndex);
});

function plusSlides(n) {
  showSlides((slideIndex += n));
}

function currentSlide(n) {
  showSlides((slideIndex = n));
}

function showSlides(n) {
  let slides = document.getElementsByClassName('mySlides');
  let dots = document.getElementsByClassName('dot');

  if (slides.length === 0) return; // Prevents error if no slides exist

  if (n > slides.length) {
    slideIndex = 1;
  }
  if (n < 1) {
    slideIndex = slides.length;
  }

  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = 'none';
  }

  for (let i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(' active', '');
  }

  if (slides[slideIndex - 1]) {
    slides[slideIndex - 1].style.display = 'block';
  }

  if (dots[slideIndex - 1]) {
    dots[slideIndex - 1].className += ' active';
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
 * Converts message keys to readable text.
 */
function getMessageText(messageKey) {
  const messages = {
    favorite_added: '✅ Vendor added to favorites!',
    favorite_removed: '✅ Vendor removed from favorites!',
    error_invalid_vendor: '❌ Error: Invalid vendor selected.',
    error_not_logged_in: '❌ Error: You must be logged in to favorite a vendor.',
    error_add_failed: '❌ Error: Failed to add vendor to favorites.',
    error_remove_failed: '❌ Error: Failed to remove vendor from favorites.',
  };
  return messages[messageKey] || '❌ Unknown error occurred.';
}

// Notification Function
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
    }, 1000); // Delay for 3 seconds
  }
};

// image preview
function previewImage() {
  const input = document.getElementById('profile-pic');
  const preview = document.getElementById('image-preview');
  const file = input.files[0];

  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

// Toggle Admin Content
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

// FeedBack Popups
document.addEventListener('DOMContentLoaded', function () {
  let popup = document.querySelector('.feedback-popup');
  if (popup) {
    popup.style.display = 'block';
    setTimeout(() => {
      popup.style.opacity = '1'; // Fades in
    }, 10);
    setTimeout(() => {
      popup.style.opacity = '0'; // Fades out after 3 sec
      setTimeout(() => (popup.style.display = 'none'), 500);
    }, 3000);
  }
});
