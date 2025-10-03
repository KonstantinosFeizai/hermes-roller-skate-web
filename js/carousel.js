const track = document.querySelector('.carousel-track');
const wrapper = track?.parentElement;
const speed = 0.5; // pixels/frame

// 1) Prefill: cloning until track is at least 2x wrapper width
function prefillCarousel() {
    if (!track || !wrapper) return;
    const items = Array.from(track.children);
    if (items.length === 0) return;

    // calculate total width of track children
    const trackWidth = () => Array.from(track.children).reduce((sum, el) => {
        const rect = el.getBoundingClientRect();
        const style = window.getComputedStyle(el);
        const mr = parseFloat(style.marginRight) || 0;
        return sum + rect.width + mr;
    }, 0);

    const wrapperWidth = wrapper.getBoundingClientRect().width;
    while (trackWidth() < wrapperWidth * 2) {
        for (const it of items) {
            track.appendChild(it.cloneNode(true));
            if (trackWidth() >= wrapperWidth * 2) break;
        }
    }
}

function animateCarousel() {
    const style = window.getComputedStyle(track);
    const matrix = new DOMMatrixReadOnly(style.transform);
    let translateX = matrix.m41 || 0;

    translateX -= speed;
    track.style.transform = `translateX(${translateX}px)`;

    const firstItem = track.firstElementChild;
    const firstRect = firstItem.getBoundingClientRect();
    const wrapperRect = wrapper.getBoundingClientRect();

    if (firstRect.right < wrapperRect.left) {
        // move first item to end
        track.appendChild(firstItem);
        const itemStyle = window.getComputedStyle(firstItem);
        const marginRight = parseFloat(itemStyle.marginRight) || 0;
        translateX += firstRect.width + marginRight;
        track.style.transform = `translateX(${translateX}px)`;
    }
    requestAnimationFrame(animateCarousel);
}

prefillCarousel();
requestAnimationFrame(animateCarousel);
