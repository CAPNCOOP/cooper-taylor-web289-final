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

/*
 * The function `showSlides` is designed to display a specific slide and update corresponding
 * navigation dots in a slideshow.
 * @param n - The `n` parameter in the `showSlides` function represents the index of the slide that you
 * want to display. It is used to control which slide is currently shown in a slideshow.
 * @returns The `showSlides` function returns nothing (`undefined`) explicitly. It either executes the
 * code inside the function or returns early if there are no slides to prevent errors.
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

/*
 * The function `plusSlides` increments the `slideIndex` variable by `n` and then calls the
 * `showSlides` function.
 * @param n - The parameter `n` in the `plusSlides` function represents the number of slides you want
 * to move forward or backward by. Positive values of `n` will move the slides forward, while negative
 * values will move the slides backward.
 */
function plusSlides(n) {
  showSlides((slideIndex += n));
}

/*
 * The function `addSwipeListeners` enables swipe functionality for a slideshow by detecting touch
 * gestures and navigating between slides based on the swipe direction.
 *
 * @returns The `addSwipeListeners` function is returning nothing (undefined) because it does not have
 * a return statement. It is setting up event listeners for touch events on a slider element and
 * handling swipe gestures to navigate between slides in a slideshow.
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
   * The function `handleSwipe` determines the direction of a swipe gesture and navigates to the next or
   * previous slide based on the swipe distance.
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

/*
 * The function `getMessageText` returns a message based on a given message key, with defaulting to an
 * unknown error message if the key is not found.
 *
 * @param messageKey - The `messageKey` parameter is a string that represents a key used to look up a
 * specific message in the `messages` object. The function `getMessageText` takes this key as input and
 * returns the corresponding message text from the `messages` object. If the key does not match any
 * message in
 *
 * @returns The function `getMessageText` returns a message based on the `messageKey` provided. If the
 * `messageKey` matches one of the keys in the `messages` object, it returns the corresponding message.
 * If the `messageKey` does not match any key in the `messages` object, it returns '❌ Unknown error
 * occurred.'.
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
/*
 * The function `showNotification` displays a message with optional error styling and automatically
 * fades out after 2 seconds.
 *
 * @param message - The `message` parameter is the text content that you want to display in the
 * notification. It can be a success message, an error message, or any other informative message that
 * you want to show to the user.
 *
 * @param [isError=false] - The `isError` parameter in the `showNotification` function is a boolean
 * parameter that determines whether the notification should be styled as an error message (`true`) or
 * a success message (`false`). If `isError` is `true`, the notification will have the 'error' class
 * applied to it
 *
 * @returns The code snippet provided defines a function `showNotification` that displays a
 * notification message on a webpage. It also includes a window onload event listener that adds a
 * fade-out effect to a message element after a delay.
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
    }, 1000); // Delay for 3 seconds
  }
};

/*
 * The `previewImage` function allows users to preview an image before uploading it by dynamically
 * updating an image element with the selected file.
 *
 * @param event - The `event` parameter in the `previewImage` function is an event object that is
 * passed when the function is called. It contains information about the event that triggered the
 * function, such as the target element (input field in this case) and any additional data related to
 * the event.
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
 * The `toggleSection` function toggles the display of a section based on its header and stores the
 * state in the browser's localStorage.
 *
 * @param header - The `header` parameter in the `toggleSection` function is a reference to the HTML
 * element that serves as the header or title of a section. This function is designed to toggle the
 * visibility of the content section associated with this header element.
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

  /*
   * The function `handleSmartScroll` is used to control the visibility of a header element based on
   * the user's scrolling behavior on the webpage.
   *
   * @returns If the condition `Math.abs(lastScrollTop - currentScrollTop) <= scrollThreshold` is true,
   * then the function `handleSmartScroll` will return without making any changes to the header
   * element.
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
