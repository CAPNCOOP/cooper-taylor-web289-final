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

// Vendor EIN Toggle
document.addEventListener('DOMContentLoaded', () => {
  const yesRadio = document.getElementById('yes');
  const noRadio = document.getElementById('no');
  const einFieldset = document.querySelector('.ein-field');

  if (yesRadio && noRadio && einFieldset) {
    yesRadio.addEventListener('change', function () {
      einFieldset.style.display = 'block';
    });

    noRadio.addEventListener('change', function () {
      einFieldset.style.display = 'none';
    });
  }
});
