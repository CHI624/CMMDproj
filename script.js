document.addEventListener('DOMContentLoaded', function() {
    
    const scrollArrow = document.querySelector('.scroll-down-arrow');

    if (scrollArrow) {
        scrollArrow.addEventListener('click', function(event) {
            // Prevent the default anchor link behavior (the sudden jump)
            event.preventDefault();

            // Get the target element's ID from the href attribute
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                // Use the modern, built-in smooth scrolling API
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    }

});
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    document.getElementById("welcome-screen").style.display = "none";
    document.getElementById("main-content").style.display = "block";
  }, 3000); // Matches the 3s animation duration
});
