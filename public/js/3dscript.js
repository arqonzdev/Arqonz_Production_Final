document.addEventListener('DOMContentLoaded', function () {
    const viewContainer = document.getElementById('viewContainer');
    const showMoreBtn = document.getElementById('showMoreBtn');
    const showLessBtn = document.getElementById('showLessBtn');

    // Only proceed if the required elements exist
    if (!viewContainer) {
        return;
    }

    let views = [];
    let displayedViews = 6;
    const renderers = [];

    function create3DView(container, modelPath, name, link) {
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer();
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setClearColor(0xffffff); // Set background color to white
        container.appendChild(renderer.domElement);
        renderers.push(renderer);
        camera.position.z = 5;

        // Add lighting
        const ambientLight = new THREE.AmbientLight(0x404040); // Soft white light
        scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 1); // White directional light
        directionalLight.position.set(1, 1, 1).normalize();
        scene.add(directionalLight);

        // Load the GLTF model
        const loader = new THREE.GLTFLoader();
        loader.load(modelPath, (gltf) => {
            const model = gltf.scene;
            model.scale.set(0.5, 0.5, 0.5); // Scale the model
            model.position.set(0, -1, 0.5);
            scene.add(model);

            // Ensure all mesh materials are updated
            model.traverse((child) => {
                if (child.isMesh) {
                    child.material.needsUpdate = true;
                }
            });

            function animate() {
                requestAnimationFrame(animate);
                model.rotation.y += 0.01;
                renderer.render(scene, camera);
            }
            animate();
        }, undefined, (error) => {
            console.error('An error occurred while loading the model:', error);
        });

        container.addEventListener('click', (event) => {
            event.preventDefault();
            window.location.href = link;
        });
    }

    function cleanup() {
        renderers.forEach(renderer => {
            if (renderer && renderer.domElement) {
                renderer.domElement.remove();
                renderer.dispose();
            }
        });
        renderers.length = 0;
    }

    function showViews(count) {
        cleanup(); 
        viewContainer.innerHTML = ''; 
        for (let i = 0; i < count && i < views.length; i++) {
            const view = views[i];
            
            const wrapper = document.createElement('div');
            wrapper.className = 'view-wrapper';
            const viewInner = document.createElement('div');
            viewInner.className = 'view-container';
            wrapper.appendChild(viewInner);
            const nameTag = document.createElement('a');
            nameTag.className = 'name-tag';
            nameTag.href = view.link; 
            nameTag.textContent = view.name;
            wrapper.appendChild(nameTag);
            viewContainer.appendChild(wrapper);
            create3DView(viewInner, view.modelPath, view.name, view.link);
        }
    }

    views = [
        { modelPath: '/static/images/3DProducts/viewer/models/chair/scene.gltf', name: 'Cement Bag', link: `/static/images/3DProducts/viewer/index.html?model=${encodeURIComponent('models/chair/scene.gltf')}` },
        { modelPath: '/static/images/3DProducts/viewer/models/chair/scene.gltf', name: 'Basic Brick', link: `/static/images/3DProducts/viewer/index.html?model=${encodeURIComponent('models/chair/scene.gltf')}` },
        { modelPath: '/static/images/3DProducts/viewer/models/chair/scene.gltf', name: 'Chair', link: `/static/images/3DProducts/viewer/index.html?model=${encodeURIComponent('models/chair/scene.gltf')}` },
        { modelPath: '/static/images/3DProducts/viewer/models/robot/RobotExpressive.glb', name: 'Robot', link: `/static/images/3DProducts/viewer/index.html?model=${encodeURIComponent('models/robot/RobotExpressive.glb')}` },
        { modelPath: '/static/images/3DProducts/viewer/models/cushion/scene.gltf', name: 'Chair', link: `/static/images/3DProducts/viewer/index.html?model=${encodeURIComponent('models/cushion/scene.gltf')}` },
        { modelPath: '/static/images/3DProducts/viewer/models/chair/scene.gltf', name: 'Chair', link: `/static/images/3DProducts/viewer/index.html?model=${encodeURIComponent('models/chair/scene.gltf')}` },
        { modelPath: '/static/images/3DProducts/viewer/models/cushion/scene.gltf', name: 'Chair', link: `/static/images/3DProducts/viewer/index.html?model=${encodeURIComponent('models/cushion/scene.gltf')}` }
    ];
    showViews(displayedViews);
});