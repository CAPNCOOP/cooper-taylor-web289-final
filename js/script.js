'use strict';

// Hero Text Animation
document.addEventListener('DOMContentLoaded', () => {
  const words = ['Fresh.', 'Local.', 'Sustainable.'];
  let currentWordIndex = 0;
  let currentCharIndex = 0;
  let isDeleting = false;
  const typingSpeed = 75;
  const deletingSpeed = 100;
  const delayBetweenWords = 1250;
  const textElement = document.getElementById('text');

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
            showNotification(data.message);
            // Toggle button style based on action
            if (data.message.includes('removed')) {
              buttonElement.textContent = '♡'; // Unfavorite icon
            } else {
              buttonElement.textContent = '❤️'; // Favorited icon
            }
          } else {
            showNotification(data.message, true);
          }
        })
        .catch(error => console.error('Error:', error));
    });
  });
});

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
