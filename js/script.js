'use strict';

// Header Menu
document.addEventListener('DOMContentLoaded', function () {
  const menu = document.getElementById('menu');
  const navLinks = document.querySelector('.nav-links');

  menu.addEventListener('click', function () {
    navLinks.classList.toggle('active');
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

// Function to show the correct slide
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

// Function to change slides
function plusSlides(n) {
  showSlides((slideIndex += n));
}

// Function to add swipe support
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

// Add Swipe Gesture Support
function addSwipeListeners() {
  const slider = document.querySelector('.mySlides').parentElement; // Get slideshow container
  let touchStartX = 0;
  let touchEndX = 0;

  slider.addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].clientX;
  });

  slider.addEventListener('touchend', e => {
    touchEndX = e.changedTouches[0].clientX;
    handleSwipe();
  });

  function handleSwipe() {
    const swipeDistance = touchStartX - touchEndX;
    const minSwipeDistance = 50; // Adjust threshold for sensitivity

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
 * Converts message keys to readable text.
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
function previewImage(event) {
  const input = event.target; // Get the input field that triggered the event
  const previewId = input.getAttribute('data-preview'); // Get the ID of the preview element from a custom attribute
  const preview = document.getElementById(previewId);
  const file = input.files[0];

  if (file && preview) {
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

// Smart Scroll Header
document.addEventListener('DOMContentLoaded', function () {
  const header = document.querySelector('header');
  let lastScrollTop = 0;
  const scrollThreshold = 5; // Minimum scroll amount to trigger header show/hide

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
