document.addEventListener("DOMContentLoaded", function() {
    var showHideElements = document.querySelectorAll(".show-hide");
    
    showHideElements.forEach(function(showHideElement) {
        var passwordInput = showHideElement.parentElement.querySelector('input[type="password"], input[type="text"]');
        var showHideSpan = showHideElement.querySelector("span");
        
        if (!passwordInput || !showHideSpan) return;
        
        // Make sure the show-hide element is visible and clickable
        showHideElement.style.display = "block";
        showHideElement.style.zIndex = "10";
        showHideElement.style.cursor = "pointer";
        
        showHideSpan.classList.add("show");
        
        showHideSpan.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (showHideSpan.classList.contains("show")) {
                passwordInput.setAttribute("type", "text");
                showHideSpan.classList.remove("show");
            } else {
                passwordInput.setAttribute("type", "password");
                showHideSpan.classList.add("show");
            }
        });
    });
    
    // Handle form submission
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener("submit", function() {
            var showHideSpan = form.querySelector(".show-hide span");
            var passwordInput = form.querySelector('input[type="text"][name="password"], input[type="password"][name="password"]');
            
            if (showHideSpan && passwordInput) {
                showHideSpan.classList.add("show");
                passwordInput.setAttribute("type", "password");
            }
        });
    });
  });