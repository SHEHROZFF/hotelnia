// ----------------------------Hotel Booking listing Carousel ---------------------------------------------------------

var swiper = new Swiper(".mySwiper", {
  slidesPerView: 4,
  spaceBetween: 30,
  loop: true,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
});


var swiper = new Swiper(".mySwiper-testimonial", {
  slidesPerView: 2,
  spaceBetween: 30,
  loop: true,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
});


document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Price range slider
  const rangeInput = document.querySelector('.form-range');
  if (rangeInput) {
      rangeInput.addEventListener('input', function() {
          const maxPriceInput = document.querySelectorAll('input[type="number"]')[1];
          maxPriceInput.value = this.value;
      });
  }

  // Handle hotel card hover effects
  const hotelCards = document.querySelectorAll('.hotel-card');
  hotelCards.forEach(card => {
      card.addEventListener('mouseenter', function() {
          this.classList.add('shadow-lg');
      });
      
      card.addEventListener('mouseleave', function() {
          this.classList.remove('shadow-lg');
      });
  });


  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
          e.preventDefault();
          
          const targetId = this.getAttribute('href');
          if (targetId === '#') return;
          
          const targetElement = document.querySelector(targetId);
          if (targetElement) {
              targetElement.scrollIntoView({
                  behavior: 'smooth'
              });
          }
      });
  });

  // Simulate loading state for View Details buttons
  const viewDetailsButtons = document.querySelectorAll('.btn-outline-primary');
  viewDetailsButtons.forEach(button => {
      button.addEventListener('click', function(e) {
          e.preventDefault();
          
          const originalText = this.textContent;
          this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
          this.disabled = true;
          
          // Simulate API call
          setTimeout(() => {
              this.innerHTML = originalText;
              this.disabled = false;
              
              // Show a toast notification (would require additional implementation)
              console.log('Hotel details loaded');
          }, 1500);
      });
  });
});

