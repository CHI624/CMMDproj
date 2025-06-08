document.addEventListener('DOMContentLoaded', function() {
    
    const scrollArrow = document.querySelector('.scroll-down-arrow');

    if (scrollArrow) {
        scrollArrow.addEventListener('click', function(event) {
            event.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
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
  }, 3000); 
});

document.querySelector("form").addEventListener("submit", function (e) {
  let hasError = false;

  document.querySelectorAll(".error").forEach(el => el.classList.remove("error"));

  this.querySelectorAll("input[type='text']").forEach(input => {
    if (!input.value.trim()) {
      input.classList.add("error");
      hasError = true;
    }
  });
  const radioGroups = ["C2_PER_COM", "C2_OPER_EX"];
  radioGroups.forEach(id => {
    const group = document.getElementById(id);
    const inputs = group.querySelectorAll("input[type='radio']");
    const isChecked = [...inputs].some(input => input.checked);
    if (!isChecked) {
      group.classList.add("error");
      hasError = true;
    }
  });

  if (hasError) {
    e.preventDefault();
  }
});
