document.addEventListener("DOMContentLoaded", function() {
    // Create a GSAP timeline
    const tl = gsap.timeline();

    // Animate the progress bar
    tl.to('.progress-bar-inner', {
    duration: 3,
    width: '100%',
    ease: 'power2.inOut'
    });

    // Animate the loading text
    tl.to('.loading-text', {
    duration: 3,
    innerText: '100%',
    ease: 'power2.inOut',
    snap: {
        innerText: 1
    }
    }, '-=3');

    // Add a delay before hiding the loading page
    tl.delay(1);

    // Hide the loading page
    tl.to('.loading-container', {
    duration: 1,
    y: "-100%",
    ease: 'power2.inOut',
    });
    // LOADING ANIMATION COMPLETE

    // Generate array of image URLs
    const images = [];
    for (let i = 0; i <= 81; i++) {
        images.push(`http://arqonz.in/static/images/desktop/Desktop/ProtohomesAssembling${i.toString().padStart(2, '0')}.jpg`);
    }

    let loadedImages = 0;
    images.forEach(function(imgSrc) {
        const img = new Image();
        img.onload = function() {
            loadedImages++;
            if (loadedImages === images.length) {
                init();
            }
        };
        img.src = imgSrc;
    });

    function init() {
        const canvas = document.getElementById('canvasA');
        const ctx = canvas.getContext('2d');

        // Set canvas size to match the image size
        const imageWidth = canvas.width = window.innerWidth;
        const imageHeight = canvas.height = window.innerHeight;
        
        function drawImage(index) {
            const img = new Image();
            img.onload = function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, imageWidth, imageHeight);
            };
            img.src = images[index];
        }

        // Initial draw
        drawImage(0);

        // Define GSAP animation timeline for canvasA
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: '#main',
                start: 'top top',
                end: 'bottom bottom',
                scrub: true, // Scrub makes the animation smooth
                pin: true,
                
                stagger: true,
                onUpdate: (self) => {
                    if (self.progress === 1) {
                        tl.pause(); // Pause animation when completed
                    }
                }
            }
        });
        // tl.from("#uppertext", {
        //     y: "50px", // Adjust as needed
        //     opacity: 0,
        //     duration: 1, // Adjust duration as needed
        //     ease: "power1.inOut" // Adjust ease as needed
        // });

        // Add animation sequence for canvasA
        images.forEach((imgSrc, index) => {
            tl.to({}, {
                duration: 1, // Adjust duration as needed
                onStart: () => {
                    drawImage(index);
                },
                onReverseComplete: () => {
                    drawImage(index - 1);
                }
            })
        });
    }
    
});
