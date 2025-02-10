'use strict';

// Hero Text
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

  setTimeout(type, typingSpeed);
});

// slideshow

let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides((slideIndex += n));
}

function currentSlide(n) {
  showSlides((slideIndex = n));
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName('mySlides');
  let dots = document.getElementsByClassName('dot');
  if (n > slides.length) {
    slideIndex = 1;
  }
  if (n < 1) {
    slideIndex = slides.length;
  }
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = 'none';
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(' active', '');
  }
  slides[slideIndex - 1].style.display = 'block';
  dots[slideIndex - 1].className += ' active';
}
