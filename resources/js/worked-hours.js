document.addEventListener('DOMContentLoaded', function() {
    // Create custom locale with Monday as first day
    if (typeof flatpickr !== 'undefined') {
        flatpickr.localize({
            firstDayOfWeek: 1 // Monday
        });

        // Initialize Flatpickr for all date inputs with Monday as first day
        const dateConfig = {
            dateFormat: "Y-m-d",
            locale: {
                firstDayOfWeek: 1 // Monday
            }
        };

        flatpickr("#date", dateConfig);
        flatpickr("#start_date", dateConfig);
        flatpickr("#end_date", dateConfig);
    }

    // Handle collapse/expand icon rotation
    const collapseElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
    collapseElements.forEach(function(element) {
        const targetId = element.getAttribute('data-bs-target');
        const iconId = element.querySelector('.collapse-icon')?.id;
        
        if (iconId && targetId) {
            const targetElement = document.querySelector(targetId);
            const icon = document.getElementById(iconId);
            
            if (targetElement && icon) {
                // Set initial state
                if (targetElement.classList.contains('show')) {
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                } else {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-up');
                }
                
                // Listen for collapse events
                targetElement.addEventListener('show.bs.collapse', function() {
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                });
                
                targetElement.addEventListener('hide.bs.collapse', function() {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-up');
                });
            }
        }
    });
});

