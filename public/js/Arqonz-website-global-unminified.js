// Website navigation Dropdown Script

document.addEventListener('DOMContentLoaded', () => {
    const parentMenus = document.querySelectorAll('.Parent-Menu > li');
    const menuOpen = document.getElementById('menuopen');
    const menuClose = document.getElementById('menuclose');
    const dashNavMain = document.querySelector('.Parent-Menu');
    const mobileMenus = document.querySelectorAll('.Parent-Menu > li > a');

    // Desktop navigation dropdowns on hover
    parentMenus.forEach(menu => {
        menu.addEventListener('mouseenter', () => {
            const childrenMenu = menu.querySelector('.Children-Menu');
            if (childrenMenu) {
                childrenMenu.style.display = 'block';
            }
        });

        menu.addEventListener('mouseleave', () => {
            const childrenMenu = menu.querySelector('.Children-Menu');
            if (childrenMenu) {
                childrenMenu.style.display = 'none';
            }
        });
    });

    // Menu open and close for mobile
    menuOpen.addEventListener('click', () => {
        dashNavMain.style.display = 'flex';
        menuOpen.style.display = 'none';
        menuClose.style.display = 'block';
    });

    menuClose.addEventListener('click', () => {
        dashNavMain.style.display = 'none';
        menuOpen.style.display = 'block';
        menuClose.style.display = 'none';
    });

    // Mobile menu click to open submenus
    mobileMenus.forEach(menu => {
        menu.addEventListener('click', function(event) {
            const parentMenu = this.parentElement;
            const childrenMenu = parentMenu.querySelector('.Children-Menu');
            
            if (childrenMenu) {
                event.preventDefault(); // Prevent link navigation
                const isActive = parentMenu.classList.contains('active');
                
                // Close any open sub-menus
                document.querySelectorAll('.Parent-Menu > li.active').forEach(item => {
                    item.classList.remove('active');
                    item.querySelector('.Children-Menu').style.display = 'none';
                });

                // Toggle the submenu of clicked menu
                if (!isActive) {
                    parentMenu.classList.add('active');
                    childrenMenu.style.display = 'block';
                }
            }
        });
    });
});

// Dropdown Js Ends

// Notifications DropDown

document.addEventListener('DOMContentLoaded', () => {
    const notifIcon = document.querySelector('.notif-icon');
    const notifDropdown = document.querySelector('.notif-dropdown');

    // Toggle the visibility of the notification dropdown
    notifIcon.addEventListener('click', (event) => {
        event.stopPropagation(); // Prevent the click event from bubbling up to the document
        notifDropdown.classList.toggle('show');
    });

    // Close the dropdown if clicking outside
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.notification-item')) {
            notifDropdown.classList.remove('show');
        }
    });

    // Add click event handler to notification links
    document.querySelectorAll('.notif-link').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var notifId = this.getAttribute('data-notif-id');
            markNotifAsRead(notifId, this.href); // Pass the URL to navigate to
        });
    });

    // Function to mark notification as read via AJAX
    function markNotifAsRead(notifId, url) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/mark-notification-as-read/' + notifId);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                // After marking as read, navigate to the URL
                window.location.href = url;
            }
        };
        xhr.send();
    }
});

// Notifications DropDown Ends
